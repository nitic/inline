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

namespace InlineCMS\Widgets\Map;

use InlineCMS\Core\Core;
use InlineCMS\Core\Widget;
use InlineCMS\Core\Layout;

class Map extends Widget {

	public function getContent($page, $regionId, $widgetData) {

		$this->addApiJs();

		$domId = $widgetData['domId'] . '-map';
		$opts = $widgetData['options'];

		Layout::addScript('new google.maps.Marker({
                position: new google.maps.LatLng('.$opts['lat'].', '.$opts['lng'].'),
                title: "' . (empty($opts['title']) ? '' : $opts['title']) .'",
                map: new google.maps.Map(document.getElementById("'.$domId.'"), {
                    center: new google.maps.LatLng('.$opts['lat'].', '.$opts['lng'].'),
                    zoom: '.intval($opts['zoom']).'
                })
            });');

		return $widgetData['content'];

	}

	private function addApiJs(){
		Layout::addJs('http://maps.googleapis.com/maps/api/js?language=' . Core::getCurrentLang());
	}

}
