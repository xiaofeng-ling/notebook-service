<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JWTAuth extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $this->checkForToken($request);
            if (!$user = $this->auth->parseToken()->authenticate())
                return apiJson([], '验证失败', 103, 401);

        } catch (TokenExpiredException $e) {
            // 刷新token的情况下不需要验证是否已过期
            if (false === strpos($request->url(), 'api/refresh'))
                return apiJson([], 'token已过期', 101, 401);
        } catch (TokenInvalidException $e) {
            return apiJson([],  'token无效', 102, 401);
        } catch (\Exception $e){
            return apiJson([],'验证失败', 103, 401);
        }

        return $next($request);
    }
}
