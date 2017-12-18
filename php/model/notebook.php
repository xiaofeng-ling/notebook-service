<?php
/**
 * Created by PhpStorm.
 * User: xiaofeng
 * Date: 2017/12/18 0018
 * Time: 15:18
 */

namespace model;

use database;

class notebook extends database
{
    public function getTableName()
    {
        return 'notebook';
    }

    /**
     * @param string $date 日期
     * @param string $data 数据，经过客户端加密后的数据
     * @throws \Exception
     */
    public function insert(string $date, string $data)
    {
        // 这里设置insertTime的值
        $params = [
            'date' => $date,
            'data' => $data,
            'insertTime' => time(),
            'updateTime' => 0,
        ];

        try
        {
            $this->query('INSERT INTO ' . $this->getTableName() . ' (date, data, insertTime, updateTime) VALUES (?, ?, ?, ?)', $params);
        }
        catch (\Exception $e)
        {
            return false;
        }

        return true;
    }

    /**
     * @param int $id
     * @param string $date 日期
     * @param string $data 数据，经过客户端加密后的数据
     * @throws \Exception
     */
    public function update(int $id, string $date, string $data)
    {
        // 这里设置updateTime的值
        $params = [
            'date' => $date,
            'data' => $data,
            'updateTime' => time(),
        ];

        try
        {
            $this->querySafety('UPDATE ' . $this->getTableName() . ' SET date=?, data=?, updateTime=? WHERE Id=' . $id, $params);
        }
        catch (\Exception $e)
        {
            return false;
        }

        return true;
    }

    /**
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function delete(int $id)
    {
        try
        {
            $this->query('DELETE FROM ' . $this->getTableName() . ' WHERE id=' . $id);
        }
        catch (\Exception $e)
        {
            return false;
        }

        return true;
    }

    public function createTable()
    {
        return $this->query('CREATE TABLE notebook(id INTEGER PRIMARY KEY, date VARCHAR NOT NULL, data VARCHAR NOT NULL, insertTime INTEGER NOT NULL, updateTime INTEGER NOT NULL)');
    }
}