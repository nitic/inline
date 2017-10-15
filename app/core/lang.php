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
use InlineCMS\Core\Config;

class Lang {
	
	private static $phrases = array();
	private static $isPhrasesLoaded = false;
	
	public static function get($phraseId){
		
		if (!self::$isPhrasesLoaded){
            self::loadServerLang(Config::get('ui_lang'));
			self::$isPhrasesLoaded = true;
		}
		
		if (!isset(self::$phrases[$phraseId])){ return $phraseId; }
        
        $phrase = self::$phrases[$phraseId];
        
        if (func_num_args() > 1){
            $formatArguments = func_get_args();
            $formatArguments[0] = $phrase;
            $phrase = call_user_func_array('sprintf', $formatArguments);
        }
		
		return $phrase;
		
	}
    
    private static function loadServerLang($lang){
        
        self::$phrases = self::loadLang($lang, array('shared', 'server'));                
        
    }
    
    public static function loadLang($lang, $sets){
        
        if (!is_array($sets)){ $sets = array($sets); }
        
        $phrases = array();
        
        foreach($sets as $set) {
            
            $langFile = Core::path('app', 'langs', $lang, "{$lang}.{$set}.json");
            
            if (!file_exists($langFile)){ continue; }
            
            $phrases = array_merge($phrases, Core::loadArrayFromJson($langFile));
			
        }
        
        return $phrases;
        
    }
    
    public static function loadWidgetLang($handler, $lang, $sets){
        
        if (!is_array($sets)){ $sets = array($sets); }
        
        $phrases = array();
        
        foreach($sets as $set) {
            
            $langFile = Core::path('app', 'widgets', $handler, 'langs', $lang, "{$lang}.{$set}.json");
            
            if (!file_exists($langFile)){ continue; }
            
            $phrases = array_merge($phrases, Core::loadArrayFromJson($langFile));
			
        }
        
        return $phrases;
        
    }
    
    public static function getJsLangFileUrl($lang){
        
        return ROOT_URL . "/static/cache/lang.{$lang}.json";
        
    }
    
    public static function getJsLangFilePath($lang){
        
        return Core::path('static', 'cache', "lang.{$lang}.json");
        
    }
    
	public static function getWidgetLang($handler, $lang){
		
		$widgetLangFile = Core::path('app', 'widgets', $handler, 'langs', "{$lang}.json");
		
		if (!file_exists($widgetLangFile)){ return array(); }
		
		return Core::loadArrayFromJson($widgetLangFile);            
		
	}
    
    public static function getLangsList(){
        
        $langsList = array();
		
		foreach(glob(Core::path('app', 'langs', '*'), GLOB_ONLYDIR) as $langDir){
			$langId = basename($langDir);
            $langsList[$langId] = Core::loadArrayFromJson(Core::path('app', 'langs', $langId, "{$langId}.json"));
		}
		
		return $langsList;
        
    }
    
}
