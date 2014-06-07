<?php

namespace Controller\Admin;

use \Model\Permission;

class ProfilePassword extends AdminBase {

    static public $url = '/admin/profile/password';

    static public function get () {
        return self::render('admin/profile_password.html', get_defined_vars());
    }

    static public function post () {
        $app = \GlobalEnv::get('app');
        $user = \GlobalEnv::get('user');
        $info = '';
        $success = true;
        $old = self::$request->post('oldpassword');
        if (!$user->checkPassword($old, $app->config('salt'))) {
            $info = '旧密码错误，请重新输入';
            $success = false;
        }
        $new = self::$request->post('newpassword');
        $confirm = self::$request->post('confirmpassword');
        if (strlen($new) <= 0) {
            $info = '请输入密码';
            $success = false;
        }
        if ($new != $confirm) {
            $info = '确认密码不匹配，请重新输入';
            $success = false;
        }
        if ($success) {
            $user->setPassword($confirm, $app->config('salt'));
            $user->save();
        }
        return json_encode(array(
            'success' => $success,
            'info' => $info,
            ''
        ));
    }

}
