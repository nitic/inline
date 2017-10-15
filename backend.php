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
	use InlineCMS\Core\Request;
	require 'bootstrap.php';

	if (!Request::has('_module') || !preg_match('/([a-z0-9_]+)/i', Request::get('_module'))){
		exit;
	}

	if (Request::has('_action') && !preg_match('/([a-z0-9_]+)/i', Request::get('_action'))){
		exit;
	}

	$module = Request::get('_module');
	$action = Request::get('_action', 'index');

    Core::runModule($module, $action);
