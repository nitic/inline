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

use InlineCMS\Core\Core;

class Config {

    private static $data = array();

    private static function getPath(){
        return Core::path('data' , 'config.json');
    }

    public static function isInstalled(){
        return file_exists(self::getPath());
    }

    public static function load(){

        if (!self::isInstalled()) { return false; }

        self::$data = Core::loadArrayFromJson(self::getPath());

	}

	public static function is($key){

		return self::get($key) == true;

	}

    public static function get($key=false, $default=false) {

        if (!$key) { return self::$data; }

        if (empty(self::$data[$key])) { return $default; }

        return self::$data[$key];

    }

    public static function set($key, $value){

        self::$data[$key] = $value;

    }

    public static function save(){

        Core::saveArrayAsJson(self::getPath(), self::$data);

    }

}
