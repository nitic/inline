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

namespace InlineCMS\Widgets\Code;

use InlineCMS\Core\Core;
use InlineCMS\Core\Widget;

class Code extends Widget {

    public function isCacheable(){
        return false;
    }

    public function getContent($page, $regionId, $widgetData) {

        $code = $widgetData['options'];

        $content = '';

        if (!empty($code['html'])){
            $content .= $code['html'] . "\n\n";
        }

        if (!empty($code['js'])){
            $content .= '<script>' . $code['js'] . '</script>' . "\n\n";
        }

        if (!empty($code['css'])){
            $content .= '<style>' . $code['css'] . '</style>' . "\n\n";
        }

        if (!empty($code['php'])){

            $phpFile = basename($code['php']);
            $phpFilePath = Core::path('app', 'custom', $phpFile);

            if (file_exists($phpFilePath)){
                ob_start();
                include $phpFilePath;
                $content .= ob_get_clean();
            }

        }

        return $content;

    }

}
