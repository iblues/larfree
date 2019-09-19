<?php

namespace Larfree\Middleware;

use Closure;
use App\Models\Common\CommonUser;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthJWT
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Larfree\Exceptions\ApiException
     */
    public function handle($request, Closure $next, int $is_force = 1)
    {
        // 开发者模式
        if (env('DEF_USER')) {
            $user = CommonUser::find(getLoginUserID());
            app()->current_auth_user = $user;// 放入实例中，同请求内复用
            return $next($request);
        } else {
            try {
                if (!$user = JWTAuth::parseToken()->authenticate()) {
                    apiError('user not found', '', 401.01);
                }
                app()->current_auth_user = $user;// 放入实例中，同请求内复用
            } catch (TokenExpiredException $e) {
                if($is_force)
                    apiError('登录已经过期，请重新登录', null, 401.02);
            } catch (TokenInvalidException $e) {
                if($is_force)
                    apiError('登录已经过期，请重新登录', null, 401.03);
            } catch (JWTException $e) {
                if($is_force)
                    apiError('请登录', null, 401);
            }

            return $next($request);
        }
    }

}
