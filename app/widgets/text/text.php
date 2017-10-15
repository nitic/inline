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

namespace InlineCMS\Widgets\Text;

use InlineCMS\Core\Widget;
use InlineCMS\Core\Layout;

class Text extends Widget {

    public function onEditablePageInitialize($page){

        Layout::addJs(ROOT_URL . '/static/editor/ckeditor.js');

    }

}
