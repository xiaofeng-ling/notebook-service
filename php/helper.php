<?php
/**
 * Created by PhpStorm.
 * User: q8499
 * Date: 2017/11/22 0022
 * Time: 15:36
 */
if (!function_exists('init'))
{
    function init()
    {
        // 切换工作目录
        if (getcwd() !== __DIR__)
            chdir(__DIR__);

        // 自动加载
        spl_autoload_register(function($classname) {
            // 加载model下的数据库文件
            // 优先加载文件夹中的类
            // new \model\test()  => test(dir)\test.php@test(class)
            if (preg_match('/model\\\/', $classname))
            {
                $path = __DIR__ . '\\'. $classname . '.php';

                if (file_exists($path))
                    require_once $path;
                else
                {
                    // 尝试加载文件
                    // new \test\test() => test.php@test(class)
                    $classnameArr = explode('\\', $classname);
                    array_pop($classnameArr);
                    $path = __DIR__ . '\\'. implode('\\', $classnameArr) . '.php';

                    if (file_exists($path))
                        require_once $path;
                }
            }
            else if ($classname)
            {
                // 优先加载文件夹中的类
                // new \test\test()  => test(dir)\test.php@test(class)
                $path = __DIR__ . '\\controller\\' . $classname . '.php';

                if (file_exists($path))
                    require_once $path;
                else
                {
                    // 尝试加载文件
                    // new \test\test() => test.php@test(class)
                    $classnameArr = explode('\\', $classname);
                    array_pop($classnameArr);
                    $path = __DIR__ . '\\controller\\' . implode('\\', $classnameArr) . '.php';
                    if (file_exists($path))
                        require_once $path;
                }
            }
        });
    }
}

if (!function_exists('router'))
{
    /**
     * @return Router()
     */
    function router()
    {
        if (!Router::$i)
            Router::$i = new Router();

        return Router::$i;
    }
}

if (!function_exists('view'))
{
    /**
     * @param $path
     * @param array $args
     * @throws Exception
     */
    function view($path, Array $args = [])
    {
        $path = __DIR__ . '\\view\\' . $path;

        if (!file_exists($path))
            throw new Exception("view file not found!");

        extract($args);

        require($path);
    }
}

if (!function_exists('db'))
{
    /**
     * @param $tableName
     * @return \database()
     * @throws Exception
     */
    function db($tableName)
    {
        $modelMap = config('modelMap');
        // 存放实例化后的数据表
        static $classMap = [];

        if (!array_key_exists($tableName, $modelMap))
            throw new Exception("没有这个数据表！");

        $tableClass = $modelMap[$tableName];

        if (!isset($classMap[$tableName]))
            $classMap[$tableName] = new $tableClass;

        return $classMap[$tableName];
    }
}

if (!function_exists('config'))
{
    // key is 'system.domain.test'
    function config($key = '')
    {
        global $config;

        if (empty($key))
            return $config;

        $key = explode('.', $key);

        $result = $config;

        foreach ($key as $v)
        {
            if (isset($result[$v]))
                $result = $result[$v];
            else
                throw new \Exception("error config!");
        }

        return $result;
    }
}

if (!function_exists('io'))
{
    /**
     * @return io()
     */
    function io()
    {
        if (!io::$i)
            io::$i = new io();

        return io::$i;
    }
}

if (!function_exists('secret'))
{
    /**
     * @param string $key
     * @return secret()
     * @throws Exception
     */
    function secret($key = '')
    {
        if (!secret::$i)
            secret::$i = new secret();

        if (!empty($key))
            secret::$i->setKey($key);

        return secret::$i;
    }
}

class io {
    public static $i;

    /**
     * @param string $key
     * @return null
     */
    public function get($key = '')
    {
        if (empty($key))
            return $_GET;

        if (isset($_GET[$key]))
            return $_GET[$key];
        else
            return null;
    }

    /**
     * @param string $key
     * @return null
     */
    public function post($key = '')
    {
        if (empty($key))
            return $_POST;

        if (isset($_POST[$key]))
            return $_POST[$key];
        else
            return null;
    }

    /**
     * @param string $key
     * @return null
     */
    public function cookie($key = '')
    {
        if (empty($key))
            return $_COOKIE;

        if (isset($_COOKIE[$key]))
            return $_COOKIE[$key];
        else
            return null;
    }

    /**
     * @param $key
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     * @return bool
     */
    public function setCookie($key, $value = '', $expire = 0, $path = '', $domain = '', $secure = false, $httponly = false)
    {
        return setcookie($key, $value, $expire, $path, $domain, $secure, $httponly);
    }
}

class secret
{
    public static $i;

    private $openssl;
    private $method = 'AES-256-CFB';        // 具体支持的算法可以用openssl_get_cipher_methods取得
    private $key = '';

    public function __construct($key = '123456789')
    {
        $this->setKey($key);

        if (!in_array($this->method, openssl_get_cipher_methods()))
            throw new Exception("encrypt method unsuporise!");
    }

    /**
     * @param $data
     * @param string $key
     * @return string
     */
    public function encrypt($data, $key = '')
    {
        if (empty($key))
            $key = $this->key;

        return openssl_encrypt($data, $this->method, $key, OPENSSL_RAW_DATA, substr(md5($key), 0, 16));
    }

    /**
     * @param $data
     * @param string $key
     * @return string
     */
    public function decrypt($data, $key = '')
    {
        if (empty($key))
            $key = $this->key;

        return openssl_decrypt($data, $this->method, $key, OPENSSL_RAW_DATA, substr(md5($key), 0, 16));
    }

    public function setKey($key)
    {
        if (empty($key))
            throw new Exception("key don't is null!");

        $this->$key = $key;
    }
}