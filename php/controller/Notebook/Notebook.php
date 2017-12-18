<?php
/**
 * Created by PhpStorm.
 * User: xiaofeng
 * Date: 2017/12/18 0018
 * Time: 15:11
 */

/**
 * 日记本对外接口
 * Class Notebook
 */
class Notebook
{
    private $tableName = 'notebook';

    /**
     * 对外接口，获取列表
     */
    public function getList()
    {
        $pageIndex = (int)io()->post('pageIndex');
        $pageSize = (int)io()->post('pageSize');

        $pageSize = $pageSize > 0 ? $pageSize : 10;

        $result = db('notebook')->querySafety('SELECT * FROM '.$this->tableName.' ');

    }
}