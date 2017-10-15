<?php 
/**
 * InlineCMS v1.0.1
 * Copyright 2016, InstantSoft
 *
 * @author Vladimir E. Obukhov
 * @package InlineCMS
 * @link http://inlinecms.com
 * @license http://inlinecms.com/license
 */

namespace InlineCMS\Modules\Pages;

use InlineCMS\Core\Core;
use InlineCMS\Core\Module;
use InlineCMS\Core\Page;
use InlineCMS\Core\Request;
use InlineCMS\Core\Response;
use InlineCMS\Core\Lang;
use InlineCMS\Core\Layout;
use InlineCMS\Core\User;
use InlineCMS\Helpers\Strings;
use InlineCMS\Helpers\Files;

class Pages extends Module {

	public function _before() {
		parent::_before();
		if (!User::isLogged()) { Core::errorAuth(); }
	}

	public function loadPageJson(){

		$pageUri = Request::get('page_uri');
		$lang = Request::get('lang');

		$page = new Page($lang, $pageUri);

		if (!$page->isExists()) { exit; }

		$page->load();

		Response::json($page->getJson());
		Response::send();

	}

	public function savePage(){

		$pageUri = Request::get('page_uri');
		$lang = Request::get('lang');
		$widgetsJson = Request::get('widgets');
		$widgetsCount = Request::get('count');
        $collectionsJson = Request::get('collections');

        try {

            $page = new Page($lang, $pageUri);

            if (!$page->isExists()) { exit; }

            $page->load();

            $widgets = json_decode($widgetsJson, true);
            $collections = json_decode($collectionsJson, true);

            $page->setWidgets($widgets, $widgetsCount);
            $page->setCollections($collections);

            $page->save();

        } catch (\Exception $e){
            Response::sendJson(array(
                'success' => false,
                'error' => $e->getMessage()
            ));
        }

		$page->deleteCache();

		Response::sendJson(array('success'=>true));

	}

	public function getPagesTree(){

		$lang = Request::get('lang');

		$pagesTreeJson = Core::getPagesTree($lang, false);

		Response::json($pagesTreeJson);
		Response::send();

	}

	public function loadPageForm(){

        $mode = Request::get('mode');

		Response::json(array(
			'html' => Layout::renderTemplate('editor/page', array(
                'mode' => $mode
            ))
		));

		Response::send();

	}

	public function createPage(){

		$title = Request::get('title');
		$lang = Request::get('lang');
		$uri = trim(Request::get('uri'), '/');
		$layout = Request::get('layout');
        $keywords = Request::get('keywords');
        $description = Request::get('description');
        $mode = Request::get('mode');

        $isCreateClear = $mode == 'clear';

        try {

            $page = new Page($lang, $uri);

            $page->setLayoutName($layout);
            $page->setTitle($title);
            $page->setMeta('keywords', $keywords);
            $page->setMeta('description', $description);

            $page->create($isCreateClear);

            if ($mode == 'copy'){

                $sourceUri = Request::get('source_uri');
                $sourceLang = Request::get('source_lang');
                $sourcePage = new Page($sourceLang, $sourceUri);

                if ($sourcePage->isExists()){
                    $sourcePage->load();
                    $page->setWidgets($sourcePage->getWidgets());
                    $page->save();
                }

            }

            Core::buildPagesTree($lang);

        } catch (\Exception $e){
            Response::sendJson(array(
                'success' => false,
                'error' => $e->getMessage()
            ));
        }

		Response::sendJson(array(
			'success' => true
		));

	}

    public function editPage(){

        $title = Request::get('title');
        $keywords = Request::get('keywords');
        $description = Request::get('description');
		$lang = Request::get('lang');

        $currentUri = Request::get('current_uri');
        $newUri = Request::get('uri');

        if ($currentUri != '/') { $currentUri = trim($currentUri, '/'); }
        if ($newUri != '/') { $newUri = trim($newUri, '/'); }

        try{

            $page = new Page($lang, $currentUri);

            $page->load();

            $page->setTitle($title);
            $page->setMeta('keywords', $keywords);
            $page->setMeta('description', $description);

            if ($newUri != $currentUri){
                $page->moveTo($newUri);
                Core::buildPagesTree($lang);
            }

            $page->save();

        } catch (\Exception $e){
            Response::sendJson(array(
                'success' => false,
                'error' => $e->getMessage()
            ));
        }

        Response::sendJson(array(
			'success' => true,
		));

    }

    public function deletePage(){

        $uri = mb_strtolower(trim(Request::get('uri'), '/'));
        $lang = Request::get('lang');
        $isPage = Request::get('is_page');

        try {

            if (!$isPage){

                $folderDir = Core::path('data', 'content', $lang, 'pages', str_replace('/', DIRECTORY_SEPARATOR, $uri));

                Files::removeDirectory($folderDir);

                Core::buildPagesTree($lang);

                Response::json(array(
                    'success' => true,
                    'is_folder' => true
                ));

                Response::send();

            }

            $page = new Page($lang, $uri);

            $page->delete();
            Core::buildPagesTree($lang);

        } catch (\Exception $e){
            Response::sendJson(array(
                'success' => false,
                'error' => $e->getMessage()
            ));
        }

        Response::sendJson(array(
			'success' => true,
		));

    }

	public function validatePage(){

		$isNewPage = Request::get('is_new');
		$title = Request::get('title');
		$lang = Request::get('lang');

        $uri = Request::get('uri');
        $currentUri = Request::get('current_uri');

        $reservedUris = array(
            'app', 'data', 'static', 'theme', 'upload', 'css', 'js'
        );

        $errors = array();

		if (!$title){
            $errors[] = Lang::get('pageTitleError');
		}

        if ($uri != '/'){ $uri = mb_strtolower(trim($uri, '/')); }
        if ($currentUri != '/'){ $currentUri = mb_strtolower(trim($currentUri, '/')); }

		if (!$uri){
            $errors[] = Lang::get('pageUrlError') . ': ' . Request::get('current_uri');
		}

        if ($uri && !preg_match('/^([a-z0-9\.\-\/]+)$/i', $uri)){
            $errors[] = Lang::get('pageUrlBadInput');
        }

        if ($isNewPage || ($currentUri != $uri)){
            $testPage = new Page($lang, $uri);
            if ($testPage->isExists()){
                $errors[] = Lang::get('pageUrlExists');
            }
        }

        if ($uri){
            foreach ($reservedUris as $reservedUri){
                if ($uri == $reservedUri || Strings::startsWith($uri, $reservedUri.'/')){
                    $errors[] = Lang::get('pageUrlReserved', $reservedUri);
                    break;
                }
            }
        }

        if ($errors && count($errors) == 1){
            $errors = $errors[0];
        }

		Response::sendJson(array(
			'success' => !$errors,
            'error' => $errors
		));

	}

}
