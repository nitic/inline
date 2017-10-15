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

namespace InlineCMS\Widgets\Gallery;

use InlineCMS\Core\Widget;
use InlineCMS\Core\Layout;

class Gallery extends Widget {

    protected $defaultThumbnailSize = 150;

    public function getContent($page, $regionId, $widgetData) {

        $galleryId = $widgetData['domId'] . '-gallery';

		Layout::addJs(ROOT_URL . '/static/jquery/lightbox/lightbox.js');
		Layout::addJs(ROOT_URL . '/static/client/gallery.js');

		return $widgetData['content'];

	}

}
