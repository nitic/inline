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

use InlineCMS\Core\Config;

class Url {

	public static function get($url, $lang){

        if ($url == 'index') { $url = '/'; }

		$urlParts = array();

		$urlParts[] = ROOT_URL;

		if ($lang != Config::get('default_lang')){
			$urlParts[] = $lang;
		}

		if ($url != '/'){
			$urlParts[] = $url;
		}

		$url = implode("/", $urlParts);

		if ($url == '') { $url = '/'; }

		return '/' . trim($url, '/');

	}

}
