<?php

namespace Extension;

class ViewVariable {

    static public $vars = null;

    static public function init() {
        static::$vars = array(
            'siteTitle' => '深大计算机与软件学院自考官网',
            'langCode' => \Util\Helper::getUserLanguageCode(),
            'globalTopMenus' => \Model\Menu::getTopMenus($all=false),
            'internationalization' => \GlobalEnv::get('app')->config('internationalization'),
        );
    }

}
ViewVariable::init();
