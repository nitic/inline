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

    Core::route( Request::getUrl() );

