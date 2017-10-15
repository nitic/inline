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

class Files {

    public static function getBytesString($bytes) {

        $kb = 1024;
        $mb = 1048576;
        $gb = 1073741824;

        if (round($bytes / $gb) > 0) {
            return ceil($bytes / $gb) . ' Gb';
        }

        if (round($bytes / $mb) > 0) {
            return ceil($bytes / $mb) . ' Mb';
        }

        if (round($bytes / $kb) > 0) {
            return ceil($bytes / $kb) . ' Kb';
        }

        return $bytes . ' b';

    }

    public static function isDirectoryEmpty($dir) {

        $handle = opendir($dir);

        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                return false;
            }
        }

        return true;

    }

    public static function removeDirectory($directory, $isClearInsteadOfRemoving=false){

        if(substr($directory,-1) == '/'){
            $directory = substr($directory,0,-1);
        }

        if(!file_exists($directory) || !is_dir($directory) || !is_readable($directory)){
            return false;
        }

        $handle = opendir($directory);

        while (false !== ($node = readdir($handle))){

            if($node != '.' && $node != '..'){

                $path = $directory.'/'.$node;

                if(is_dir($path)){
                    if (!self::removeDirectory($path)) { return false; }
                } else {
                    if(!unlink($path)) { return false; }
                }

            }

        }

        closedir($handle);

        if ($isClearInsteadOfRemoving == false){
            if(!rmdir($directory)){
                return false;
            }
        }

        return true;

    }

    public static function clearDirectory($directory){

        return self::removeDirectory($directory, true);

    }

	public static function removeEmptyDirectories($path){

        $empty = true;

        foreach (glob($path.DIRECTORY_SEPARATOR."*") as $file) {
            $empty &= is_dir($file) && self::removeEmptyDirectories($file);
        }

        return $empty && rmdir($path);

    }

	public static function getDirContents($dir, &$results = array()){
		$files = scandir($dir);

		foreach($files as $file){
			$path = realpath($dir.DIRECTORY_SEPARATOR.$file);
			if(!is_dir($path)) {
				$results[] = $path;
			} else if(is_dir($path) && $file != "." && $file != "..") {
				self::getDirContents($path, $results);
				$results[] = $path;
			}
		}

		return $results;

	}

}
