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

namespace InlineCMS\Core;

use InlineCMS\Core\Config;

class Request {

    private static $data = array();
	private static $url = "";

    public static function load(){

        $request = array();

        $urlParts = parse_url($_SERVER['REQUEST_URI']);

        if (!empty($urlParts['query'])){
            parse_str($urlParts['query'], $request);
        }

        $request = array_merge($request, $_REQUEST);

        if (!empty($request)){
            foreach($request as $key => $value){
                self::set($key, $value);
            }
        }

		self::$url = empty($_GET['uri']) ? '' : $_GET['uri'];
        self::$url = mb_substr(self::$url, mb_strlen(ROOT_URL));

        if (!self::$url) { self::$url = '/'; }

    }

	public static function getUrl(){
		return self::$url;
	}

	public static function getHostUrl(){

		$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';

		return $protocol . $_SERVER['HTTP_HOST'] . (ROOT_URL ? ROOT_URL : '') . '/';

	}

	public static function isUrl($url){
		return $url == self::$url;
	}

    public static function setUrl($url){
        self::$url = $url;
    }

    public static function set($key, $value){

        if (is_string($value)){
            $value = trim($value);
        }

        self::$data[$key] = $value;

	}

    public static function get($key, $default=false) {

        if (!isset(self::$data[$key])) { return $default; }

        return self::$data[$key];

    }

    public static function getAll(){
        return self::$data;
    }

    public static function has($key){

        return isset(self::$data[$key]);

    }

}
