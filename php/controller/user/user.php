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

    /**
     * 加载登录界面
     * @throws \Exception
     */
    public function loginView()
    {
        return view('user\login.php');
    }

    /**
     * 对外接口 登录
     * @return array
     * @throws \Exception
     */
    public function login()
    {
        $name = trim(io()->post('name'));
        $password = trim(io()->post('password'));

        // 这里从数据库里面查找有没有这个人，没有的话直接返回
        // 有的话进行密码比对
        $result = db('user')->query('SELECT password FROM user WHERE name="'.$name.'"');

        if (!$result)
            return ['cn' => 2, 'msg' => '没有这个用户'];

        if ($password != $result[0]['password'])
            return ['cn' => 1, 'msg' => '验证失败！'];

        $sess = base64_encode(secret()->encrypt($name));
        // 假设验证通过
        io()->setCookie('sess', $sess, time()+3600, '/');
        return ['cn' => 0, 'msg' => '验证通过', 'data' => $sess];
    }

    /**
     * 获取登录的用户
     * @return array|null
     * @throws \Exception
     */
    public function getLoginUser()
    {
        if ($this->loggedUser)
            return $this->loggedUser;

        $sess = io()->cookie('sess');

        if (empty($sess))
            $sess = io()->post('sess');

        $name = secret()->decrypt(base64_decode($sess));

        $user = db('user')->query('SELECT * FROM user WHERE name="'.$name.'"');

        if (!$user)
            return ['cn' => 4, 'msg' => '没有这个用户'];

        return $this->loggedUser = $user;
    }
}