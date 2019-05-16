<?php

namespace Larfree\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
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
        $request->headers->set('X-Requested-With','XMLHttpRequest');
        $request->headers->set('accept','application/json');

//        dd($request->headers);s
        $response = $next($request);
        //跨域
        $response->header('Access-Control-Allow-Origin','*');
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Authorization, Cookie, Accept');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS, DELETE');
        $response->header('Access-Control-Allow-Credentials', 'false');

        $content = $response->getOriginalContent();

        if(method_exists($response,'setEncodingOptions')){
            $response->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        }
        
//dd($content);
        $json=json_decode($response->getContent(),true);

        if(isset($json['code'])){
            $response->setStatusCode($json['code']);
            return $response;
        }
        //没数据的时候
//        if( $content['message']=='Object of class Illuminate\Database\Eloquent\Builder could not be converted to string'){
//            $content=[];
//            $response->setStatusCode(200);
//        }
        if(!$request->ajax())
            return $response;
        //200代码的才是正常返回
        $code = $response->getStatusCode()<400?1:0;

        //对分页进行再处理
        $page = $this->FormatPage($content);
        return $response = $this->FormatJson($response,$content,$page,$code);
    }

    /**
     * 重置json格式
     * @param $response
     * @param $content
     * @param int $code
     * @return mixed
     */
    protected function FormatJson($response,$content,$page,$code=1){
        $StatusCode = $response->getStatusCode();
        $msg = '';
        //ios需要返回200才能解析
//        if($StatusCode!=500)
//            $response->setStatusCode(200);

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
     * 如果是分页类,重构下结果
     * @param &$data
     * @return array 分页数组
     */
    protected function FormatPage(&$data){
        //不是分页类
        if(!is_subclass_of($data,'\Illuminate\Pagination\AbstractPaginator') ){
            return [];
        }
        $data = $data->toArray();
        $page = $data;
        $data=$data['data'];
        unset($page['data']);
        return $page;
    }
}
