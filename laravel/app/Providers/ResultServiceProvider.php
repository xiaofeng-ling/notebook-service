<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ResultServiceProvider extends ServiceProvider
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
        $this->app->singleton("Result", function($app, $parameters) {
            return new Result($parameters[0], $parameters[1]);
        });
    }
}

class Result
{
    private $errorCode = 0;
    private $errorMsg = 'success!';
    private $data = [];

    /**
     * Error constructor.
     * @param int $errorCode
     * @param string $errorMsg
     * @param array $data
     */
    public function __construct(int $errorCode, string $errorMsg, Array $data = [])
    {
        $this->setErr($errorCode, $errorMsg);
        $this->setData($data);
    }

    /**
     * @param int $errorCode
     * @param string $errorMsg
     */
    public function setErr(int $errorCode, string $errorMsg)
    {
        $this->errorCode = $errorCode;
        $this->errorMsg = $errorMsg;
    }

    /**
     * @param array $data
     */
    public function setData(Array $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return @json_encode([
            'code' => $this->errorCode,
            'msg' => $this->errorMsg,
            'data' => $this->data,
        ]);
    }
}
