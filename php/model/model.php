<?php
/**
 * Created by PhpStorm.
 * User: q8499
 * Date: 2017/11/22 0022
 * Time: 16:22
 */

/**
 * Class database
 */
abstract class database
{
    public static $i;
    private $connect;

    public function __construct()
    {
        // 连接数据库
        $this->connect = new SQLite3(config('db.filename'));
        if (!$this->connect)
            throw new Exception($this->connect->lastErrorMsg());
    }

    public function query($query)
    {
        $result = $this->connect->query($query);
        $resultArr = [];

        if (!$result)
            throw new Exception($this->connect->lastErrorMsg());

        while ($r = $result->fetchArray(SQLITE3_ASSOC))
            $resultArr[] = $r;

        return $resultArr;
    }

    public function __destruct()
    {
        $this->connect->close();
    }

    abstract public function getTableName();
    abstract public function createTable();
}

/**
 * Class db
 */
class db extends database
{
    public function getTableName()
    {
        return 'user';
    }

    public function createTable()
    {
        return $this->query('CREATE TABLE user(id INT PRIMARY KEY, name VARCHAR NOT NULL, password VARCHAR NOT NULL)');
    }
}