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

    public static $i;

    public function __construct()
    {
        $this->initRoutes();
    }

    /**
     * 启动路由，处理请求
     */
    public function start()
    {
        $url = $_SERVER['REQUEST_URI'];

        $parseUrl = $this->parseUrl($url);

        $result = '';

        // 先执行全局路由，可以做些是否登录的验证
        foreach ($this->globalRoutes as $func)
            call_user_func($func);

        if ($_SERVER['REQUEST_METHOD'] == 'POST')
            $result = $this->dispatch($this->postRoutes, $url);
        else if ($_SERVER['REQUEST_METHOD'] == 'GET')
            $result = $this->dispatch($this->getRoutes, $url);

        if (is_array($result))
        {
            // 确保返回的格式统一
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

        $this->addGlobalRoles('user_check', function() {
            $userSess = io()->cookie('sess');

            if (!$userSess)
                ;

            $userId = secret()->decrypt($userSess);
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
        $result = '';

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

        die('404 Not Found!');
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
}