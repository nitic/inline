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

namespace InlineCMS\Modules\Settings;

use InlineCMS\Core\Core;
use InlineCMS\Core\Config;
use InlineCMS\Core\Module;
use InlineCMS\Core\Request;
use InlineCMS\Core\Response;
use InlineCMS\Core\Lang;
use InlineCMS\Core\Layout;
use InlineCMS\Core\User;

class Settings extends Module {

	public function _before() {
		parent::_before();
		if (!User::isLogged()) { Core::errorAuth(); }
	}

	public function loadLayoutsForm(){

        $layouts = Core::getLayoutsList();
        $layoutFiles = Core::getLayoutsFilesList();

        $newLayouts = array();

        if (is_array($layoutFiles)){
            foreach($layoutFiles as $file){
                if (isset($layouts[$file])) { continue; }
                $newLayouts[] = $file;
            }
        }

		Response::sendJson(array(
			'html' => Layout::renderTemplate('settings/layouts', array(
                'layouts' => $layouts,
                'newLayouts' => $newLayouts
            ))
		));

	}

    public function validateLayouts(){

        $new_file = Request::get('layout_file');
        $new_name = Request::get('layout_name');

        $errors = array();

        if ($new_file){

            if (!file_exists(Core::path('theme', $new_file))){
                $errors[] = Lang::get('layoutFileError');
            }

            if (!$new_name){
                $errors[] = Lang::get('layoutNameError');
            }
        }

        if ($errors && count($errors)==1) { $errors = $errors[0]; }

        Response::sendJson(array(
            'success' => !$errors,
            'error' => $errors
        ));

    }

    public function saveLayouts(){

        $new_file = Request::get('layout_file');
        $new_name = Request::get('layout_name');
        $is_open_editor = Request::get('is_open', false);

        try {

            $layouts = Core::getLayoutsList();
            $files = array_keys($layouts);

            foreach($files as $file){
                $key = str_replace('.', '_', $file);
                $layouts[$file] = Request::get('layout:'.$key, $file);
            }

            $url = false;

            if ($new_file && $new_name){
                $layouts[$new_file] = $new_name;
                $url = $is_open_editor ? Core::getLayouterUrl($new_file) : false;
            }

            Core::saveLayoutsList($layouts);

        } catch (\Exception $e){
            Response::sendJson(array(
                'success' => false,
                'error' => $e->getMessage()
            ));
        }

        Response::sendJson(array(
            'success' => true,
            'url' => $url
        ));

    }

	public function loadGlobalCode(){

        try{

            $code = Core::getGlobalCode();

        } catch (\Exception $e){
            Response::sendJson(array(
                'success'=>false,
                'error'=>$e->getMessage()
            ));
        }

		Response::sendJson(array(
            'success' => true,
            'code' => $code
        ));

	}

    public function saveGlobalCode(){

        $html = Request::get('html');

        $code = array('html' => $html);

        try{

            Core::saveGlobalCode($code);

        } catch (\Exception $e){
            Response::sendJson(array(
                'success'=>false,
                'error'=>$e->getMessage()
            ));
        }

		Response::sendJson(array(
            'success' => true,
        ));

    }

	public function loadUserForm(){

        $email = Config::get('email');

		Response::sendJson(array(
			'html' => Layout::renderTemplate('settings/user', array(
                'email' => $email,
            ))
		));

	}

    public function validateUser(){

        $email = Request::get('email');
        $new_pass = Request::get('new_pass');
        $new_pass2 = Request::get('new_pass2');
        $password = Request::get('password');

        $isPasswordCorrect = sha1(Config::get('email').$password) == Config::get('password_hash');

        $errors = array();

        if (!$email) {
            $errors[] = Lang::get('profileEmailError');
        }

        if (!$errors && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = Lang::get('profileEmailFormatError');
        }

        if (!$errors && $new_pass && ($new_pass != $new_pass2)) {
            $errors[] = Lang::get('profileNewPasswordRepeatError');
        }

        if (!$errors && !$isPasswordCorrect) {
            $errors[] = Lang::get('profileOldPasswordError');
        }

        if ($errors && count($errors)==1) { $errors = $errors[0]; }

        Response::sendJson(array(
            'success' => !$errors,
            'error' => $errors
        ));

    }

    public function saveUser(){

        $email = Request::get('email');
        $new_pass = Request::get('new_pass');
        $old_password = Request::get('password');

        $isPasswordCorrect = sha1(Config::get('email').$old_password) == Config::get('password_hash');

        if (!$isPasswordCorrect){
            Response::sendJson(array(
                'success' => false,
                'error' => Lang::get('profileOldPasswordError')
            ));
        }

        Config::set('email', $email);

        $password = $new_pass ? $new_pass : $old_password;

        $hash = sha1($email.$password);

        Config::set('password_hash', $hash);

        try {

            Config::save();

        } catch (\Exception $e){
            Response::sendJson(array(
                'success' => false,
                'error' => $e->getMessage()
            ));
        }

        Response::sendJson(array(
            'success' => true,
        ));

    }

	public function loadMailForm(){

		Response::sendJson(array(
			'html' => Layout::renderTemplate('settings/mail')
		));

	}

    public function saveMail(){

        $mail_from = Request::get('mail_from');
        $mail_transport = Request::get('mail_transport');
        $mail_smtp_host = Request::get('mail_smtp_host');
        $mail_smtp_port = Request::get('mail_smtp_port');
        $mail_smtp_user = Request::get('mail_smtp_user');
        $mail_smtp_pass = Request::get('mail_smtp_pass');
        $mail_smtp_enc = Request::get('mail_smtp_enc');

        Config::set('mail_from', $mail_from);
        Config::set('mail_transport', $mail_transport);
        Config::set('mail_smtp_host', $mail_smtp_host);
        Config::set('mail_smtp_port', $mail_smtp_port);
        Config::set('mail_smtp_user', $mail_smtp_user);
        Config::set('mail_smtp_pass', $mail_smtp_pass);
        Config::set('mail_smtp_enc', $mail_smtp_enc);

        try {

            Config::save();

        } catch (\Exception $e){
            Response::sendJson(array(
                'success' => false,
                'error' => $e->getMessage()
            ));
        }

        Response::sendJson(array(
            'success' => true,
        ));

    }

}
