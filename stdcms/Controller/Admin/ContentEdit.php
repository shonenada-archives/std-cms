<?php

namespace Controller\Admin;

use \Model\Menu;
use \Model\User;
use \Model\Article;
use \Model\ArticleContent;
use \Model\Category as CategoryModel;
use \Model\Permission;


class ContentEdit extends AdminBase {

    static public $url = '/admin/content/:aid/edit';
    static public $conditions = array('aid' => '\d+');

    static public function get ($aid) {
        $article = Article::find($aid);
        if ($article == null) {
            self::redirect(self::urlFor('admin_content_get'));
        }
        $menus = Menu::getByTypes(array(2));
        $timestamp = $_SESSION['add_timestamp'] = time() * 10000 + rand(0, 9999);
        return self::render('admin/content_edit.html', get_defined_vars());
    }

    static public function post ($aid) {
        if ($_SESSION['add_timestamp'] != self::$request->post('timestamp')) {
            return self::redirect(self::urlFor('admin_content_edit_get', array('aid' => $aid)));
        }
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
        if ($success) {
            $article = Article::find($aid);
            if ($article == null) {
                return self::redirect(self::urlFor('admin_content_edit_get', array('aid' => $aid)));
            }
            $url ='';
            if (isset($post['url'])) {
                $url = $post['url'];
            }
            $data = array(
                'menu' => $menu,
                'category' => null,
                'editor' => \GlobalEnv::get('user'),
                'open_style' => $post['open_style'],
                'redirect_url' => $url,
                'edit_time' => new \DateTime('now', new \DateTimezone('Asia/Shanghai')),
            );
            $article->populateFromArray($data)->save();
            $zh = $article->translate('zh');
            $zh->title = $post['title'];
            $zh->content = $post['content'];
            $zh->save();
            if (isset($post['title_eng'])){
                $en = $article->translate('en');
                $en->title = $post['title_eng'];
                $en->content = $post['content_eng'];
                $en->save();
            }
            if (empty($_SESSION['upload_buffer'])){
                $upload_buffer = array();
            } else {
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
