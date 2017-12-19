<?php
/**
 * Created by PhpStorm.
 * User: q8499
 * Date: 2017/11/22 0022
 * Time: 15:29
 */

class router
{
    private $getRoutes;
    private $postRoutes;

    private $globalRoutes;

    private $url;

    public static $i;

    public function __construct()
    {
        $this->url = $_SERVER['REQUEST_URI'];
        $this->initRoutes();
    }

    /**
     * 启动路由，处理请求
     */
    public function start()
    {
        $result = '';

        // 先执行全局路由，可以做些是否登录的验证
        foreach ($this->globalRoutes as $func)
            call_user_func($func);

        if ($_SERVER['REQUEST_METHOD'] == 'POST')
            $result = $this->dispatch($this->postRoutes, $this->url);
        else if ($_SERVER['REQUEST_METHOD'] == 'GET')
            $result = $this->dispatch($this->getRoutes, $this->url);

        if (is_array($result))
        {
            // 确保返回的格式统一
            header('Content-type: application/json;charset=utf-8');
            $result = array_replace(['cn' => 0, 'msg' => '', 'data' => []], $result);
            echo json_encode($result);
        }
        else
            echo $result;
    }

    /**
     * 初始化路由
     */
    private function initRoutes()
    {
        $this->postRoutes = [];
        $this->getRoutes = [];

        // 用户验证
        $this->addGlobalRoles('user_check', function() {
            if (preg_match('/\/user\/login/', $this->url))
                return;

            if (preg_match('/\/user$/', $this->url))
                return;

            $user = (new \user\user())->getLoginUser();

            if (isset($user['cn']) && $user['cn'] != 0)
                $this->on403($user['msg']);
        });
    }

    /**
     * 解析url
     * @param $url
     * @return array|mixed
     */
    protected function parseUrl($url)
    {
        $url = preg_replace('/\.php/', '', $url);
        $url = explode('/', $url);

        // 去掉最开头的空元素
        array_shift($url);
        return $url;
    }

    /**
     * @param $url
     * @param callable $func
     * @param string $method
     * @throws Exception
     */
    public function addRoles($url, callable $func, $method = 'GET')
    {
        $method = strtoupper($method);

        $url = '/' . preg_replace('/\{var\}/', '(\\S+)', preg_replace('/\//', '\\/', $url)) . '$/';

        if ($method == 'GET')
            $this->getRoutes[$url] = $func;
        else if ($method == 'POST')
            $this->postRoutes[$url] = $func;
        else
            throw new Exception("method type no suppiose!");
    }

    /**
     * 处理路由
     * @param array $routes
     * @param $url
     * @return mixed|string
     */
    private function dispatch(Array $routes, $url)
    {
        foreach ($routes as $k => $v)
        {
            if (preg_match($k, $url, $match))
            {
                // 去除掉开头的自身匹配
                array_shift($match);

                $result = call_user_func_array($v, $match);

                return $result;
            }
        }

        $this->on404();
    }

    /**
     * @param $name
     * @param callable $func
     */
    public function addGlobalRoles($name, callable $func)
    {
        $this->globalRoutes[$name] = $func;
    }

    /**
     * @param $name
     * @return int
     */
    public function removeGlobalRoles($name)
    {
        if (!array_key_exists($name, $this->globalRoutes))
            return -1;

        unset($this->globalRoutes[$name]);

        return 1;
    }

    /**
     * @param $url
     */
    public function redirect($url)
    {
        header("Location: $url");
    }

    /**
     * 404 error
     */
    private function on404()
    {
        die('404 Not Found!');
    }

    /**
     * 403 error
     * @param string $msg
     */
    private function on403($msg = '')
    {
        header('Content-type: text/html;charset=utf-8');
        die('<h1>403</h1>'.$msg);
    }
}