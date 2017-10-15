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
use InlineCMS\Core\Layout;
use InlineCMS\Core\Lang;

class Page {

    private $name = '';
    private $title = '';
    private $slug = '';
    private $layoutName = '';

    private $meta = array(
        'keywords' => '',
        'description' => ''
    );

	private $lang;

    private $widgets = array();
    private $collections = array();

    public function __construct($lang, $uri = '/') {

		$this->lang = $lang;

        $this->detectNameAndSlug($uri);

    }

    private function detectNameAndSlug($uri){

        $this->slug = '';

        if ($uri == '/') {
            $this->name = 'index';
            return;
        }

        $uriSegments = explode('/', trim($uri, '/'));
        $segmentsCount = count($uriSegments);
        $lastSegmentIndex = $segmentsCount - 1;

        $this->name = $uriSegments[$lastSegmentIndex];

        if ($segmentsCount == 1){
            return;
        }

        unset($uriSegments[$lastSegmentIndex]);

        $this->slug = implode('/', $uriSegments);

    }

	public function getLevel(){

		if (!$this->slug){ return 1; }

		return substr_count($this->slug, '/') + 2;

	}

    public function setName($name){
        $this->name = $name;
        return $this;
    }

    public function getName(){
        return $this->name;
    }

    public function setTitle($title){
        $this->title = $title;
        return $this;
    }

    public function getTitle(){
        return $this->title;
    }

    public function setMeta($meta, $value=false){
        if (is_array($meta)){
            $this->meta = array_merge($this->meta, $meta);
        } else if (is_string($meta)) {
            $this->meta[$meta] = $value;
        }
        return $this;
    }

    public function getMeta($key=false){
        if ($key){
            return isset($this->meta[$key]) ? $this->meta[$key] : false;
        }
        return $this->meta;
    }

    public function setLang($lang){
        $this->lang = $lang;
        return $this;
    }

    public function getLang(){
        return $this->lang;
    }

    public function setSlug($slug){
        $this->slug = $slug;
        return $this;
    }

    public function getSlug(){
        return $this->slug;
    }

    public function isRoot(){
        return $this->slug == '';
    }

    public function getUri(){
        return $this->isRoot() ? $this->name : $this->slug . '/' . $this->name;
    }

    public function setLayoutName($layoutName){
        $this->layoutName = $layoutName;
        return $this;
    }

    public function getLayoutName(){
        return $this->layoutName;
    }

    public function setWidgets($widgets){
        $this->widgets = $widgets;
        return $this;
    }

    public function getWidgets(){
        return $this->widgets;
    }

    public function getRegionWidgets($regionId){
        if ($this->isRegionEmpty($regionId)) { return array(); }
        return $this->widgets[$regionId];
    }

    public function setRegionWidgets($regionId, $widgets){
        $this->widgets[$regionId] = $widgets;
    }


    public function setCollections($collections){
        $this->collections = $collections;
        return $this;
    }

    public function getCollections(){
        return $this->collections;
    }

    public function getCollection($regionId){
        if ($this->isCollectionEmpty($regionId)) { return array(); }
        return $this->collections[$regionId];
    }

    public function isCollectionEmpty($regionId){
        return empty($this->collections[$regionId]);
    }

    public function isRegionExists($regionId){
        return isset($this->widgets[$regionId]) || isset($this->collections[$regionId]);
    }

    public function isRegionFilled($regionId){
        return !empty($this->widgets[$regionId]);
    }

    public function isRegionEmpty($regionId){
        return !$this->isRegionFilled($regionId);
    }

    private function createFile(){

        $jsonFileDir = $this->getFilesDir();
        $jsonFilePath = $this->getJsonFilePath();

        if (!file_exists($jsonFileDir)){
            mkdir($jsonFileDir, 0777, true);
        }

		$json = $this->getJson();

		if (@file_put_contents($jsonFilePath, $json) === false){
            throw new \Exception(Lang::get('errorPageCreate'));
        }

    }

	public function create($isClear=false){

        $themeSchemeJson = Layout::getLayoutJson($this->layoutName);

        $this->widgets = array();

        if (!empty($themeSchemeJson['regions'])){
            foreach($themeSchemeJson['regions'] as $region){

                $this->createRegion($region, $isClear);

            }
        }

		$this->createFile();

	}

    public function createRegion($region, $isClear=false){

        if ($region['is_collection']) {

            if ($region['is_global']){
                $this->collections[ $region['id'] ] = 'global';
            } else {
                $this->collections[ $region['id'] ] = $region['content'];
            }

            return;

        }

        if ($region['is_global']){

            $this->widgets[ $region['id'] ] = 'global';

        } else {

            $this->widgets[ $region['id'] ][] = array(
                'id' => 0,
                'handler' => 'text',
                'options' => array(),
                'content' => $isClear ? '' : $region['content']
            );

        }

    }

    public function hasChildren(){

        $childrenDir = $this->getFilesDir() . $this->name;

        if (!file_exists($childrenDir) || !is_dir($childrenDir)){ return false; }

        return true;

    }

    public function getChildren(){

        $childrenDir = $this->getFilesDir() . ($this->slug ? DIRECTORY_SEPARATOR . $this->name : $this->name);

        if (!file_exists($childrenDir) || !is_dir($childrenDir)){ return false; }

        $children = array();

        foreach(glob($childrenDir . DIRECTORY_SEPARATOR . '*.json') as $file){

            $childUri = $this->getUri() . '/' . pathinfo($file, PATHINFO_FILENAME);

            $childPage = new Page($this->lang, $childUri);

            $children[] = $childPage;

        }

        return $children;

    }

    public function moveTo($uri){

        $children = $this->getChildren();

        if ($children){
            foreach($children as $childPage){
                $childPage->load();
                $childPage->moveTo($uri . '/' . $childPage->name);
            }
        }

        unlink($this->getJsonFilePath());

        if ($this->isCacheExists()){
            unlink($this->getCacheFilePath());
        }

        $this->detectNameAndSlug($uri);

        $this->createFile();

    }

    public function save(){

		$globalRegions = Layout::getGlobalRegions($this->lang);
		$structureJson = $this->loadJson();

		foreach($structureJson['widgets'] as $regionId=>$widgets){
			if (is_string($widgets) && $widgets == 'global'){
				$globalRegions[$regionId] = $this->getRegionWidgets($regionId);
				$this->widgets[$regionId] = 'global';
			}
		}

		foreach($structureJson['collections'] as $regionId=>$collection){
			if (is_string($collection) && $collection == 'global'){
				$globalRegions[$regionId] = $this->getCollection($regionId);
				$this->collections[$regionId] = 'global';
			}
		}

		Layout::saveGlobalRegions($globalRegions, $this->lang);

		$jsonFilePath = $this->getJsonFilePath();
        $json = $this->getJson();

        if(@file_put_contents($jsonFilePath, $json) === false){
            throw new \Exception(Lang::get('pageSaveError'));
        }

    }

    public function load(){

        $json = $this->loadJson();

        $this->name = $json['name'];
        $this->title = $json['title'];
        $this->meta = $json['meta'];
        $this->slug = $json['slug'];
        $this->layoutName = $json['layout'];

		$globalRegions = Layout::getGlobalRegions($this->lang);

		$widgetsList = $json['widgets'];

		foreach($widgetsList as $regionId=>$widgets){
			if (is_string($widgets) && $widgets == 'global'){
				$widgetsList[$regionId] = $globalRegions[$regionId];
			}
		}

        $this->widgets = $widgetsList;

		$collectionsList = $json['collections'];

        if (is_array($collectionsList)){
            foreach($collectionsList as $regionId=>$items){
                if (is_string($items) && $items == 'global'){
                    $collectionsList[$regionId] = $globalRegions[$regionId];
                }
            }
        }

        $this->collections = $collectionsList;

    }

    public function isExists($lang=false){

        if (!$lang) { $lang = $this->lang; }

        $jsonFilePath = $this->getJsonFilePath($lang);

        return file_exists($jsonFilePath);

    }

	public function isCacheExists(){

		$cacheFilePath = $this->getCacheFilePath();

		return file_exists($cacheFilePath);

	}

    public function isCacheValid(){

        $cacheFilePath = $this->getCacheFilePath();

        $cacheTime = filemtime($cacheFilePath);
        $globalsChangedTime = filemtime(Core::path('data', 'content', $this->lang, 'globals.json'));
        $menusChangedTime = filemtime(Core::path('data', 'content', $this->lang, 'menus.json'));

        $globalCodeFile = Core::path('data', 'globalcode.json');
        $codeChangedTime = file_exists($globalCodeFile) ? filemtime($globalCodeFile) : 0;

        if ($cacheTime < $globalsChangedTime ||
                $cacheTime < $menusChangedTime ||
                ($codeChangedTime && ($cacheTime < $codeChangedTime))){
            return false;
        }

        return true;

    }

	public function saveCache($html){

		$cacheFilePath = $this->getCacheFilePath();

		file_put_contents($cacheFilePath, $html);

	}

	public function getCache(){

		$cacheFilePath = $this->getCacheFilePath();

		return file_get_contents($cacheFilePath);

	}

	public function deleteCache(){

		if (!$this->isCacheExists()) { return; }

		unlink($this->getCacheFilePath());

	}

    public function delete(){

        $children = $this->getChildren();

        if ($children){
            foreach($children as $childPage){
                $childPage->delete();
            }
        }

        if (@unlink($this->getJsonFilePath()) === false){
            throw new \Exception(Lang::get('errorPageDelete'));
        }

        if ($this->isCacheExists()){
            if (@unlink($this->getCacheFilePath()) === false){
                throw new \Exception(Lang::get('errorPageDelete'));
            }
        }

    }

	private function getFilesDir($lang=false){

        if (!$lang) { $lang = $this->lang; }

        $slugPath = empty($this->slug) ? '' : str_replace('/', DIRECTORY_SEPARATOR, $this->slug);

        $jsonFileDir = Core::path('data', 'content', $lang, 'pages', $slugPath);

        return $jsonFileDir;

    }

    private function getJsonFilePath($lang=false){

		$jsonFilePath = $this->getFilesDir($lang) . DIRECTORY_SEPARATOR . "{$this->name}.json";

        return $jsonFilePath;

    }

	private function getCacheFilePath($lang=false){

		$cacheFilePath = $this->getFilesDir($lang) . DIRECTORY_SEPARATOR . "{$this->name}.html";

        return $cacheFilePath;

	}

    private function loadJson(){

        $jsonFilePath = $this->getJsonFilePath();

        if (!file_exists($jsonFilePath)) { return array(); }

        $json = file_get_contents($jsonFilePath);

        return json_decode($json, true);

    }

    public function getJson(){

        return json_encode(array(
            'name' => $this->name,
            'title' => $this->title,
            'slug' => $this->slug,
			'uri' => $this->getUri(),
			'lang' => $this->lang,
			'meta' => $this->meta,
            'layout' => $this->layoutName,
            'widgets' => $this->widgets,
            'collections' => $this->collections
        ));

    }

}
