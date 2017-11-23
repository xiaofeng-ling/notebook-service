<?php
/**
 * Created by PhpStorm.
 * User: q8499
 * Date: 2017/11/22 0022
 * Time: 16:22
 */

interface database
{
    public function getOne($query);

    public function getSome($query);

    public function insert($query);

    public function delete($query);

    public function update($query);

    public function query($query);
}

class db implements database
{

    public static $i;
    private $connect;

    public function __construct()
    {
        // 连接数据库
        $this->connect = new SQLite3(config()['db']['filename']);

        if (!$this->connect)
            throw new Exception($this->connect->lastErrorMsg());
    }

    public function getOne($query)
    {
        // TODO: Implement getOne() method.
    }

    public function getSome($query)
    {
        // TODO: Implement getSome() method.
    }

    public function insert($query)
    {
        // TODO: Implement insert() method.
    }

    public function delete($query)
    {
        // TODO: Implement delete() method.
    }

    public function update($query)
    {
        // TODO: Implement update() method.
    }

    public function query($query)
    {
        $this->connect->query($query);
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }
}