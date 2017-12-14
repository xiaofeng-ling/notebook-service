<?php
/**
 * Created by PhpStorm.
 * User: q8499
 * Date: 2017/11/22 0022
 * Time: 11:08
 */

/** 负责路由处理
 * 映射规则
 * {domain}/dir/class/method/args1/args2...
 * {domain}/file/class/method/args1/args2...
 */

require_once 'config/config.php';
require_once 'router.php';
require_once 'helper.php';

init();

Router()->addRoles('/user', function() {
    $user = new \user\user();
    $user->loginView();
});

Router()->addRoles('/user/login', function() {
    $user = new \user\user();
    return $user->login();
}, 'post');

Router()->addRoles('/test', function() {
   $user = new \user\user();

   var_dump($user->getLoginUser());
});

// 用作路由时
if (preg_match('/\.js|\.css|\.png|\.jpg/', $_SERVER['REQUEST_URI']))
    return false;

Router()->start();

