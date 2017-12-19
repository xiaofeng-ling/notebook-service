<?php
/**
 * Created by PhpStorm.
 * User: xiaofeng
 * Date: 2017/12/18 0018
 * Time: 15:11
 */

namespace notebook;

/**
 * 日记本对外接口
 * Class Notebook
 */
class Notebook
{
    private $tableName = 'notebook';

    /**
     * 对外接口，获取列表
     * @throws \Exception
     */
    public function getList()
    {
        $date = trim(io()->post('date'));

        $pageIndex = (int)io()->post('pageIndex');
        $pageSize = (int)io()->post('pageSize');

        $pageSize = $pageSize > 0 ? $pageSize : 10;

        $skip = $pageIndex*$pageSize;

        $where = ' WHERE ';
        $limit = " LIMIT $skip,$pageSize";

        if (!empty($date))
        {
            // 模糊查询
            $date = "'%$date%'";
            $where .= 'date LIKE ' . $date;
        }
        else
            $where = '';

        $result = db('notebook')->querySafe('SELECT * FROM '.$this->tableName.$where.$limit);

        return ['data' => $result];
    }

    /**
     * 增加一页日记
     * @return array
     * @throws \Exception
     */
    public function add()
    {
        $date = trim(io()->post('date'));
        $data = trim(io()->post('data'));

        if (empty($date) || empty($data))
            return ['cn' => 4, 'msg' => '参数错误！'];

        $result = db('notebook')->insert($date, $data);

        if (!$result)
            return ['cn' => 4, 'msg' => '插入失败！'];

        return ['msg' => '成功！'];
    }

    /**
     * 更新一条数据
     * @return array
     * @throws \Exception
     */
    public function update()
    {
        $id = (int)(io()->post('id'));

        $date = trim(io()->post('date'));
        $data = trim(io()->post('data'));

        if ($id <= 0 || empty($date) || empty($data))
            return ['cn' => 4, 'msg' => '参数错误！'];

        $notebook = db('notebook')->getOneById($id);

        if (empty($notebook))
            return ['cn' => 4, 'msg'=>'没有这条数据！'];

        $result = db('notebook')->update($id, $date, $data);

        if (!$result)
            return ['cn' => 4, 'msg' => '更新失败！'];

        return ['msg' => '成功！'];
    }

    public function remove()
    {
        $id = (int)io()->post('id');

        if ($id <= 0)
            return ['cn' => 4, 'msg' => '参数错误！'];

        $notebook = db('notebook')->getOneById($id);

        if (empty($notebook))
            return ['cn' => 4, 'msg'=>'没有这条数据！'];

        $result = db('notebook')->delete($id);

        if (!$result)
            return ['cn' => 4, 'msg' => '更新失败！'];

        return ['msg' => '成功！'];
    }
}