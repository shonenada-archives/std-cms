<?php

namespace Controller\Admin;

use \Model\Menu;
use \Model\User;
use \Model\Article;
use \Model\Category as CategoryModel;
use \Model\Permission;


class ContentSingle extends AdminBase {

    static public $url = '/admin/content/single';

    static public function get () {
        $now = new \DateTime('now', new \DateTimezone('Asia/Shanghai'));
        $page = self::$request->get('page');
        $pagesize = self::$app->config('pagesize');
        if (empty($page)) {
            $page = 1;
        }
        $singlePageMenu = Menu::getByTypes(array(1));
        $singleMids = array();
        foreach($singlePageMenu as $menu) {
            $singleMids[] = $menu->id;
        }
        $artilce_pager = Article::paginateWithMids($page, $pagesize, $singleMids, 'sort', true);
        return self::render('admin/content_single.html', get_defined_vars());
    }

}
