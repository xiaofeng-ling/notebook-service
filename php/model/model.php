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
    protected $connect;

    public function __construct()
    {
        // 连接数据库
        $this->connect = new SQLite3(config('db.filename'));
        if (!$this->connect)
            throw new Exception($this->connect->lastErrorMsg());
    }

    /**
     * @param $query
     * @return array
     * @throws Exception
     */
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

    /**
     * 安全的查询
     * @param string $query
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function querySafe(string $query, Array $params = [])
    {
        $query = $this->connect->prepare($query);

        // 防止是关联数组引起的冲突
        $params = array_values($params);

        for ($i=0; $i<count($params); $i++)
        {
            $query->bindValue($i+1, $params[$i]);
        }

        $result = $query->execute();
        $resultArr = [];

        if (!$result)
            throw new Exception($this->connect->lastErrorMsg());

        while ($r = $result->fetchArray(SQLITE3_ASSOC))
            $resultArr[] = $r;

        return $resultArr;
    }

    public function getConnect()
    {
        return $this->connect;
    }

    public function __destruct()
    {
        $this->connect->close();
    }

    abstract public function getTableName();
    abstract public function createTable();
}