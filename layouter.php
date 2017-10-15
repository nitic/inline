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

	use InlineCMS\Core\Core;
	use InlineCMS\Core\Layout;
	use InlineCMS\Core\Request;
	use InlineCMS\Core\User;

    require 'bootstrap.php';

	if (!User::isLogged()) { Core::error404(); }



    $isDone = Request::get('done');

    if ($isDone){
        Core::doneLayoutEditing();
        exit;
    }

    $layoutFile = Request::get('layout');
    if (!$layoutFile) { Core::error404(); }

    $isWizard = Request::get('wizard');

    $layoutHtml = Layout::renderLayoutEditor($layoutFile, $isWizard);
    if (!$layoutHtml) { Core::error404(); }

    echo $layoutHtml; exit;