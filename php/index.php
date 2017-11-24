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

spl_autoload_register(function($classname) {
    if ($classname)
    {
        // 优先加载文件夹中的类
        // new \test\test()  => test(dir)\test.php@test(class)
        $path = __DIR__ . '\\controller\\' . $classname . '.php';

        if (file_exists($path))
            require_once $path;
        else
        {
            // 尝试加载文件
            // new \test\test() => test.php@test(class)
            $classnameArr = explode('\\', $classname);
            array_pop($classnameArr);
            $path = __DIR__ . '\\controller\\' . implode('\\', $classnameArr) . '.php';

            if (file_exists($path))
                require_once $path;
        }
    }
});

Router()->addRoles('/user', function() {
    $user = new \user\user();
    $user->loginView();
});

Router()->addRoles('/user/login', function() {
    $user = new \user\user();
    return $user->login();
}, 'post');

Router()->start();

