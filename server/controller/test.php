<?php

class test
{
    public function hello()
    {
        preg_match('/\/test\/(\S+)\/(\S+)/', '/test/12/44', $result);
        return $result;
//        return view('test.php', ['hello' => '世界，你好！']);
//        return "hello, world!\n";
    }
}