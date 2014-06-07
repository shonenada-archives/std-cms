<?php

namespace Controller\Admin;

use \Model\Menu;
use \Model\User;
use \Model\Article;
use \Model\Permission;


class ContentSingleCreate extends AdminBase {

    static public $url = '/admin/content/single/:mid/create';
    static public $conditions = array('mid' => '\d+');


    static public function get ($mid) {
        $menu = Menu::find($mid);
        $menus = Menu::getByTypes(array(2));
        $timestamp = $_SESSION['add_timestamp'] = time() * 10000 + rand(0, 9999);
        return self::render('admin/content_create.html', get_defined_vars());
    }

    static public function post ($mid) {
        $post = self::$request->post();
        $info = '';
        $success = true;

        if ($_SESSION['add_timestamp'] != $post['timestamp']) {
            $success = false;
            $info = '时间参数不正确，请刷新后重新输入';
        }

        if (empty($post['title'])) {
            $success = false;
            $info = '标题不能为空';
        }

        $menu = Menu::find($post['menu']);
        if ($menu == null) {
            $info = '菜单不存在！';
            $success = false;
        }
        if ($menu->type == 1) {
            // 单页
            $a = Article::findOneBy(array('menu' => $menu));
            if ($a != null) {
                $info = '单页菜单只允许存在一篇文章，新增失败！';
                $success = false;
            }
        }
        if ($success) {
            $article = new Article();
            $user = \GlobalEnv::get('user');
            $data = array(
                'menu' => $menu,
                'category' => null,
                'author' => $user,
                'editor' => $user,
                'redicret_url' => $post['url'],
                'edit_time' => new \DateTime("now"),
            );
            $open_style = $post['open_style'];
            if (isset($open_style)) {
                $data['open_style'] = $open_style;
            }
            $article->populateFromArray($data)->save();
            $zh = $article->translate('zh');
            $zh->title = $post['title'];
            $zh->content = $post['content'];
            $zh->save();
            if (isset($post['title_eng'])) {
                $en = $article->translate('en');
                $en->title = $post['title_eng'];
                $en->content = $post['content_eng'];
                $en->save();
            }

            if (empty($_SESSION['upload_buffer'])){
                $upload_buffer = array();
            }
            else {
                $upload_buffer = $_SESSION['upload_buffer'];
            }
            foreach($upload_buffer as $f) {
                $f->article = $article;
                $f->save();
            }
            $_SESSION['upload_buffer'] = array();
        }

        return json_encode(array(
            'success' => $success,
            'info' => $info,
            'next' => self::urlFor('admin_content_get')
        ));
    }

}
