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
require_once "model/model.php";

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

    (new \test())->hello();

//   return $user->getList();
});

Router()->addRoles('/modelTest', function() {
    $test = new \Test\modelTest();

    return $test->notebookTest();
});

/**
 * ----------------------------------------------------------
 * 日记本的对外接口
 */
Router()->addRoles('/notebook/getList', function() {
    $notebook = new \notebook\Notebook();

    return $notebook->getList();
}, 'post');

Router()->addRoles('/notebook/add', function() {
    $notebook = new \notebook\Notebook();

    return $notebook->add();
}, 'post');

Router()->addRoles('/notebook/remove', function() {
    $notebook = new \notebook\Notebook();

    return $notebook->remove();
}, 'post');

Router()->addRoles('/notebook/update', function() {
    $notebook = new \notebook\Notebook();

    return $notebook->update();
}, 'post');

/**
 * ----------------------------------------------------------
 * 日记本接口结束
 */

// 用作路由时
if (preg_match('/\.js|\.css|\.png|\.jpg/', $_SERVER['REQUEST_URI']))
    return false;

Router()->start();

