<?php
/**
 * Created by PhpStorm.
 * User: q8499
 * Date: 2017/11/23 0023
 * Time: 14:41
 */

namespace user;

class user
{
    public function loginView()
    {
        return view('user\login.php');
    }

    public function login()
    {
        $name = trim(io()->post('name'));
        $password = trim(io()->post('password'));

        // 这里从数据库里面查找有没有这个人，没有的话直接返回
        // 有的话进行密码比对

        // 假设验证通过
        io()->setCookie('sess', secret()->encrypt($name), time()+3600, '/');
        return ['cn' => 0, 'msg' => '验证通过'];
    }
}