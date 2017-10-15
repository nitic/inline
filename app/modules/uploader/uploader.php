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

namespace InlineCMS\Modules\Uploader;

use InlineCMS\Core\Core;
use InlineCMS\Core\Module;
use InlineCMS\Core\Request;
use InlineCMS\Core\Response;
use InlineCMS\Core\User;

class Uploader extends Module {

	public function _before() {
		parent::_before();
		if (!User::isLogged()) { Core::errorAuth(); }
	}

    public function upload(){

        $uploader = new \InlineCMS\Core\Uploader();

        $result = $uploader->upload('file');

        unset($result['path']);

        Response::sendJson($result);

    }

	public function uploadImage(){

        $isResize = Request::has('resize');
        $width = intval(Request::get('width', 1000));
        $height = intval(Request::get('height', 1000));

        $uploader = new \InlineCMS\Core\Uploader();

        $uploader->setAllowedExtensions(array(
           'jpg', 'jpeg', 'png', 'gif', 'bmp'
        ));

        $result = $uploader->upload('image');

        if (!$isResize || !$result['success']){
            unset($result['path']);
            Response::sendJson($result);
        }

        $size = "{$width}x{$height}";
        list($year, $month, $day) = explode('-', date('Y-m-d'));
        $thumbDir = Core::pathUpload('thumbs', $size, $year, $month, $day);

        if (!file_exists($thumbDir)){ @mkdir($thumbDir, 0777, true); }

        $sourceFile = $result['path'] . DIRECTORY_SEPARATOR . $result['name'];
        $resizedFile = $thumbDir . DIRECTORY_SEPARATOR . $result['name'];
        $resizedUrl = ROOT_URL . str_replace(ROOT_PATH, '', $thumbDir) . '/' . $result['name'];

        \InlineCMS\Loader::loadLibrary('zebraimage.php');

        $image = new \Zebra_Image();

        $image->source_path = $sourceFile;
        $image->target_path = $resizedFile;

        $mode = ZEBRA_IMAGE_CROP_CENTER;

        if (!$image->resize($width, $height, $mode)){
            unset($result['path']);
            Response::sendJson(array(
                'success' => true,
                'url' => $result['url']
            ));
        }

        @unlink($sourceFile);

        Response::sendJson(array(
            'success' => true,
            'url' => $resizedUrl
        ));

	}

    public function uploadAndResize(){

        $width = intval(Request::get('w', 150));
        $height = intval(Request::get('h', 150));
        $isSquare = intval(Request::get('s', 0));

        if ($isSquare){
            $width = $height = min(array($width, $height));
        }

        $size = "{$width}x{$height}";

        $uploader = new \InlineCMS\Core\Uploader();

        $uploader->setAllowedExtensions(array(
           'jpg', 'jpeg', 'png', 'gif', 'bmp'
        ));

        list($year, $month, $day) = explode('-', date('Y-m-d'));

        $uploadDir = Core::pathUpload($year, $month, $day);
        $thumbDir = Core::pathUpload('thumbs', $size, $year, $month, $day);

        $result = $uploader->upload('image', $uploadDir);

        if (!$result['success']){
            Response::sendJson($result);
        }

        if (!file_exists($thumbDir)){ @mkdir($thumbDir, 0777, true); }

        $sourceFile = $result['path'] . DIRECTORY_SEPARATOR . $result['name'];
        $resizedFile = $thumbDir . DIRECTORY_SEPARATOR . $result['name'];
        $resizedUrl = ROOT_URL . str_replace(ROOT_PATH, '', $thumbDir) . '/' . $result['name'];

        \InlineCMS\Loader::loadLibrary('zebraimage.php');

        $image = new \Zebra_Image();

        $image->source_path = $sourceFile;
        $image->target_path = $resizedFile;

        $mode = $isSquare ? ZEBRA_IMAGE_CROP_CENTER : ZEBRA_IMAGE_NOT_BOXED;

        if (!$image->resize($width, $height, $mode)){
            Response::sendJson(array('success' => false));
        }

        Response::sendJson(array(
            'images' => array(
                array(
                    'url' => $result['url'],
                    'thumb_url' => $resizedUrl,
                    'title' => pathinfo($result['name'], PATHINFO_FILENAME),
                    'width' => $width,
                    'height' => $height,
                )
            )
        ));

    }

}
