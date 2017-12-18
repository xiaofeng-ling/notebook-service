<?php
/**
 * Created by PhpStorm.
 * User: xiaofeng
 * Date: 2017/12/18 0018
 * Time: 16:48
 */

/**
 * 这个文件仅用于生成数据表
 */
require_once "../config/config.php";
require_once "../helper.php";
require_once "model.php";

init();

$modelMap = config('modelMap');

foreach ($modelMap as $v)
{
    /**
     * @var \database $model
     */
    $model = new $v;

    try
    {
        $model->createTable();
    }
    catch (Exception $e)
    {
        var_dump($e->getMessage());
        continue;
    }
}