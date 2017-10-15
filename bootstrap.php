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

    session_start();

    define('ROOT_PATH', dirname(__FILE__));
    define('ROOT_URL', str_replace(rtrim($_SERVER['DOCUMENT_ROOT'], '/'), '', str_replace(DIRECTORY_SEPARATOR, '/', ROOT_PATH)));

    mb_internal_encoding('UTF-8');

    require_once ROOT_PATH . '/app/loader.php';

    spl_autoload_register(array('\InlineCMS\Loader', 'autoLoad'));

    \InlineCMS\Core\Config::load();
    \InlineCMS\Core\Request::load();
