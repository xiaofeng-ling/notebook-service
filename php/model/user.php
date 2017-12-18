<?php
/**
 * Created by PhpStorm.
 * User: xiaofeng
 * Date: 2017/12/18 0018
 * Time: 15:18
 */

namespace model;

use database;

class user extends database
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