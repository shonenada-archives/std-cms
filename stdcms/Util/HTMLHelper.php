<?php

namespace Util;

class HTMLHelper {
    
    static public function getImg ($input) {
        $imgs = array();
        preg_match("/<[img|IMG][^>]+src=[\'\"](?<url>[\S]+)[\'\"][^>]+>/", $input, $imgs);
        if (isset($imgs['url']))
            return $imgs['url'];
        else
            return null;
    }

    static public function removeHTML($input) {
        $no_html = preg_replace("|<[^>]+>|", '', $input);
        return $no_html;
    }
    
}
