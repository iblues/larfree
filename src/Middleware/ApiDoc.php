<?php

namespace Larfree\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;

class ApiDoc
{
    /**
     * 重新整理api的返回结果.
     * 并生成对于的json文件
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if (!config('app.debug')) {
            return $response;
        }
        //以下汇总storage/apiReturn/tmp生成存储返回结果的文件
        $nowRoute = Route::current();


        $content = $response->getContent();
        $path    = storage_path('apiReturn/tmp/');
        if (!file_exists($path)) {
            mkdir($path);
        }
        $md5 = md5(Route::currentRouteAction().$content);
        $file = $path.$md5.'.json';
        //美化格式
        $content = json_encode(json_decode($content, 1), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $content = [
            'content' => $content, 'time' => time(), 'action' => Route::currentRouteAction(), 'url' => $nowRoute->uri
        ];
        file_put_contents($file, json_encode($content));
        $response->header('ApiReturn', $md5);
        return $response;
    }

}
