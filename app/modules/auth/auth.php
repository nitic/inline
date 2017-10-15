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

namespace InlineCMS\Modules\Auth;

use InlineCMS\Core\Config;
use InlineCMS\Core\Module;
use InlineCMS\Core\Request;
use InlineCMS\Core\Response;
use InlineCMS\Core\Layout;
use InlineCMS\Core\Lang;
use InlineCMS\Core\User;

class Auth extends Module {
	
    public function login(){
        
        $email = Request::get('auth_email');
        $password = Request::get('auth_password');
        
        if (!$email || !$password){        
            Response::sendHtml(Layout::renderTemplate('auth/login'));
            return;
        }
        
        if ($email == Config::get('email') && sha1($email.$password) == Config::get('password_hash')){
            User::login();            
            return;
        }
        
        Response::sendHtml(Layout::renderTemplate('auth/login', array(
            'error' => Lang::get('authError')
        )));
        
    }
    
}
