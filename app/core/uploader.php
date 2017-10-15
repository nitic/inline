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

use InlineCMS\Core\Lang;
use InlineCMS\Helpers\Files;

class Uploader {

    private $allowedExtensions = array();
    private $allowedSize = 0;

    public static function getMaxUploadSize(){
        $maxSize = ini_get('upload_max_filesize');
        $maxSize = str_replace('M', 'Мb', $maxSize);
        $maxSize = str_replace('K', 'Kb', $maxSize);
        return $maxSize;
    }

    public function isUploaded($name){
        if (!isset($_FILES[$name])) { return false; }
        if (!$_FILES[$name]['size']) { return false; }
        return true;
    }

    public function setAllowedExtensions($list=array()){
        $this->allowedExtensions = $list;
    }

    public function setAllowedSize($bytes){
        $this->allowedSize = $bytes;
    }

    public function upload($name, $uploadDir = false){

        if (!$uploadDir){
            list($year, $month, $day) = explode('-', date('Y-m-d'));
            $uploadDir = Core::pathUpload($year, $month, $day);
        }

        if ($this->isUploaded($name)){
            return $this->proccessUpload($name, $uploadDir);
        }

        return array(
            'success' => false,
            'error' => Lang::get('uploadErrorNoFile')
        );

    }

    private function proccessUpload($name, $uploadDir){

        $errorCode = $_FILES[$name]['error'];
        $maxSize = $this->getMaxUploadSize();

        $source = $_FILES[$name]['tmp_name'];

        $fileSize = $_FILES[$name]['size'];
        $fileName = basename($_FILES[$name]['name']);
        $fileExt = mb_strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $uploadErrors = array(
            UPLOAD_ERR_INI_SIZE => Lang::get('uploadErrorSize', $maxSize),
            UPLOAD_ERR_FORM_SIZE => Lang::get('uploadErrorSize', $maxSize),
            UPLOAD_ERR_PARTIAL => Lang::get('uploadErrorPartial'),
            UPLOAD_ERR_NO_FILE => Lang::get('uploadErrorNoFile'),
            UPLOAD_ERR_NO_TMP_DIR => Lang::get('uploadErrorTmp'),
            UPLOAD_ERR_CANT_WRITE => Lang::get('uploadErrorPerms'),
            UPLOAD_ERR_EXTENSION => Lang::get('uploadErrorExt')
        );

        if($errorCode !== UPLOAD_ERR_OK && isset($uploadErrors[$errorCode])){
            return array(
                'success' => false,
                'error' => $uploadErrors[$errorCode],
                'name' => $fileName,
            );
        }

        if ($this->allowedExtensions && !in_array($fileExt, $this->allowedExtensions)){
            return array(
                'error' => Lang::get('uploadErrorType', implode(', ', $this->allowedExtensions)),
                'success' => false,
                'name' => $fileName
            );
        }

        if ($this->allowedSize > 0){
            if ($fileSize > $this->allowedSize){
                return array(
                    'error' => Lang::get('uploadErrorSize', Files::getBytesString($allowed_size)),
                    'success' => false,
                    'name' => $fileName
                );
            }
        }

        if (!file_exists($uploadDir)){	@mkdir($uploadDir, 0777, true); }
        if (!is_writable($uploadDir)){	@chmod($uploadDir, 0777); }

        $destination = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
        $fileUrl = ROOT_URL . str_replace(ROOT_PATH, '', $uploadDir) . '/' . $fileName;

        return array(
            'success' => @move_uploaded_file($source, $destination),
            'path'  => $uploadDir,
            'url' => $fileUrl,
            'name' => $fileName,
            'size' => $fileSize,
            'size_formatted' => Files::getBytesString($fileSize)
        );

    }

}
