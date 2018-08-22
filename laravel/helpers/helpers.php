<?php
/**
 * Created by PhpStorm.
 * User: xiaofeng
 * Date: 2018/8/22 0022
 * Time: 13:39
 */

if (!function_exists('apiJson'))
{
    /**
     * @param $data
     * @param string $message
     * @param int $code
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    function apiJson($data, $message = '成功', $code = 1000, $status = 200)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'code' => $code
        ], $status);
    }
}