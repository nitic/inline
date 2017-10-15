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

namespace InlineCMS\Widgets\Form;

use InlineCMS\Core\Core;
use InlineCMS\Core\Config;
use InlineCMS\Core\Lang;
use InlineCMS\Core\Widget;
use InlineCMS\Core\Request;
use InlineCMS\Core\Response;
use InlineCMS\Core\Mailer;

class Form extends Widget {

    public function isCacheable(){
        return false;
    }

    public function getContent($page, $regionId, $widgetData) {

		$domId = $widgetData['domId'];
		$options = $widgetData['options'];

        $submittedFormId = Request::get('form_id', false);

        $formId = md5($domId);

        if (!$submittedFormId || $submittedFormId != $formId){

            $shortId = mb_substr($formId, 0, 8);
            $sentId = Request::get('sent');

            if (!$sentId || $sentId != $shortId){
                return $widgetData['content'];
            }

            if ($sentId == $shortId){
                return '<div class="form-thanks-message">' . nl2br($options['thanks_msg']) . '</div>';
            }

        }

        $fields = Request::get('fields');

        if (!$fields) {
            return $widgetData['content'];
        }

        return $this->proccessForm($domId, $options, $fields);

	}

    private function proccessForm($id, $options, $values){

        $errors = array();
        $data = array();

        foreach($options['fields'] as $index => $field){

            $value = isset($values[$index]) ? $values[$index] : '';

            if ($field['isMandatory'] && empty($value)) {
                $errors[$index] = true;
                continue;
            }

            if ($field['type'] == 'email' && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)){
                $errors[$index] = true;
                continue;
            }

            if ($field['type'] == 'checkbox'){
                $value = $value ? Lang::get('yes') : Lang::get('no');
            }

            $data[] = array(
                'title' => $field['title'],
                'value' => $value
            );

        }

        if ($errors){
            return $this->renderForm($id, $options, $values, $errors);
        }

        $this->sendForm($options, $data);

        $sentId = mb_substr(md5($id), 0, 8);

        Core::redirect("?sent={$sentId}");

    }

    private function sendForm($options, $data){

        $mailer = new Mailer();

        if ($options['email_type'] == 'default'){
            $mailer->addTo(Config::get('email'));
        } else {
            $mailer->addTo($options['email']);
        }

        $subject = empty($options['subject']) ? Lang::get('formMessageSubject') : $options['subject'];

        $mailer->setSubject($subject);

        $letter = '';

        foreach($data as $field){
            if (!$field['value']) { continue; }
            $letter .= "<p><strong>{$field['title']}:</strong><br/>{$field['value']}</p>";
        }

        $mailer->setBodyHTML($letter);

        $mailer->send();

    }

    public function buildForm(){

        $id = Request::get('id');
        $options = json_decode(Request::get('options'), true);

        Response::sendJson(array(
            'success' => true,
            'html' => $this->renderForm($id, $options)
        ));

    }

    private function renderForm($id, $options, $values=array(), $errors=array()){

        return $this->renderTemplate('form', array(
            'form_id' => md5($id),
            'options' => $options,
            'values' => $values,
            'errors' => $errors
        ));

    }

}
