<?php
/**
 * Created by PhpStorm.
 * User: xiaofeng
 * Date: 2017/12/18 0018
 * Time: 16:56
 */

namespace Test;

class modelTest
{
    public function notebookTest()
    {
        var_dump(db('notebook')->insert('2017-12-18', '12312'));
        var_dump(db('notebook')->update(2, '2012-22-19', '434'));
        var_dump(db('notebook')->delete(1));
    }
}