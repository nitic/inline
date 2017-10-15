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

class Response {
    
	private static $headers = array();
	private static $output = "";
	
	public static function addHeader($header){
		self::$headers[] = $header;
	}
	
	public static function html($html){
		self::$output = $html;
	}
	
	public static function plain($text){
		self::$output = $text;
	}
	
	public static function json($data){
		
		self::addHeader('Content-Type: application/json');
		
		if (is_array($data)){
			$data = json_encode($data);
		}
		
		self::$output = $data;
		
	}
    
    public static function sendHtml($html){
        self::html($html);
        self::send();
    }
	
    public static function sendPlain($text){
        self::plain($text);
        self::send();
    }
	
    public static function sendJson($data){
        self::json($data);
        self::send();
    }
	
	public static function send(){
		
		if (self::$headers){
			foreach(self::$headers as $header){
				header($header);
			}
		}
		
		if (self::$output){
			echo self::$output;
		}
		
		exit;
		
	}
	
}
