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
        $response = $next($request);


        $request->headers->set('X_REQUESTED_WITH','XMLHttpRequest');
        //跨域
        $response->header('Access-Control-Allow-Origin','*');
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Authorization, Cookie, Accept');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, PTIONS, DELETE');
        $response->header('Access-Control-Allow-Credentials', 'false');

        $content = $response->getOriginalContent();
        //如果status已经有了 说明apiResource处理了 就不处理了
        if(isset($content['status'])){
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
        $code = $response->getStatusCode()<400?0:1;

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
            $msg=current(current($content['errors']));
            $content = $content['errors'];
        }

//        if($StatusCode==401)
//            $code=-10;
//        dd($response);
//        exit();
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
