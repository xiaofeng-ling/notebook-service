<?php
/**
 * Created by PhpStorm.
 * User: q8499
 * Date: 2017/11/23 0023
 * Time: 14:41
 */

namespace user;

class user
{
    /**
     * @var null
     */
    private $loggedUser = null;

    public function loginView()
    {
        return view('user\login.php');
    }

    public function login()
    {
        $name = trim(io()->post('name'));
        $password = trim(io()->post('password'));

        // 这里从数据库里面查找有没有这个人，没有的话直接返回
        // 有的话进行密码比对
        $result = db()->query('SELECT password FROM user WHERE name="'.$name.'"');

        if (!$result)
            return ['cn' => 2, 'msg' => '没有这个用户'];

        if ($password != $result[0]['password'])
            return ['cn' => 1, 'msg' => '验证失败！'];

        $sess = base64_encode(secret()->encrypt($name));
        // 假设验证通过
        io()->setCookie('sess', $sess, time()+3600, '/');
        return ['cn' => 0, 'msg' => '验证通过', 'data' => $sess];
    }

    public function getLoginUser()
    {
        if ($this->loggedUser)
            return $this->loggedUser;

        $sess = io()->cookie('sess');

        if (empty($sess))
            $sess = io()->post('sess');

        $name = secret()->decrypt(base64_decode($sess));

        $user = db()->query('SELECT * FROM user WHERE name="'.$name.'"');

        if (!$user)
            return ['cn' => 4, 'msg' => '没有这个用户'];

        return $this->loggedUser = $user;
    }
}