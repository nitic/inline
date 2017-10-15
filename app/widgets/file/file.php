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

namespace InlineCMS\Widgets\File;

use InlineCMS\Core\Core;
use InlineCMS\Core\Widget;
use InlineCMS\Core\Request;
use InlineCMS\Core\Response;

class File extends Widget {

    public function delete(){

        $url = Request::get('url');

        $path = str_replace('/', DIRECTORY_SEPARATOR, trim(mb_substr($url, mb_strlen(ROOT_URL)), '/'));

        $pathSegments = explode(DIRECTORY_SEPARATOR, $path);

        if ($pathSegments[0] != 'upload') { exit; }

        $fullPath = Core::path($path);

        @unlink($fullPath);

        Response::sendJson(array(
            'success' => true,
        ));

    }

}
