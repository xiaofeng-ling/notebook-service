<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SecretServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Secret', function($app) {
            return new Secret();
        });
    }
}

class Secret
{
    private $openssl;
    private $method = 'AES-256-CFB';        // 具体支持的算法可以用openssl_get_cipher_methods取得
    private $key = '';

    /**
     * secret constructor.
     * @param string $key
     * @throws \Exception
     */
    public function __construct($key = '123456789')
    {
        $this->setKey($key);

        if (!in_array($this->method, openssl_get_cipher_methods()))
            throw new \Exception("encrypt method unsuporise!");
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

    /**
     * @param $key
     * @throws \Exception
     */
    public function setKey($key)
    {
        if (empty($key))
            throw new \Exception("key don't is null!");

        $this->$key = $key;
    }
}
