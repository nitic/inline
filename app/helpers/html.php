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

namespace InlineCMS\Helpers;

use InlineCMS\Helpers\Strings;

class Html {

    public static function urlStartsWithProtocol($url) {

        return preg_match("~^(?:f|ht)tps?://~i", $url) || Strings::startsWith($url, '//');

    }

    public static function addThemeUrlToAttribute($attribute, $html){

        $attributes = array();

        preg_match_all('/'.$attribute.'="([^"]+)"/i', $html, $attributes);

        if (!$attributes){ return $html; }

        foreach($attributes[0] as $index => $attributeHtml){

            $attributeValue = $attributes[1][$index];

            if (!$attributeValue || Strings::startsWith($attributeValue, '#') || $attributeValue == '/') { continue; }
            if (self::urlStartsWithProtocol($attributeValue)){ continue; }

            if ($attributeValue == 'index.html' || $attributeValue == '/') {
                $attributeValue = '{insert:root}';
            } else {
                $attributeValue = '{insert:root}/theme/' . ltrim($attributeValue, '/');
            }

            $newAttributeHtml = "{$attribute}=\"{$attributeValue}\"";

            $html = str_replace($attributeHtml, $newAttributeHtml, $html);

        }

        return $html;

    }

}
