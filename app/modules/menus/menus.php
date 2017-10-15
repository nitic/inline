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

namespace InlineCMS\Modules\Menus;

use InlineCMS\Core\Core;
use InlineCMS\Core\Module;
use InlineCMS\Core\Request;
use InlineCMS\Core\Page;
use InlineCMS\Core\Response;
use InlineCMS\Core\User;
use InlineCMS\Core\Layout;
use InlineCMS\Core\Lang;
use InlineCMS\Helpers\Url;

class Menus extends Module {

	public function _before() {
		parent::_before();
		if (!User::isLogged()) { Core::errorAuth(); }
	}

	public function getMenusTree() {

		$lang = Request::get('lang');

		$menus = Core::getMenus($lang);
		$tree = array();

		foreach ($menus as $menuId => $items){

			$menuNodeId = $this->getMenuNodeId($menuId);

			$menu = array(
				'id' => "n-{$menuNodeId}",
				'text' => $menuId,
				'icon' => 'fa fa-bars',
                'data' => array(
                    'menu' => $menuId
                ),
				'state' => array(
					'opened' => true,
					'selected' => true
				),
				'children' => array()
			);

			if ($items){
				foreach($items as $id=>$item){
					$menu['children'][] = $this->getMenuItemNode($menuId, $menuNodeId, $id, $item);
				}
			}

			$tree[] = $menu;

		}

		Response::sendJson(array(
            'menus' => $menus,
            'tree' => $tree
        ));

	}

	public function loadMenuItemForm(){

        $mode = Request::get('mode');
        $lang = Request::get('lang');

        $menus = Core::getMenus($lang);
        $menusList = array();

        foreach ($menus as $menuId => $items){
			$menuNodeId = $this->getMenuNodeId($menuId);
            $menusList[$menuNodeId] = $menuId;
        }

		Response::sendJson(array(
			'html' => Layout::renderTemplate('editor/menu-item', array(
                'mode' => $mode,
                'menus' => $menusList
            ))
		));

	}

    public function saveMenusOrdering(){

        $lang = Request::get('lang');
        $ordering = Request::get('ordering');

        try {

            $menus = Core::getMenus($lang);

            foreach ($menus as $menuId => $items){

                $reorderedItems = array();

                foreach(explode(',', $ordering[$menuId]) as $position){
                    $reorderedItems[] = $items[$position];
                }

                $menus[$menuId] = $reorderedItems;

            }

            Core::saveMenus($menus, $lang);

        } catch (\Exception $e){
            Response::sendJson(array(
                'success'=>false,
                'error'=>$e->getMessage()
            ));
        }

        Response::sendJson(array(
            'success'=>true
        ));

    }

    public function createMenuItem(){

        $menuId = Request::get('menu');
		$title = Request::get('title');
		$type = Request::get('type');
		$pageUri = Request::get('page');
		$url = Request::get('url');
		$target = Request::get('target');
		$lang = Request::get('lang');
		$currentUri = Request::get('current_uri');

        try {

            $item = array(
                'type' => $type,
                'title' => $title,
                'url' => $type=='page' ? $pageUri : $url,
                'target' => $target
            );

            $item['id'] = Core::createMenuItem($lang, $menuId, $item);

            $html = '';

            $page = new Page($lang, $currentUri);

            $page->load();

            $layoutScheme = Layout::getLayoutJson($page->getLayoutName());

            $menuTemplate = $layoutScheme['menus'][$menuId];

            if ($menuTemplate){

                $itemUrl = $type=='page' ? Url::get($pageUri, $lang) : $url;

                $isActive = (Url::get($currentUri, $lang) == $itemUrl) && !empty($menuTemplate['selected_item']);

                $template = $isActive ? $menuTemplate['selected_item'] : $menuTemplate['item'];

                $html = Layout::renderMenuItem($template, $itemUrl, $item);

            }

        } catch (\Exception $e){

            Response::sendJson(array(
                'success'=>false,
                'error'=>$e->getMessage()
            ));

        }

        Response::sendJson(array(
			'success' => true,
            'item_html' => $html,
            'node' => $this->getMenuItemNode($menuId, $this->getMenuNodeId($menuId), $item['id'], $item),
            'item' => $item
		));

		Response::send();

    }

    public function editMenuItem(){

        $menuId = Request::get('menu');
        $itemId = Request::get('id');
		$title = Request::get('title');
		$type = Request::get('type');
		$pageUri = Request::get('page');
		$url = Request::get('url');
		$target = Request::get('target');
		$lang = Request::get('lang');
		$currentUri = Request::get('current_uri');

        try {

            $item = array(
                'id' => $itemId,
                'type' => $type,
                'title' => $title,
                'url' => $type=='page' ? $pageUri : $url,
                'target' => $target
            );

            Core::updateMenuItem($lang, $menuId, $itemId, $item);

            $html = '';

            $page = new Page($lang, $currentUri);

            $page->load();

            $layoutScheme = Layout::getLayoutJson($page->getLayoutName());

            $menuTemplate = $layoutScheme['menus'][$menuId];

            if ($menuTemplate){

                $itemUrl = $type=='page' ? Url::get($pageUri, $lang) : $url;

                $isActive = (Url::get($currentUri, $lang) == $itemUrl) && !empty($menuTemplate['selected_item']);

                $template = $isActive ? $menuTemplate['selected_item'] : $menuTemplate['item'];

                $html = Layout::renderMenuItem($template, $itemUrl, $item);

            }

        } catch (\Exception $e){

            Response::sendJson(array(
                'success'=>false,
                'error'=>$e->getMessage()
            ));

        }

        Response::sendJson(array(
			'success' => true,
            'item_html' => $html,
            'node' => $this->getMenuItemNode($menuId, $this->getMenuNodeId($menuId), $item['id'], $item),
            'item' => $item
		));

    }

    public function deleteMenuItem(){

        $lang = Request::get('lang');
        $menuId = Request::get('menu');
        $itemId = Request::get('id');

        try {

            Core::deleteMenuItem($lang, $menuId, $itemId);

        } catch (\Exception $e){
            Response::sendJson(array(
                'success'=>false,
                'error'=>$e->getMessage()
            ));
        }

        Response::sendJson(array(
			'success' => true,
		));

    }

    public function validateMenuItem(){

		$title = Request::get('title');
		$type = Request::get('type');
		$pageUri = Request::get('page');
		$lang = Request::get('lang');

        $errors = array();

		if (!$title){
            $errors[] = Lang::get('menuItemTitleError');
		}

		if ($type == 'page'){
            $page = new Page($lang, $pageUri);
            if (!$page->isExists()){
                $errors[] = Lang::get('menuItemPageError');
            }
		}

        if ($errors && count($errors) == 1){
            $errors = $errors[0];
        }

		Response::json(array(
			'success' => !$errors,
            'error' => $errors
		));

		Response::send();

	}

    private function getMenuItemNode($menuId, $menuNodeId, $id, $item){

        return array(
            'id' => "n-{$menuNodeId}-{$id}",
            'text' => $item['title'],
            'icon' => 'fa fa-link',
            'data' => array(
                'type' => $item['type'],
                'url' => $item['url'],
                'id' => $id,
            )
        );

    }

	private function getMenuNodeId($menuId){

		$menuId = mb_strtolower($menuId, 'utf-8');
		$menuId = str_replace(' ', '-', $menuId);

		$nodeId = preg_replace ('/[^a-zA-Z0-9\-\/]/u', '-', $menuId);
		$nodeId = preg_replace('/([-]+)/i', '-', $nodeId);
		$nodeId = trim($nodeId, '-');

		if (!$nodeId){ $nodeId = 'untitled'; }

		return $nodeId;

	}

}
