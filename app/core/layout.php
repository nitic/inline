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

use InlineCMS\Loader;
use InlineCMS\Core\Core;
use InlineCMS\Core\Lang;
use InlineCMS\Core\Request;
use InlineCMS\Helpers\Html;
use InlineCMS\Helpers\Url;

class Layout {

    private static $insertions = array(
		'head' => array(),
		'body' => array()
	);

	public static function addInsertion($to, $tag, $isUnique=true){

		if ($isUnique){
			$hash = md5($tag);
			self::$insertions[$to][$hash] = $tag;
		} else {
			self::$insertions[$to][] = $tag;
		}

	}

    public static function addCss($url){

		$tag = '<link rel="stylesheet" type="text/css" href="'.$url.'">';
        self::addInsertion('head', $tag);

    }

    public static function addJs($url){

		$tag = '<script type="text/javascript" src="'.$url.'"></script>';
		self::addInsertion('body', $tag);

    }

    public static function addScript($script){

        $tag = '<script>'.$script.'</script>';
		self::addInsertion('body', $tag, false);

    }

    public static function addHtml($html){

        self::addInsertion('body', $html);

    }

    public static function createLayoutScheme($layoutFile){

        Loader::loadLibrary('ganon.php');

        $layoutFilePath = Core::path('data', 'layouts', $layoutFile);
        if (!file_exists($layoutFilePath)) { self::prepareLayoutFile($layoutFile); }

        $html = file_get_contents($layoutFilePath);

        $scheme = array(
            'jquery' => 0,
            'menus' => array(),
            'regions' => array()
        );

        $dom = str_get_dom($html);

        $document = $dom('html', 0);

        if (isset($document->attributes['data-jquery'])){
            $scheme['jquery'] = $document->attributes['data-jquery'];
            $document->deleteAttribute('data-jquery');
        }

        $menus = $dom('*[data-menu]');

        foreach($menus as $index=>$menuElement){

            $menuId = $menuElement->attributes['data-menu'];
            if (!$menuId) { $menuId = 'menu' . ($index+1); }

			$menuItemsHtml = array();

			$menuItems = array(
				'regular' => $menuElement(":element", 0),
				'selected' => $menuElement("[data-selected]", 0)
			);

			if ($menuItems['regular'] === $menuItems['selected']){
				$menuItems['regular'] = $menuItems['selected']->getNextSibling();
			}

			if (!$menuItems['regular']) { continue; }

			foreach ($menuItems as $type => $menuItem){

				if (!$menuItem) { continue; }

                $subMenu = $menuItem('ul', 0);
                if ($subMenu) { $subMenu->delete(); }

				$menuItemLink = $menuItem('a', 0);

				if (!$menuItemLink && $menuItem->tag) {
						$menuItemLink = $menuItem;
				}

				$menuItemLink->href = '{item:href}';
				$menuItemLink->setInnerText('{item:title}');

                if (!isset($menuItemLink->target)){
                    $menuItemLink->addAttribute('target', '{item:target}');
                } else {
                    $menuItemLink->target = '{item:target}';
                }

				if ($type == 'selected'){
					$menuItem->deleteAttribute('data-selected');
				}

				$menuItemsHtml[$type] = $menuItem->html();

			}

            $menuElement->setInnerText("{menu:{$menuId}}");

            $scheme['menus'][$menuId] = array(
                'id' => $menuId,
                'item' => $menuItemsHtml['regular'],
                'selected_item' => empty($menuItemsHtml['selected']) ? false : $menuItemsHtml['selected'],
            );

        }

        $regions = $dom('*[data-region]');

        foreach($regions as $regionElement){

            $regionId = $regionElement->attributes['data-region'];
			$regionHtml = str_replace(array("\r", "\t", "\n"), '', trim($regionElement->getInnerText()));

            $isGlobalRegion = isset($regionElement->attributes['data-global']);
            $isFixedRegion = isset($regionElement->attributes['data-fixed']);
            $isCollectionRegion = isset($regionElement->attributes['data-collection']);

            $regionElement->deleteAttribute('data-region');
            $regionElement->deleteAttribute('data-global');
            $regionElement->deleteAttribute('data-fixed');

            if ($isCollectionRegion){
                $regionChildsHtml = array();
                for($i=0; $i < $regionElement->childCount(); $i++){
                    $childElement = $regionElement->getChild($i);
                    $childHtml = str_replace(array("\r", "\t", "\n"), '', trim($childElement->html()));
                    if (!$childHtml) { continue; }
                    $regionChildsHtml[] = $childHtml;
                }
            }

            $regionElement->setInnerText("{region:{$regionId}}");

			$region = array(
                'id' => $regionId,
                'content' => $isCollectionRegion ? $regionChildsHtml : $regionHtml,
                'is_global' => $isGlobalRegion,
                'is_fixed' => $isFixedRegion,
                'is_collection' => $isCollectionRegion,
            );

			if ($isGlobalRegion){
				$scheme['globals'][$regionId] = $region;
				unset($region['content']);
			}

			$scheme['regions'][$regionId] = $region;

        }

        $htmlElement = $dom('html', 0);
        $htmlElement->lang = '{insert:lang}';

        $headElement = $dom('head', 0);
        $titleElement = $headElement('title', 0);
        $titleElement->setInnerText('{insert:title}');

        $head = $dom('head', 0);
        $body = $dom('body', 0);

        $metaKeys = $head('meta[name=keywords]', 0);

        if ($metaKeys){
            $metaKeys->attributes['content'] = '{meta:keywords}';
        } else {
            $head->addText("\t" . '<meta name="keywords" content="{meta:keywords}">' . "\n");
        }

        $metaDesc = $head('meta[name=description]', 0);

        if ($metaDesc){
            $metaDesc->attributes['content'] = '{meta:description}';
        } else {
            $head->addText("\t" . '<meta name="description" content="{meta:description}">' . "\n");
        }

        $head->addText("\t" . '{insert:head}' . "\n");
        $body->addText("\t" . '{insert:body}' . "\n");

		$scheme['dom'] = $dom;

        return $scheme;

    }

    public static function getLayoutScheme($layoutFile){

		$schemeFilePath = Core::path('data', 'layouts', "{$layoutFile}.scheme");

        return Core::loadFileContents($schemeFilePath);

    }

    public static function getLayoutJson($layoutFile){

		$jsonFilePath = Core::path('data', 'layouts', "{$layoutFile}.json");

		return Core::loadArrayFromJson($jsonFilePath);

    }

	public static function saveGlobalRegions($regions, $lang=false){

        if ($lang){
            $globalRegionsFilePath = Core::path('data', 'content', $lang, 'globals.json');
            Core::saveArrayAsJson($globalRegionsFilePath, $regions);
            return;
        }

        $langs = Config::get('langs');

        foreach($langs as $lang){
            if (!isset($regions[$lang])){ $regions[$lang] = array(); }
            self::saveGlobalRegions($regions[$lang], $lang);
        }

	}

	public static function getGlobalRegions($lang=false){

        if ($lang){
            $globalRegionsFilePath = Core::path('data', 'content', $lang, 'globals.json');
            return Core::loadArrayFromJson($globalRegionsFilePath);
        }

        $langs = Config::get('langs');
        $regions = array();

        foreach($langs as $lang){
            $regions[$lang] = self::getGlobalRegions($lang);
        }

        return $regions;

	}

	private static function renderMenus($html, $layoutJson, $page){

		$menusStructure = Core::getMenus($page->getLang());

		foreach($layoutJson['menus'] as $menuId=>$menuTemplate){

			$menuItemsHtml = array();

			foreach($menusStructure[$menuId] as $item){

				$url = $item['type'] == 'page' ? Url::get($item['url'], $page->getLang()) : $item['url'];
				$isActive = Request::isUrl($url) && !empty($menuTemplate['selected_item']);

				$template = $isActive ? $menuTemplate['selected_item'] : $menuTemplate['item'];

                $itemHtml = self::renderMenuItem($template, $url, $item);

				$menuItemsHtml[] = "\t" . $itemHtml;

			}

			$menuHtml = implode("\n", $menuItemsHtml);
			$html = str_replace("{menu:{$menuId}}", $menuHtml, $html);

		}

		return $html;

	}

    public static function renderMenuItem($template, $url, $item){

        $html = $template;

        $target = !empty($item['target']) ? $item['target'] : '_self';

        $html = str_replace('{item:title}', $item['title'], $html);
        $html = str_replace('{item:href}', $url, $html);
        $html = str_replace('{item:target}', $target, $html);

        return $html;

    }

    public static function renderPage($page){

		if (!Config::is('dev_mode') && $page->isCacheExists() && $page->isCacheValid()){
			return $page->getCache();
		}

        $html = self::getLayoutScheme($page->getLayoutName());
        $layoutJsonScheme = self::getLayoutJson($page->getLayoutName());

        self::addCss(ROOT_URL . '/static/cms/css/client.css');

        if (empty($layoutJsonScheme['jquery'])){
            self::addJs(ROOT_URL . '/static/jquery/jquery.js');
        }

		$html = self::renderMenus($html, $layoutJsonScheme, $page);

		$widgetsObjects = Core::getWidgetsObjects();

        $isCacheable = true;

        if (!empty($layoutJsonScheme['regions'])){
            foreach($layoutJsonScheme['regions'] as $regionId => $region){

                if (!$page->isRegionExists($regionId)){
                    $page->createRegion($region);
                    $page->save();
                    $page->load();
                }

                $regionHtml = self::getStaticRegionHtml($page, $region, $widgetsObjects);

                $html = str_replace("{region:{$regionId}}", $regionHtml, $html);

                foreach($page->getRegionWidgets($region['id']) as $widgetData){
                    $isCacheable = $isCacheable && $widgetsObjects[$widgetData['handler']]->isCacheable();
                }

            }
        }

        $globalCode = Core::getGlobalCode();

        if (!empty($globalCode['html'])){
            self::addHtml($globalCode['html']);
        }
        

        $html = self::replaceInsertions($html, $page);

        if ($isCacheable){
            $page->saveCache($html);
        }

        return $html;

    }

    public static function renderPageEditor($page){

		$widgetsObjects = Core::getWidgetsObjects();

		foreach($widgetsObjects as $widget){
			$widget->onEditorInitialize($page);
		}

        $pageLangs = array();
        $isHaveAllLangs = true;

        foreach (Config::get('langs') as $lang) {
            $isLangExists = $lang == $page->getLang() || $page->isExists($lang);
            $langTitle = strtoupper($lang);
            $isNewLang = false;
            if (!$isLangExists) {
                $langTitle = sprintf(Lang::get('langCreatePage'), $langTitle);
                $isHaveAllLangs = false;
                $isNewLang = true;
            }
            $pageLangs[$lang] = array(
                'title' => $langTitle,
                'isNew' => $isNewLang
            );
        }

        $menus = Core::getMenus(Core::getCurrentLang());

        return self::renderTemplate('editor/editor', array(
            'page' => $page,
            'pageLangs' => $pageLangs,
            'isHaveAllLangs'=> $isHaveAllLangs,
            'menus' => $menus,
            'options' => array(
                'lang' => Config::get('ui_lang'),
                'defaultLang' => Config::get('default_lang'),
                'backendUrl' => Core::getBackendUrl(),
                'editorUrl' => Core::getEditorUrl($page),
                'pageUri' => $page->getUri(),
                'pageLang' => Core::getCurrentLang(),
                'rootUrl' => ROOT_URL,
                'widgetsList' => Core::getWidgetsList()
    		)
        ));

    }

    public static function renderEditablePage($page){

        $widgetsObjects = Core::getWidgetsObjects();

        foreach($widgetsObjects as $widget){
			$widget->onEditablePageInitialize($page);
		}

		self::addCss(ROOT_URL . '/static/font-awesome/css/font-awesome.css');
        self::addCss(ROOT_URL . '/static/cms/css/client.css');
        self::addCss(ROOT_URL . '/static/cms/css/editor-inject.css');

        $html = self::getLayoutScheme($page->getLayoutName());
        $layoutJson = self::getLayoutJson($page->getLayoutName());

		$html = self::renderMenus($html, $layoutJson, $page);

        if (!empty($layoutJson['regions'])){

            foreach($layoutJson['regions'] as $regionId => $region){

                if (!$page->isRegionExists($regionId)){
                    $page->createRegion($region);
                    $page->save();
                    $page->load();
                }

                $regionHtml = self::getEditableRegionHtml($page, $region, $widgetsObjects);

                $html = str_replace("{region:{$regionId}}", $regionHtml, $html);

            }

        }

        $html = self::replaceInsertions($html, $page);

        return $html;

    }

    private static function replaceInsertions($rawHtml, $page){

        $html = str_replace(array(
            '{insert:root}',
            '{insert:lang}',
            '{insert:title}',
            '{meta:keywords}',
            '{meta:description}'
        ), array(
            ROOT_URL,
            htmlspecialchars($page->getLang()),
            htmlspecialchars($page->getTitle()),
            htmlspecialchars($page->getMeta('keywords')),
            htmlspecialchars($page->getMeta('description')),
        ), $rawHtml);

		foreach(self::$insertions as $to => $tags){

			$tags = implode("\n", array_map(function($tag){
				return "\t" . $tag;
			}, $tags));

			$html = str_replace('{insert:'.$to.'}', $tags, $html);

		}

        return $html;

    }

    private static function getStaticRegionHtml($page, $region, $widgetsObjects){

        $html = '';

        if ($region['is_collection']){
            foreach($page->getCollection($region['id']) as $collectionItem){
                $html .= $collectionItem;
            }
            return $html;
        }

        foreach($page->getRegionWidgets($region['id']) as $widgetData){

			$widget = $widgetsObjects[$widgetData['handler']];

			$widgetData['domId'] = 'inlinecms-widget-'.$region['id'] . $widgetData['id'];

			$domClasses = implode(' ', array(
				'inlinecms-widget',
				'inlinecms-widget-' . $widgetData['handler'],
			));

            $widgetHtml = $widget->getContent($page, $region['id'], $widgetData);

            if (!$widgetHtml) { continue; }

            if ($region['is_fixed']){
                $html .= $widgetHtml;
            } else {
                $html .= '<div class="'.$domClasses.'">';
                    $html .= $widgetHtml;
                $html .= '</div>';
            }

        }

        return $html;

    }

    private static function getEditableRegionHtml($page, $region, $widgetsObjects){

        $regionDomClasses = array('inlinecms-region');

        if ($region['is_fixed']) { $regionDomClasses[] = 'inlinecms-region-fixed'; }

        $regionDomClasses = implode(' ', $regionDomClasses);

        $html = '';

        if ($region['is_collection']){
            foreach($page->getCollection($region['id']) as $collectionItem){
                $html .= $collectionItem;
            }
            return $html;
        }

        $html = '<div class="'.$regionDomClasses.'" data-region-id="'.$region['id'].'">';

        foreach($page->getRegionWidgets($region['id']) as $widgetData){

            $widget = $widgetsObjects[$widgetData['handler']];

			$widgetDomClasses = implode(' ', array(
				'inlinecms-widget',
				'inlinecms-widget-' . $widgetData['handler'],
			));

			$widgetData['domId'] = 'inlinecms-widget-'.$region['id'] . $widgetData['id'];

            $html .= '<div class="'.$widgetDomClasses.'" id="'.$widgetData['domId'].'" data-id="'.$widgetData['id'].'">';
                $html .= '<div class="inlinecms-content">';
                    $html .= $widget->getEditableContent($page, $region['id'], $widgetData);
                $html .= '</div>';
            $html .= '</div>';

        }

        $html .= '</div>';

        return $html;

    }

	public static function renderTemplate($template, $data=array()){

		ob_start();

		extract($data);

        $template = str_replace('/', DIRECTORY_SEPARATOR, $template);

		include Core::path('app', 'templates', "{$template}.tpl.php");

		$html = ob_get_clean();

		return $html;

	}

	public static function isWidgetTemplateExists($handler, $template){

		$templateFile = Core::path('app', 'widgets', $handler, 'templates', "{$template}.tpl.php");

		return file_exists($templateFile);

	}

    private static function clearLayoutMarkup($layoutDom){

        $layoutDom('html', 0)->deleteAttribute('data-jquery');

        foreach($layoutDom('*[data-region]') as $regionElement){
            $regionElement->deleteAttribute('data-region');
            $regionElement->deleteAttribute('data-global');
            $regionElement->deleteAttribute('data-fixed');
        }

        foreach($layoutDom('*[data-menu]') as $menuElement){

            $menuElement->deleteAttribute('data-menu');

            foreach($menuElement('*[data-selected]') as $selectedItem){
                $selectedItem->deleteAttribute('data-selected');
            }

        }

        return $layoutDom;

    }

    private static function addRegionToDomElement($element, $region){

        $element->addAttribute('data-region', $region['id']);
        if ($region['is_fixed']){ $element->addAttribute('data-fixed', 'yes'); }
        if ($region['is_collection']){ $element->addAttribute('data-collection', $region['id']); }
        if ($region['is_global']){ $element->addAttribute('data-global', 'yes'); }

        return $element;

    }

    private static function addRegionToLayouts($layouts, $region){

        foreach($layouts as $layoutFile=>$layoutTitle){

            $layoutFilePath = Core::path('data', 'layouts', $layoutFile);

            if (!file_exists($layoutFilePath)) {
                self::prepareLayoutFile($layoutFile);
            }

            $html = file_get_contents($layoutFilePath);

            $dom = str_get_dom($html);

            $regionDom = $dom($region['path'], 0);

            if (!$regionDom) { continue; }
            if (!empty($regionDom->attributes['data-region'])){ continue; }

            $regionDom = self::addRegionToDomElement($regionDom, $region);

            file_put_contents($layoutFilePath, $dom);

        }

    }

    public static function updateLayout($layoutFile, $structure, $jqueryVersion){

        Loader::loadLibrary('ganon.php');

        $otherLayouts = Core::getLayoutsList();
        unset($otherLayouts[$layoutFile]);

        $layoutFilePath = Core::path('data', 'layouts', $layoutFile);

        $html = file_get_contents($layoutFilePath);

        $dom = self::clearLayoutMarkup(str_get_dom($html));

        $dom('html', 0)->addAttribute('data-jquery', $jqueryVersion);

        if (is_array($structure['deletions'])){
            foreach($structure['deletions'] as $deletion){

                $deletionDom = $dom($deletion['path'], 0);
                if (!$deletionDom) { continue; }

                $deletionDom->delete();

            }
        }

        if (is_array($structure['styles'])){
            foreach($structure['styles'] as $path => $style){

                $styleDom = $dom($path, 0);
                if (!$styleDom) { continue; }

                $styleDom->attributes['style'] = $style;

            }
        }

        if (is_array($structure['regions'])){
            foreach($structure['regions'] as $region){

                $regionDom = $dom($region['path'], 0);
                if (!$regionDom) { continue; }

                $regionDom = self::addRegionToDomElement($regionDom, $region);

                if (!empty($region['is_scan'])){
                    self::addRegionToLayouts($otherLayouts, $region);
                }

            }
        }

        if (is_array($structure['menus'])){
            foreach($structure['menus'] as $menu){

                $menuDom = $dom($menu['path'], 0);
                if (!$menuDom) { continue; }

                $menuDom->addAttribute('data-menu', $menu['id']);

                $selectedItem = $dom($menu['path'] . ' > *:eq(' . $menu['active_item_index'] . ')', 0);

                if ($selectedItem) { $selectedItem->addAttribute('data-selected', 'yes'); }

            }
        }

        file_put_contents($layoutFilePath, $dom);

    }

    public static function updateLayoutFromTemplate($layoutFile, $structure, $jqueryVersion){

        $templateFilePath = Core::path('theme', $layoutFile);

        if (!file_exists($templateFilePath)) { return false; }

        self::prepareLayoutFile($layoutFile);

        self::updateLayout($layoutFile, $structure, $jqueryVersion);

        return true;

    }

    public static function prepareLayoutFile($layoutFile){

        $layoutFileTemplatePath = Core::path('theme', $layoutFile);
        $layoutFilePath = Core::path('data', 'layouts', $layoutFile);

        copy($layoutFileTemplatePath, $layoutFilePath);

        $html = file_get_contents($layoutFilePath);
        $html = Html::addThemeUrlToAttribute('src', $html);
        $html = Html::addThemeUrlToAttribute('href', $html);

        Loader::loadLibrary('ganon.php');

        $dom = str_get_dom($html);

        /* Anti skel */
        foreach ($dom('head noscript') as $noscriptTag){
            $cssCount = count($noscriptTag('link[rel="stylesheet"]'));
            if (!$cssCount) { continue; }
            $childsCount = 0;
            for ($i = 0; $i < $noscriptTag->childCount(); $i++) {
                if (!trim($noscriptTag->getChild($i)->html())) { continue; }
                $childsCount++;
            }
            if ($childsCount == $cssCount){
                $head = $dom('head', 0);
                foreach($noscriptTag('link[rel="stylesheet"]') as $cssLink){
                    $cssLink->changeParent($head);
                }
                $noscriptTag->delete();
                $html = $dom;
            }
        }

        file_put_contents($layoutFilePath, $html);

    }

    public static function renderLayoutEditor($layoutFile, $isWizard=false){

        $layoutFilePath = Core::path('data', 'layouts', $layoutFile);

        if (!file_exists($layoutFilePath)) {
            self::prepareLayoutFile($layoutFile);
            if (!file_exists($layoutFilePath)) {
                return false;
            }
        }

        $panelHtml = self::renderTemplate('layouter/panel', array(
            'layouts' => Core::getLayoutsList(),
            'currentLayout' => $layoutFile
        ));

        return self::renderTemplate('layouter/layouter', array(
            'panel' => $panelHtml,
            'options' => array(
                'lang' => Config::get('ui_lang'),
                'backendUrl' => Core::getBackendUrl(),
                'layouterUrl' => Core::getLayouterUrl(),
                'editorUrl' => Core::getLayouterEditorUrl($layoutFile),
                'rootUrl' => ROOT_URL,
                'layout' => $layoutFile,
                'isWizard' => $isWizard
            )
        ));

    }

    public static function renderEditableLayout($layoutFile){

        $layoutFilePath = Core::path('data', 'layouts', $layoutFile);

        $html = file_get_contents($layoutFilePath);

		self::addCss(ROOT_URL . '/static/font-awesome/css/font-awesome.css');
		self::addCss(ROOT_URL . '/static/cms/css/editor-inject.css');
		self::addCss(ROOT_URL . '/static/cms/css/layouter-inject.css');

        self::addHtml(self::renderTemplate('layouter/contextmenu'));

        $html = str_replace('{insert:root}', ROOT_URL, $html);
        $html = str_replace('</head>', implode("\n", self::$insertions['head']) . '</head>', $html);
        $html = str_replace('</body>', implode("\n", self::$insertions['body']) . '</body>', $html);

        return $html;

    }

}
