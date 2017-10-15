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
    use InlineCMS\Core\Page;
	use InlineCMS\Core\Request;
	use InlineCMS\Core\User;
	use InlineCMS\Helpers\Url;

    require 'bootstrap.php';

	if (!User::isLogged()) { Core::error404(); }

    $lang = Request::get('lang');
    $uri = Request::get('page');
    $layout = Request::get('layout');

    if ($lang && $uri){

        $page = new Page($lang, $uri);

        if (!$page->isExists()) { Core::error404(); }

        $page->load();

        Request::setUrl(Url::get($uri, $lang));

        echo Layout::renderEditablePage($page);

    }

    if ($layout){

        echo Layout::renderEditableLayout($layout);

    }
