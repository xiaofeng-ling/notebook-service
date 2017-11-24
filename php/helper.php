<?php
/**
 * Created by PhpStorm.
 * User: q8499
 * Date: 2017/11/22 0022
 * Time: 15:36
 */

if (!function_exists('router'))
{
    function router()
    {
        if (!Router::$i)
            Router::$i = new Router();

        return Router::$i;
    }
}

if (!function_exists('view'))
{
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
    function db()
    {
        if (!db::$i)
            db::$i = new db();

        return db::$i;
    }
}

if (!function_exists('config'))
{
    function config()
    {
        global $config;

        return $config;
    }
}

if (!function_exists('io'))
{
    function io()
    {
        if (!io::$i)
            io::$i = new io();

        return io::$i;
    }
}

if (!function_exists('secret'))
{
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

        return openssl_encrypt($data, $this->method, $key, OPENSSL_RAW_DATA, substr(md5($data), 0, 16));
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

        return openssl_decrypt($data, $this->method, $key, OPENSSL_RAW_DATA, substr(md5($data), 0, 16));
    }

    public function setKey($key)
    {
        if (empty($key))
            throw new Exception("key don't is null!");

        $this->$key = $key;
    }
}