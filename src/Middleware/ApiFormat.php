<?php

namespace Larfree\Middleware;

use Closure;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ApiFormat
{
    /**
     * 重新整理api的返回结果.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //必须加 否则422报错的时候回302跳转
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->headers->set('accept', 'application/json');

        /**
         * @var $response Response
         */
        $response = $next($request);

        if ($response instanceof BinaryFileResponse) {
            return $response;
        }

        //跨域
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Authorization, Cookie, Accept');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS, DELETE');
        $response->header('Access-Control-Allow-Credentials', 'false');

        //设置json返回中文
        if (method_exists($response, 'setEncodingOptions')) {
            $response->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        }

        $json = json_decode($response->getContent(), true);

        //有code和status和msg可以认为已经格式化了
        if (isset($json['code']) && isset($json['status'])) {
            $this->setHttpCode($json['code'], $response);
            $this->appCode($request, $response);
            return $response;
        }

        //非ajax请求.
        if (!$request->ajax()) {
            return $response;
        }

        //200代码的才是正常返回
        $code = $response->getStatusCode() < 400 ? 1 : 0;
        //获取原始内容
        $content = $response->getOriginalContent();
        //重新规整输出格式.
        $response = $this->FormatJson($response, $content, $code);
        $this->appCode($request, $response);
        return $response;
    }

    /**
     * 特殊设备强制返回200
     * @param $request
     * @param $response
     * @author Blues
     *
     */
    protected function appCode($request, $response)
    {
        if ($request->headers->get('device') == 'app' && $response->getStatusCode() < 500) {
            $this->setHttpCode(200, $response);
        }
    }

    /**
     * 设置httpCode
     * @param $code
     * @param $response
     * @author Blues
     *
     */
    protected function setHttpCode($code, $response)
    {
        if ($code > 10000) {
            $code = intval($code / 100);
        }

        if ($code >= 600) {
            $code = 412;
        }

        $response->setStatusCode($code);
    }

    /**
     * 重置json格式
     * @param $response
     * @param $content
     * @param  int  $code
     * @return mixed
     */
    protected function FormatJson($response, $content, $code = 1)
    {
        $StatusCode = $response->getStatusCode();
        $msg        = '';

        if ($StatusCode == 302) {
            return $response;
        }
        if ($StatusCode == 422 && isset($content['errors'])) {
            $msg     = current(current($content['errors']));
            $content = $content['errors'];
        }

        //兼容不同版本的validate返回
        if ($StatusCode == 422 && !$msg) {
            $msg     = current(current($content));
            $content = $content;
        }
        return $this->resource($response, $content, $code, $StatusCode, $msg);
    }

    /**
     * 如果需要修改返回格式, 请重写此函数
     * @param $response
     * @param $content
     * @param $code
     * @param $status
     * @param $msg
     * @return mixed
     * @author Blues
     *
     */
    protected function resource($response, $content = null, $code = 200, $status = 1, $msg = '')
    {
        $return = [
            'msg' => $msg,
            'code' => $status,
            'status' => $code,
            'data' => $content,
        ];
        //重新设置格式
        if (method_exists($response, 'setData')) {
            //如果是json响应
            return $response->setData($return);
        } else {
            //视图类响应
            return $response->setContent($return);
        }
    }
}
