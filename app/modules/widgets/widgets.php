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

namespace InlineCMS\Modules\Widgets;

use InlineCMS\Core\Core;
use InlineCMS\Core\Module;
use InlineCMS\Core\Request;
use InlineCMS\Core\Response;
use InlineCMS\Core\User;

class Widgets extends Module {

	public function loadOptionsForm(){

        if (!User::isLogged()) { Core::errorAuth(); }

		$handler = Request::get('handler');

        $widget = Core::getWidget($handler);

        if (!$widget) { exit; }

        Response::json(array(
			'html' => $widget->renderOptionsForm()
		));

		Response::send();

	}

    public function run(){

        $widgetId = Request::get('_widgetId');
        $action = Request::get('_widgetAction');

        if (!$widgetId || !$action) { Core::error404(); }

        $widgetObject = Core::getWidget($widgetId);

        if (method_exists($widgetObject, $action)){
            $widgetObject->{$action}();
        }

    }

}
