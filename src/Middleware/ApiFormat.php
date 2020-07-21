<?php

namespace Larfree\Middleware;

use Closure;
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

//        dd($request->headers);s
        $response = $next($request);

        if ($response instanceof BinaryFileResponse) {
            return $response;
        }

        //跨域
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Authorization, Cookie, Accept');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS, DELETE');
        $response->header('Access-Control-Allow-Credentials', 'false');


        //设置中文不要转码
        if (method_exists($response, 'setEncodingOptions')) {
            $response->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        }

        $this->setHttpCode(null,$response);
        $this->appCode($request,$response);
        $code = $response->getStatusCode()<400?1:0;

        //重置特殊错误码
        if($response->getStatusCode()>300) {
            $content = $response->getOriginalContent();
            return $response = $this->FormatJson($response, $content, $code);
        }else{
            return $response;
        }
    }


    /**
     * 重置json格式
     * @param $response
     * @param $content
     * @param int $code
     * @return mixed
     */
    protected function FormatJson($response,$content,$code=1){
        $StatusCode = $response->getStatusCode();
        $msg = '';
        if($StatusCode==302){
            return $response;
        }
        if($StatusCode==422 && isset($content['errors'])){
            $msg = current(current($content['errors']));
            $content = $content['errors'];
        }

        //兼容不同版本的validate返回
        if($StatusCode==422 && !$msg){
            $msg = current(current($content));
            $content = $content;
        }

//        if($StatusCode==401)
//            $code=-10;

        //重新设置格式
        if(method_exists($response,'setData')) {
            //如果是json响应
            return $response->setData([
                'msg'=>$msg,
                'code' => $StatusCode,
                'status' => $code,
                'data' => $content,
                'debug' => '',
            ]);
        }else{
            //视图类响应
            return $response->setContent([
                'code' => $StatusCode,
                'status' => $code,
                'data' => $content,
                'debug' => '',
            ]);
        }
    }

    /**
     * app不方便处理其他异常状态码
     * @param $request
     * @param $response
     * @author Blues
     */
    protected function appCode($request, $response)
    {
        if ($request->headers->get('device') == 'app' && $response->getStatusCode() < 500) {
            $this->setHttpCode(200, $response);
        }
    }

    protected function setHttpCode($code = null,$response)
    {
        if(!$code)
            $code = $response->getStatusCode();
        if ($code > 10000) {
            $code = intval($code / 100);
        }
        if ($code >= 600) {
            $code = 412;
        }
        $response->setStatusCode($code);
    }

}
