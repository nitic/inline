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
use InlineCMS\Core\Layout;

class Widget {

    private $name, $lang;

    public function __construct($name) {

        $this->name = $name;
        $this->lang = Lang::loadWidgetLang($this->name, Config::get('ui_lang'), 'server');

    }

    public function getName(){
        return $this->name;
    }

    public function isCacheable(){
        return true;
    }

    public function getDirectory(){
        return Core::path('app', 'widgets', $this->name);
    }

    public function lang($phraseId){

        $phrase = isset($this->lang[$phraseId]) ? $this->lang[$phraseId] : $phraseId;

        if (func_num_args() > 1){
            $formatArguments = func_get_args();
            $formatArguments[0] = $phrase;
            $phrase = call_user_func_array('sprintf', $formatArguments);
        }

        return $phrase;
    }

    public function getDefaultOptions(){
        return array();
    }

    public function onEditorInitialize($page){

        $widgetNamespace = explode('\\', get_class($this));
        $widgetName = $widgetNamespace[ count($widgetNamespace)-1 ];

        $jsFileUrl = '/static/widgets/' . mb_strtolower($widgetName) . '.js';
        $jsFilePath = Core::path(str_replace('/', DIRECTORY_SEPARATOR, $jsFileUrl));

        if (file_exists($jsFilePath)){
            Layout::addJs(ROOT_URL . $jsFileUrl);
        }

    }

    public function onEditablePageInitialize($page){
        return;
    }

    public function getContent($page, $regionId, $widgetData){

        return $widgetData['content'];

    }

    public function getEditableContent($page, $regionId, $widgetData){

        return $widgetData['content'];

    }

    public function renderOptionsForm(){

        return $this->renderTemplate('options', $this->getDefaultOptions());

    }

    public function isTemplateExists($template){

		$templateFile = Core::pathRelative($this->getDirectory(), 'templates', "{$template}.tpl.php");

		return file_exists($templateFile);

    }

    public function renderTemplate($template, $data){

        if (!$this->isTemplateExists($template)) { return false; }

        ob_start();

		extract($data);

		$templateFile = Core::pathRelative($this->getDirectory(), 'templates', "{$template}.tpl.php");

		include $templateFile;

		$html = ob_get_clean();

		return $html;

    }

}
