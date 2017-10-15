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

class User {
    
    public static function login(){
        
        $_SESSION['inlinecms']['is_logged'] = true;
        
    }
    
    public static function logout(){
        
        unset($_SESSION['inlinecms']['is_logged']);
        
    }
    
    public static function isLogged(){
        
        return isset($_SESSION['inlinecms']['is_logged']);
        
    }
    
}
