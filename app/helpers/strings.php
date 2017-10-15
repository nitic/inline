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

namespace InlineCMS\Helpers;

class Strings {

    public static function startsWith($haystack, $needle) {

        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;

    }

}
