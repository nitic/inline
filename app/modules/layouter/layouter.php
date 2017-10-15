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

namespace InlineCMS\Modules\Layouter;

use InlineCMS\Core\Core;
use InlineCMS\Core\Config;
use InlineCMS\Core\Module;
use InlineCMS\Core\Request;
use InlineCMS\Core\Response;
use InlineCMS\Core\User;
use InlineCMS\Core\Layout;
use InlineCMS\Core\Lang;

class Layouter extends Module {

	public function _before() {
		parent::_before();
		if (!User::isLogged()) { Core::errorAuth(); }
	}

	public function loadStyleForm(){

		Response::json(array(
			'html' => Layout::renderTemplate('layouter/style')
        ));

		Response::send();

	}

	public function loadRegionForm(){

        $mode = Request::get('mode');

		Response::json(array(
			'html' => Layout::renderTemplate('layouter/region', array(
                'mode' => $mode,
            ))
		));

		Response::send();

	}

	public function loadMenuForm(){

        $mode = Request::get('mode');

        $menus = array_keys(Core::getMenus(Config::get('default_lang')));

		Response::json(array(
			'html' => Layout::renderTemplate('layouter/menu', array(
                'mode' => $mode,
                'menus' => $menus
            ))
		));

		Response::send();

	}

    public function validateRegion(){

		$id = Request::get('id');

        $errors = array();

		if (!$id){
            $errors[] = Lang::get('regionIdError');
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

    public function validateMenu(){

		$id = Request::get('id');

        $errors = array();

		if (!$id){
            $errors[] = Lang::get('menuIdError');
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

    public function saveLayout(){

        $layoutFile = Request::get('layout');
        $regionsJson = Request::get('regions');
        $menusJson = Request::get('menus');
        $deletionsJson = Request::get('deletions');
        $stylesJson = Request::get('styles');
        $jquery = Request::get('jquery');

        $structure = array(
            'regions' => json_decode($regionsJson, true),
            'menus' => json_decode($menusJson, true),
            'deletions' => json_decode($deletionsJson, true),
            'styles' => json_decode($stylesJson, true),
        );

        Layout::updateLayout($layoutFile, $structure, $jquery);

        Core::initializeLayout($layoutFile);

        Response::sendJson(array(
			'success' => true
		));

    }

    public function updateLayout(){

        $layoutFile = Request::get('layout');
        $regionsJson = Request::get('regions');
        $menusJson = Request::get('menus');
        $deletionsJson = Request::get('deletions');
        $stylesJson = Request::get('styles');
        $jquery = Request::get('jquery');

        $structure = array(
            'regions' => json_decode($regionsJson, true),
            'menus' => json_decode($menusJson, true),
            'deletions' => json_decode($deletionsJson, true),
            'styles' => json_decode($stylesJson, true),
        );

        $success = Layout::updateLayoutFromTemplate($layoutFile, $structure, $jquery);

        if ($success) { Core::initializeLayout($layoutFile); }

        Response::sendJson(array(
			'success' => $success
		));

    }

}
