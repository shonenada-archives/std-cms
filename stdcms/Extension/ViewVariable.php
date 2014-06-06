<?php

namespace Extension;

class ViewVariable {

    static public $vars = null;

    static public function init() {
        static::$vars = array(
            'siteTitle' => 'stu-cms',
            'langCode' => \Util\Helper::getUserLanguageCode(),
            'globalTopMenus' => \Model\Menu::getTopMenus($all=false),
            'internationalization' => \GlobalEnv::get('app')->config('internationalization'),
        );
    }

}
ViewVariable::init();
