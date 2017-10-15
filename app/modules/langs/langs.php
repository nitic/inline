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

namespace InlineCMS\Modules\Langs;

use InlineCMS\Core\Module;
use InlineCMS\Core\Request;
use InlineCMS\Core\Response;
use InlineCMS\Core\Lang;

class Langs extends Module {
	
    public function loadLang(){
        
        $lang = Request::get('lang');
        $set = Request::get('set');
        
        $phrases = Lang::loadLang($lang, $set);
        
        Response::json($phrases);
        Response::send();
        
    }
	
    public function loadWidgetLang(){
        
        $handler = Request::get('handler');
        $lang = Request::get('lang');
        
        $phrases = Lang::loadWidgetLang($handler, $lang, 'client');
        
        Response::json($phrases);
        Response::send();
        
    }
    
}
