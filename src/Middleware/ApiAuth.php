<?php

namespace Larfree\Middleware;

use Closure;
use App\Models\Common\CommonUser;
use Illuminate\Support\Facades\Auth;
use Larfree\Exceptions\ApiException;

class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param int $is_force 是否强制登录
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
                //如果未登录
                if (!Auth::check()) {
                    if ($is_force) {
                        apiError('请登录', null, 401);
                    } else {
                        app()->current_auth_user = Auth::user();;// 放入实例中，同请求内复用
                    }
                }
            } catch (ApiException $e) {
                throw $e;
            } catch (\Exception $e) {
                if ($is_force)
                    apiError('登录失效,请重新登录', null, 401);
            }

            return $next($request);
        }
    }

}
