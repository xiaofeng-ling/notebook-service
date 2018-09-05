<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwtauth')->except(['login']);
    }

    /**
     * 使用账号密码登录
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['message' => '账号密码不正确', 'code' => 104], 401);
        }

        return apiJson(['token' => $token, 'user_id' => auth('api')->user()->getUserId()]);
    }

    /**
     * 登出
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => '成功退出', 'code' => 100]);
    }

    /**
     * 刷新token
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return apiJson(auth('api')->refresh());
    }
}
