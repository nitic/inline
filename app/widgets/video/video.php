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

namespace InlineCMS\Widgets\Video;

use InlineCMS\Core\Widget;
use InlineCMS\Core\Request;
use InlineCMS\Core\Response;

class Video extends Widget {


    public function validate(){

        $url = Request::get('url');

        $source = $this->getSourceByUrl($url);

        if (!$source){
            Response::sendJson(array(
                'success' => false,
                'error' => $this->lang('videoUnknown', implode(', ', array_keys($this->getSources())))
            ));
        }

        Response::sendJson(array(
            'success' => true
        ));

    }

    public function getVideoCode(){

        $url = Request::get('url');
        $size = Request::get('size');

        list($w, $h) = explode('x', $size);

        $source = $this->getSourceByUrl($url);

        Response::sendJson(array(
            'success' => (bool)$source,
            'html' => '<iframe src="'.$source['result'].'" width="'.$w.'" height="'.$h.'" frameborder="0" allowfullscreen></iframe>'
        ));

    }

    public function getSourceByUrl($url){

        foreach($this->getSources() as $name => $source){

            $patterns = is_array($source['url']) ? $source['url'] : array($source['url']);

            foreach($patterns as $pattern){
                if (preg_match("~{$pattern}~imu", $url, $matches)){
                    $src = $source['src'];
                    foreach($matches as $index=>$match){
                        $src = str_replace("$" . ($index+1), $match, $src);
                    };
                    $source['result'] = $src;
                    return $source;
                }
            }

        }

        return false;

    }

    private function getSources(){
        return array(

            'YouTube' => array(
                'url' => array(
                    'https?://youtu\.be/([0-9a-z-_]{11})',
                    'https?://(?:video\.google\.(?:com|com\.au|co\.uk|de|es|fr|it|nl|pl|ca|cn)/(?:[^"]*?))?(?:(?:m|www|au|br|ca|es|fr|de|hk|ie|in|il|it|jp|kr|mx|nl|nz|pl|ru|tw|uk)\.)?youtube\.com(?:[^"]*?)?(?:&|&amp;|/|\?|;|\%3F|\%2F)(?:video_id=|v(?:/|=|\%3D|\%2F)|embed(?:/|=|\%3D|\%2F))([0-9a-z-_]{11})',
                ),
                'src' => 'https://www.youtube.com/embed/$2'
            ),

            'Dailymotion' => array(
                'url' => array(
                    'http://dai\.ly/([a-z0-9]{1,})',
                    'http://(?:www\.)?dailymotion\.(?:com|alice\.it)/(?:(?:[^"]*?)?video|swf)/([a-z0-9]{1,18})',
                ),
                'src' => '//www.dailymotion.com/embed/video/$2'
            ),

            'Vimeo' => array(
                'url' => 'https?://(?:www\.)?vimeo\.com/(?:[0-9a-z_-]+/)?(?:[0-9a-z_-]+/)?([0-9]{1,})',
                'src' => 'http://player.vimeo.com/video/$2'
            ),

            'Aparat' => array(
                'url' => 'http://www.aparat.com/v/([A-z0-9-_]+)(?:/.*)?',
                'src' => 'http://www.aparat.com/video/video/embed/videohash/$2/vt/frame/'
            ),

            'Blip' => array(
                'url' => 'http://blip\.tv/[a-z0-9-]+/[a-z0-9-]+-',
                'src' => 'http://blip.tv/play/$3.x?p=1'
            ),

            'ClipFish' => array(
                'url' => 'http://(?:www\.)?clipfish\.de/(?:video)?player\.(?:swf|php)(?:[^"]*?)videoid=((?:[a-z0-9]{18})(?:==)?|(?:[a-z0-9]{6})(?:==)?)',
                'src' => 'http://www.clipfish.de/embed_video/?vid=$2'
            ),

            'Metatube' => array(
                'url' => 'http://www\.metatube\.com/([a-z]+)/videos/([a-z0-9-/]+)/',
                'src' => 'http://www.metatube.com/$2/videos/$3/embed/'
            ),

        );
    }

}
