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

namespace InlineCMS\Modules\Setup;

use InlineCMS\Core\Core;
use InlineCMS\Core\Config;
use InlineCMS\Core\Module;
use InlineCMS\Core\Request;
use InlineCMS\Core\Response;
use InlineCMS\Core\Lang;
use InlineCMS\Core\Layout;
use InlineCMS\Core\User;
use InlineCMS\Loader;

class Setup extends Module {

    public function _before() {
        parent::_before();
        if (Config::isInstalled()){ Core::error404(); }
    }

    public function index(){

        $lang = Request::get('lang', 'en');
        $phrases = Lang::loadLang($lang, 'setup');

        $steps = array(
            'start' => $phrases['stepStart'],
            'check' => $phrases['stepCheckServer'],
            'site' => $phrases['stepSite'],
            'admin' => $phrases['stepAdministrator'],
            'template' => $phrases['stepTemplate'],
            'finish' => $phrases['stepFinish']
        );

        $currentStep = 'start';

        Response::html(Layout::renderTemplate('setup/wizard', array(
            'langId' => $lang,
            'lang' => $phrases,
            'steps' => $steps,
            'currentStep' => $currentStep,
            'step' => $this->renderStep($currentStep, $phrases)
        )));

        Response::send();

    }

    public function loadLangName(){

        $code = Request::get('code');

        $langs = Loader::loadLibrary('langs.php');

        $name = isset($langs[mb_strtoupper($code)]) ? $langs[mb_strtoupper($code)] : '';

        Response::json(array('name' => $name));
        Response::send();

    }

    public function loadStep(){

        $step = Request::get('step');
        $lang = Request::get('lang');

        $phrases = Lang::loadLang($lang, 'setup');

        Response::json(array(
            'html' => $this->renderStep($step, $phrases)
        ));

        Response::send();

    }

    public function validateStep(){

        $step = Request::get('step');
        $lang = Request::get('lang');

        $phrases = Lang::loadLang($lang, 'setup');

        $method = 'validate' . strtoupper($step);

        $result = array();

        if (method_exists($this, $method)){
            $result = $this->{$method}($phrases);
        } else {
            $result = array('success' => true);
        }

        Response::json($result);

        Response::send();

    }

    private function renderStep($step, $lang){

        $stepTemplate = "setup/step-{$step}";

        $method = 'step' . strtoupper($step);

        if (method_exists($this, $method)){
            return $this->{$method}($stepTemplate, $lang);
        }

        return Layout::renderTemplate($stepTemplate, array(
            'lang' => $lang,
        ));

    }

    private function stepStart($template, $lang){

        $currentLang = Request::get('lang', 'en');

        $langsList = Lang::getLangsList();

        return Layout::renderTemplate($template, array(
            'lang' => $lang,
            'langs' => $langsList,
            'currentLang' => $currentLang
        ));

    }

    private function stepCheck($template, $lang){

        $reqs = $this->getCheckedRequirements($lang);

        $isErrors = false;

        foreach($reqs as $req){
            if (!$req['isValid']){
                $isErrors = true;
                break;
            }
        }

        return Layout::renderTemplate($template, array(
            'lang' => $lang,
            'reqs' => $reqs,
            'errors' => $isErrors
        ));

    }

    private function validateCheck($lang){

        $reqs = $this->getCheckedRequirements($lang);
        $error = false;

        foreach($reqs as $req){
            if (!$req['isValid']){
                $error = $lang['checkError'];
                break;
            }
        }

        return array(
            'success' => !$error,
            'error' => $error
        );

    }

    private function getCheckedRequirements($lang){

        $reqs = array();

        $minPHPversion = '5.3.0';
        $folders = array('data', 'upload');

        $reqs[] = array(
            'title' => sprintf($lang['checkPHP'], $minPHPversion),
            'isValid' => (version_compare(PHP_VERSION, $minPHPversion) >= 0)
        );

        $reqs[] = array(
            'title' => sprintf($lang['checkExtension'], 'JSON'),
            'isValid' => function_exists('json_encode'),
            'nested' => true
        );

        $reqs[] = array(
            'title' => sprintf($lang['checkExtension'], 'GD'),
            'isValid' => function_exists('imagecopyresampled'),
            'nested' => true
        );

        foreach($folders as $folder){
            $reqs[] = array(
                'title' => sprintf($lang['checkFolder'], $folder),
                'isValid' => is_writable(Core::path($folder))
            );
        };

        return $reqs;

    }

    private function stepSite($template, $lang){

        $langs = Loader::loadLibrary('langs.php');

        $currentLang = Request::get('lang', 'en');
        $currentName = isset($langs[mb_strtoupper($currentLang)]) ? $langs[mb_strtoupper($currentLang)] : '';


        return Layout::renderTemplate($template, array(
            'lang' => $lang,
            'currentLang' => $currentLang,
            'currentName' => $currentName,
        ));

    }

    private function validateSite($lang){

        $name = Request::get('site_name');

        $error = false;

        if (!$name) { $error = $lang['siteNameError']; }

        return array(
            'success' => !$error,
            'error' => $error
        );

    }

    private function stepAdmin($template, $lang){

        $password = $this->generatePassword();

        return Layout::renderTemplate($template, array(
            'lang' => $lang,
            'password' => $password,
        ));

    }

    private function validateAdmin($lang){

        $email = Request::get('admin_email');
        $password = Request::get('admin_password');
        $password2 = Request::get('admin_password2');

        $error = false;

        if (!$email) { $error = $lang['adminEmailError']; }

        if (!$error && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = $lang['adminEmailFormatError'];
        }

        if (!$error && (!$password || !$password2)) { $error = $lang['adminPasswordError']; }

        if (!$error && ($password != $password2)) { $error = $lang['adminPasswordRepeatError']; }

        return array(
            'success' => !$error,
            'error' => $error
        );

    }

    private function generatePassword($length = 10) {

        $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789()_^%&$#+-@!?';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;

    }

    private function stepTemplate($template, $lang){

        $folder = 'theme';

        $layoutsList = Core::getLayoutsFilesList();
        $layouts = array();

        if ($layoutsList){
            foreach($layoutsList as $file){

                $name = pathinfo($file, PATHINFO_FILENAME);
                $name = str_replace('_', ' ', $name);
                $name = str_replace('-', ' ', $name);
                $name = ucfirst($name);

                $layouts[$file] = $name;

            }
        }

        return Layout::renderTemplate($template, array(
            'lang' => $lang,
            'folder' => $folder,
            'layouts' => $layouts,
        ));

    }

    private function validateTemplate($lang){

        $index = Request::get('index_layout');
        $files = Request::get('layout_files');
        $names = Request::get('layout_names');

        $error = false;

        if (!$index || !$files || !$names) { $error = $lang['templateLayoutError']; }

        return array(
            'success' => !$error,
            'error' => $error
        );

    }

    public function save(){

        $lang = Request::get('lang');
        $site_name = Request::get('site_name');
        $site_desc = Request::get('site_desc');
        $site_langs = Request::get('site_langs');
        $admin_email = Request::get('admin_email');
        $admin_password = Request::get('admin_password');
        $index_layout = Request::get('index_layout');
        $layout_files = Request::get('layout_files');
        $layout_names = Request::get('layout_names');

        $langs = explode(',', $site_langs);

        $config = array(
            'dev_mode' => 0,
            'ui_lang' => $lang,
            'default_lang' => $langs[0],
            'langs' => $langs,
            'index_layout' => $index_layout,
            'site_title' => $site_name,
            'site_description' => $site_desc,
            'email' => $admin_email,
            'password_hash' => sha1($admin_email . $admin_password)
        );

        $layouts = array();

        $layout_files_list = explode("\n", $layout_files);
        $layout_names_list = explode("\n", $layout_names);

        foreach($layout_files_list as $i => $file){
            $layouts[$file] = $layout_names_list[$i];
        }

        foreach(array('content', 'layouts') as $dir){
            $dirPath = Core::path('data', $dir);
            if (!file_exists($dirPath)){
                mkdir($dirPath, 0777);
            }
        }

        Core::saveArrayAsJson(Core::path('data', 'config.json'), $config);
        Core::saveLayoutsList($layouts);

        User::login();

        Response::json(array(
            'success' => Config::isInstalled(),
            'next_url' => Core::getLayouterUrl($layout_files_list[0], true)
        ));

        Response::send();

    }

}
