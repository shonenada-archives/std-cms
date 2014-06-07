<?php

namespace Controller\Admin;

use \Model\Menu;
use \Model\User;
use \Model\Article;
use \Model\Category as CategoryModel;
use \Model\Permission;


class Content extends AdminBase {

    static public $url = '/admin/content';

    static public function get () {
        $page = self::$request->get('page');
        $pagesize = self::$app->config('pagesize');
        if (empty($page)) {
            $page = 1;
        }
        $listPageMenu = Menu::getByTypes(array(2));
        $listMids = array();
        foreach($listPageMenu as $menu) {
            $listMids[] = $menu->id;
        }
        $artilce_pager = Article::paginateWithMids($page, $pagesize, $listMids, 'sort', true);
        $total = $artilce_pager->count();
        $pager = array('current' => $page, 'nums' => ceil($total / $pagesize));
        $now = new \DateTime('now', new \DateTimezone('Asia/Shanghai'));
        return self::render('admin/content.html', get_defined_vars());
    }

}
