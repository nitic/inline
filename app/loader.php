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

namespace InlineCMS;

class Loader {

    public static function autoLoad($className){

        $className = mb_strtolower($className);

        $pathSegments = array_map(function ($segment){
            if ($segment == 'inlinecms') { return 'app'; }
            return $segment;
        }, explode('\\', $className));

        $path = ROOT_PATH . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pathSegments);
        $file = $path . '.php';

        include_once $file;

    }

    public static function loadLibrary($libraryFile) {

        return include_once ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . $libraryFile;

    }

}

