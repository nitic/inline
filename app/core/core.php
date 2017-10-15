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

namespace InlineCMS\Core;

use InlineCMS\Core\Config;
use InlineCMS\Core\Layout;
use InlineCMS\Core\Page;
use InlineCMS\Core\Response;
use InlineCMS\Core\User;
use InlineCMS\Helpers\Files;

define('PRODUCT_TITLE', 'InlineCMS');
define('PRODUCT_ATTRIBUTION', true);

class Core {

	private static $currentLang;

    public static function getVersion(){
        return '1.0.0';
    }

    public static function initializeLayout($layoutFile){

        $langs = Config::get('langs');
		$menus = self::getMenus();
		$globalRegions = Layout::getGlobalRegions();

        $scheme = Layout::createLayoutScheme($layoutFile);

        foreach($langs as $lang){

            if (!empty($scheme['menus'])){
                foreach($scheme['menus'] as $menu){
                    if (isset($menus[$lang][$menu['id']])) { continue; }
                    $menus[$lang][$menu['id']] = array();
                }
            }

            if (!empty($scheme['globals'])){
                foreach($scheme['globals'] as $regionId => $region){

                    if (isset($globalRegions[$lang][$regionId])){ continue; }

                    if ($region['is_collection']){

                        $globalRegions[$lang][$regionId] = $region['content'];

                    } else {

                        $globalRegions[$lang][$regionId][] = array(
                            'id' => 0,
                            'handler' => 'text',
                            'options' => array(),
                            'content' => $region['content']
                        );

                    }

                }

            }

        }

        unset($scheme['globals']);

        self::saveMenus($menus);
        Layout::saveGlobalRegions($globalRegions);

        $dom = $scheme['dom']; unset($scheme['dom']);

        $schemeFilePath = self::path('data', 'layouts', "{$layoutFile}.scheme");
        $jsonFilePath = self::path('data', 'layouts', "{$layoutFile}.json");

        file_put_contents($schemeFilePath, $dom);
        file_put_contents($jsonFilePath, json_encode($scheme, JSON_HEX_AMP));

    }

    public static function initializeSite(){

        $layoutFiles = self::getLayoutsList();

        foreach($layoutFiles as $layoutFile => $layoutFileDescription){

            $schemeFilePath = self::path('data', 'layouts', "{$layoutFile}.scheme");

            if (file_exists($schemeFilePath)){ continue; }

            self::initializeLayout($layoutFile);

        }

        $langs = Config::get('langs');
        $menus = self::getMenus();

		foreach($langs as $lang){

            $page = new Page($lang, '/');

            $page->
                setLayoutName(Config::get('index_layout'))->
                setTitle(Config::get('site_title'))->
                setMeta('description', Config::get('site_description'))->
                create();

            foreach($menus[$lang] as $menuId=>$menu){
                self::createMenuItem($lang, $menuId, array(
                    'type' => 'page',
                    'title' => Lang::get('homePage'),
                    'url' => 'index',
                    'target' => '_self'
                ));
            }

            self::buildPagesTree($lang);

		}

    }

	public static function createMenuItem($lang, $menuId, $item){

		$menus = self::getMenus($lang);

		if (!isset($menus[$menuId])){
			$menus[$menuId] = array();
		}

		$menus[$menuId][] = $item;

		self::saveMenus($menus, $lang);

        return count($menus[$menuId])-1;

	}

	public static function updateMenuItem($lang, $menuId, $itemId, $item){

		$menus = self::getMenus($lang);

		if (!isset($menus[$menuId])){
			$menus[$menuId] = array();
		}

		$menus[$menuId][$itemId] = $item;

		self::saveMenus($menus, $lang);

	}

	public static function deleteMenuItem($lang, $menuId, $itemId){

		$menus = self::getMenus($lang);

		unset($menus[$menuId][$itemId]);

		self::saveMenus($menus, $lang);

	}

    public static function getPlainPagesList($lang){

        $pages[] = array(
            'title' => Lang::get('homePage'),
            'uri' => '/',
            'level' => 0
        );

        $pagesTree = self::getPagesTree($lang);

        if (empty($pagesTree['children'])) { return $pages; }

        $pages = self::getPlainPagesListRecursive($pagesTree['children'], $pages, 1);

        return $pages;

    }

    private static function getPlainPagesListRecursive($tree, $list=array(), $level=0){

        foreach($tree as $node){

            $list[] = array(
                'title' => $node['text'],
                'uri' => $node['data']['url'],
                'level' => $level
            );

            if (!empty($node['children'])){
                $list = self::getPlainPagesListRecursive($node['children'], $list, $level+1);
            }

        }

        return $list;

    }

	public static function buildPagesTree($lang){

        Files::removeEmptyDirectories(self::path('data', 'content', $lang, 'pages'));

		$pagesTree = array(
            'id' => 'n-index',
            'text' => Lang::get('homePage'),
            'icon' => 'fa fa-home',
            'state' => array(
                'opened' => true
            ),
            'data' => array(
                'url' => 'index',
                'type' => 'page'
            ),
            'children' => self::buildPagesTreeRecursive($lang)
        );

		$pagesTreeFilePath = self::path('data', 'content', $lang, 'pages.json');

		self::saveArrayAsJson($pagesTreeFilePath, $pagesTree);

	}

	public static function buildPagesTreeRecursive($lang, $siteDir = '', $tree = array()){

		$directory = self::path('data', 'content', $lang, 'pages', $siteDir ? $siteDir : '');

		$nodesAdded = array();

		$files = scandir($directory);

        $filesFound = 0;

		foreach($files as $file){

            $filesFound++;

			if ($file == '.' || $file == '..'){
				continue;
			}

            $filePath = $directory . DIRECTORY_SEPARATOR . $file;
            $isChilds = is_dir($filePath);

            if (!$isChilds && pathinfo($file, PATHINFO_EXTENSION) !== 'json'){
                continue;
            }

            if ($file == 'index.json' && !$siteDir){
                continue;
            }

			$fileNameOnly = pathinfo($file, PATHINFO_FILENAME);

			$title = $isChilds ? $file : $fileNameOnly;

			$nodeId = 'n-' . str_replace('/', '-', ($siteDir ? $siteDir . '-' : '') . $fileNameOnly);
			$nextSiteDir = ($siteDir ? $siteDir . '/' : '') . $file;

			$node = array(
				'id' => $nodeId,
				'text' => $title,
				'data' => array(
					'url' => ($siteDir ? $siteDir . '/' : '') . $title,
					'type' => $isChilds ? 'folder' : 'page'
				),
				'icon' => $isChilds ? 'fa fa-folder' : 'fa fa-file-o'
			);

			if ($isChilds) {
				$node['children'] = array();
				$node['children'] = self::buildPagesTreeRecursive($lang, $nextSiteDir, $node['children']);
			}

			if (!isset($nodesAdded[$nodeId])){

				$nodesAdded[$node['id']] = count($tree);
				$tree[] = $node;

			} else {

				$tree[ $nodesAdded[$node['id']] ]['icon'] = 'fa fa-file-o';
				$tree[ $nodesAdded[$node['id']] ]['data']['type'] = 'page';

			}
		}

        if ($filesFound == 2){ rmdir($directory); }

		return $tree;

	}

	public static function getPagesTree($lang, $isArray = true){

		$treeFilePath = self::path('data', 'content', $lang, 'pages.json');

		if (!$isArray){
			return file_get_contents($treeFilePath);
		}

		return self::loadArrayFromJson($treeFilePath);

	}

	public static function saveLayoutsList($layouts){

		self::saveArrayAsJson(self::path('data', 'layouts.json'), $layouts);

	}

	public static function getLayoutsList(){

		return self::loadArrayFromJson(self::path('data', 'layouts.json'));

	}

	public static function getLayoutsFilesList(){

		$layoutsList = array();

		foreach(glob(self::path('theme', '*.html')) as $file){
			$layoutId = basename($file);
			$layoutsList[$layoutId] = $layoutId;
		}

		foreach(glob(self::path('theme', '*.htm')) as $file){
			$layoutId = basename($file);
			$layoutsList[$layoutId] = $layoutId;
		}

		return $layoutsList;

	}

    public static function saveGlobalCode($code){

        $globalCodeFile = self::path('data', 'globalcode.json');

        self::saveArrayAsJson($globalCodeFile, $code);

    }

    public static function getGlobalCode(){

        $globalCodeFile = self::path('data', 'globalcode.json');

        if (!file_exists($globalCodeFile)) { return array('html'=>''); }

        return self::loadArrayFromJson($globalCodeFile);

    }

	public static function saveMenus($menus, $lang=false){

        if ($lang){
            self::saveArrayAsJson(self::path('data', 'content', $lang, 'menus.json'), $menus);
            return;
        }

        $langs = Config::get('langs');

        foreach($langs as $lang){
            if (!isset($menus[$lang])) { $menus[$lang] = array(); }
            self::saveMenus($menus[$lang], $lang);
        }

	}

	public static function getMenus($lang=false){

        if ($lang){
            return self::loadArrayFromJson(self::path('data', 'content', $lang, 'menus.json'));
        }

        $langs = Config::get('langs');
        $menus = array();

        foreach($langs as $lang){
            $menus[$lang] = self::getMenus($lang);
        }

        return $menus;

	}

	public static function saveArrayAsJson($file, $data){

        $dir = pathinfo($file, PATHINFO_DIRNAME);

        if (!file_exists($dir) || !is_dir($dir)){ @mkdir($dir, 0777, true); }

		if (@file_put_contents($file, json_encode($data)) === false){
            throw new \Exception(Lang::get('errorJsonSave'));
        }

	}

	public static function loadArrayFromJson($file){

        if (!file_exists($file)) { return array(); }

        $json = @file_get_contents($file);

        if ($json === false){
            throw new Exception(Lang::get('errorJsonLoad'));
        }

		return json_decode($json, true);

	}

	public static function loadFileContents($file){

		return file_get_contents($file);

	}

	public static function getWidgetsList(){

		return self::loadArrayFromJson(self::path('app', 'widgets', 'widgets.json'));

	}

	public static function getWidgetsObjects(){

		$widgetsObjects = array();
		$widgetsList = self::getWidgetsList();

		foreach ($widgetsList as $id){

			$widgetClass = "\inlinecms\widgets\\{$id}\\{$id}";
            $widget = new $widgetClass($id);

            $widgetsObjects[$id] = $widget;
		}

		return $widgetsObjects;

	}

    public static function getWidget($id){

        $widgetClass = "\inlinecms\widgets\\{$id}\\{$id}";
        $widget = new $widgetClass($id);

        return $widget;

    }

    public static function redirect($url, $code=303){
        if ($code == 301){
            Response::addHeader('HTTP/1.1 301 Moved Permanently');
        } else {
            Response::addHeader('HTTP/1.1 303 See Other');
        }
        Response::addHeader('Location: '.$url);
        Response::send();
    }

	public static function error404(){
		die('<h1>404</h1>');
	}

    public static function errorAuth(){
        Response::sendJson(array(
            'success' => false,
            'no_auth' => true
        )); 
    }

    public static function route($requestUri) {

        if (!Config::isInstalled()){
            self::runModule('setup', 'index');
            exit;
        }

        if (mb_strstr($requestUri, '?')){
            $uriParts = explode('?', $requestUri);
            $requestUri = $uriParts[0];
        }

        if (Request::has('edit')) {
            if (!User::isLogged()){
                self::runModule('auth', 'login', false);
            }
            self::redirect(ROOT_URL . $requestUri);
        }

        if (Request::has('exit')) {
            User::logout();
            self::redirect(ROOT_URL . $requestUri);
        }

		$lang = Config::get('default_lang');
		$uriLang = false;

		$uriParts = explode('/', trim($requestUri, '/'));
		if (mb_strlen($uriParts[0])==2 && in_array($uriParts[0], Config::get('langs'))){
			$uriLang = $uriParts[0];
			unset($uriParts[0]);
			$requestUri = '/' . implode('/', $uriParts);
			if ($uriLang == $lang){
				self::redirect($requestUri, 301);
			}
			$lang = $uriLang;
		}

		self::$currentLang = $lang;

        $page = new Page($lang, $requestUri);

        if (!$page->isExists()){
            self::error404();
        }

        $page->load();

		$html = '';

        if (User::isLogged()){
            $html = Layout::renderPageEditor($page);
        } else {
            $html = Layout::renderPage($page);
        }

		Response::html($html);
		Response::send();

    }

	public static function runModule($moduleName, $actionName, $isExitAfterRun = true){

		$moduleClass = "inlinecms\modules\\{$moduleName}\\{$moduleName}";

		$module = new $moduleClass();

		if (!method_exists($module, $actionName)){ self::error404(); }

		$module->_before();
		$module->{$actionName}();
		$module->_after();

        if ($isExitAfterRun) { exit; }

	}

    public static function doneLayoutEditing(){

        $contentDir = self::path('data', 'content', Config::get('default_lang'), 'pages');

        if (!file_exists($contentDir) || Files::isDirectoryEmpty($contentDir)){
            self::initializeSite();
        }

        self::redirect(ROOT_URL . '/');

    }

	public static function getCurrentLang(){
		return self::$currentLang;
	}

	public static function path(){
		return ROOT_PATH . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, func_get_args());
	}

	public static function pathRelative(){
		return implode(DIRECTORY_SEPARATOR, func_get_args());
	}

    public static function pathUpload(){
        return self::path('upload', implode(DIRECTORY_SEPARATOR, func_get_args()));
    }

    public static function getBackendUrl(){

        return ROOT_URL . '/backend.php';

    }

    public static function getEditorUrl($page){

        $url = ROOT_URL . "/editor.php?lang={$page->getLang()}&page={$page->getUri()}";

        return $url;

    }

    public static function getLayouterUrl($layoutFile = false, $isWizard=false){

        $url = ROOT_URL . "/layouter.php";

        if ($layoutFile) { $url .= "?layout={$layoutFile}"; }
        if ($isWizard) { $url .= '&wizard=1'; }

        return $url;

    }

    public static function getLayouterEditorUrl($layoutFile){

        $url = ROOT_URL . "/editor.php?layout={$layoutFile}";

        return $url;

    }


}
