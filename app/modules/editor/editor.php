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

namespace InlineCMS\Modules\Editor;

use InlineCMS\Core\Core;
use InlineCMS\Core\Module;
use InlineCMS\Core\Response;
use InlineCMS\Core\Layout;
use InlineCMS\Core\User;

class Editor extends Module {

	public function _before() {
		parent::_before();
		if (!User::isLogged()) { Core::errorAuth(); }
	}

	public function loadImageForm(){

		Response::json(array(
			'html' => Layout::renderTemplate('editor/image')
        ));

		Response::send();

	}

	public function loadSourceForm(){

		Response::json(array(
			'html' => Layout::renderTemplate('editor/source')
        ));

		Response::send();

	}

}
