<?php

namespace Larfree\Controllers;

use App\Http\Controllers\Api\TestController;
use Illuminate\Http\Request;
use Larfree\Libs\Swagger;
use Illuminate\Routing\Controller as Controller;

//可以这么用变量
//define("API_HOST", ($env === "production") ? "example.com" : "localhost");
//SWG\Swagger(host=API_HOST)
class SwaggerController extends Controller
{

    protected $path;
    /**
     * 返回JSON格式的Swagger定义
     *
     * 这里需要一个主`Swagger`定义：
     * @SWG\Swagger(
     *   schemes={"http"},
     *   host="larfree.dev",
     *   basePath="/api",
     *   @SWG\Info(
     *     title="Larfree自动文档",
     *     version="1.0.0"
     *   )
     * )
     *  定义登录的方式
     *  @SWG\SecurityScheme(
     *   securityDefinition="Authorization",
     *   type="apiKey",
     *   in="header",
     *   name="Authorization"
     * )
     */
    public function getJSON()
    {
        // 你可以将API的`Swagger Annotation`写在实现API的代码旁，从而方便维护，
        // `swagger-php`会扫描你定义的目录，自动合并所有定义。这里我们直接用`Controller/`
        if(!$this->path)
            apiError('Path未定义');
        $swagger = \OpenApi\scan($this->path);

        $swagger->paths = $this->parseAction($swagger->paths);

        //转成array
        $doc = json_decode(json_encode($swagger),true);
        $doc = $this->walkResponses($doc);


//        $doc['paths']['/test/test/']['get']['responses'][200]['content']['application/json']['schema']=[
//            'type'=>'json',
//            'example'=>'{
//"data": [],
//"code": 412,
//"status": 0,
//"msg": "Path未定义"
//}',
//        ];

//        $doc['paths']['/test/test/']['get']['responses'][200]['content']['字典']['schema']=[
//            'type'=>'object',
//            'properties'=>[
//                'name'=>[
//                    'type'=>'string',
//                    'description'=>'测试',
//                    'example'=>1
//                ],
//                'name2'=>[
//                    'type'=>'array',
//                    'description'=>'测试',
//                    'items'=>[
//                        'type'=>'string',
//                        'description'=>'测试',
//                        'enum'=>[
//                           '1:测试',2,3
//                        ],
//                        'example'=>1
//                    ],
//                ],
//            ]
//        ];

        $doc = $this->getParameters($doc);
        return response()->json($doc, 200);
    }

    public function walkResponses($doc){
        array_walk($doc['paths'],function(&$url){
            array_walk($url,function(&$method){
                array_walk($method['responses'],function(&$responses){
                    if(stripos($responses['description'],'md5:')!==false){

                        $description = explode('md5:',$responses['description']);
                        $responses['description'] = $description[0];
                        $responses['content']['application/json']['schema']=[
                            'type'=>'json',
                            'example'=>$this->getMd5Content($description[1])['content']
                        ];
                    }

                });
            });
        });
        return $doc;
    }

    protected function getMd5Content($md5){
        $tmpPath = storage_path('apiReturn/tmp/');
        $usePath = storage_path('apiReturn/use/');

        $file = $md5.'.json';
        if( file_exists($usePath.$file) ){
            $path = $usePath.$file;
        }elseif(file_exists($tmpPath.$file) ){
            copy($tmpPath.$file,$usePath.$file);
            $path = $usePath.$file;
        }else{
            return 'json undefined';
        }

        return json_decode( trim(file_get_contents($path)) ,1);

    }

    public function getParameters($doc){
        $data = $doc['paths'];
        $methods=['get','post','put','delete','patch','delete'];
        foreach($data as $key=>$swg){
            foreach($swg as $k=>$action){
                if(in_array($k,$methods)){
                    $className = $action['x-class'];
                    $namespace = $action['x-file']['namespace'];
                    $method = $action['x-method'];
                    $class = $namespace.'\\'.$className;
                    $controller = \App::make($class);
                    $validate = $controller->getParamDefine($method);
//                    dump($validate);
                }
            }
        }
        return $doc;
    }
    /**
     * 获取对应的文件路径出来
     */
    public function parseAction($swagger){
        $methods=['get','post','put','delete','patch','delete'];

        foreach($swagger as $key=>$swg){
            foreach($swg as $k=>$action){
                if(in_array($k,$methods)){
                    if(!is_object($action))
                        continue;

                    $doc = json_decode(json_encode($swagger),true);

                    if(@$action->_context) {
                        $file = $action->_context->getRootContext();
                        //获取对于注释的函数方法
                        $method =  $action->_context->method;

                        $swagger[$key]->$k->x=[];
                        $swagger[$key]->$k->x['method']=$method;
                        $swagger[$key]->$k->x['file']=$file;
                        $swagger[$key]->$k->x['class']=$action->_context->class;
//                        echo $file['namespace'];
//                        $class = new $file['namespace']();
                    }
                }
            }
//            dump($swg);
        }
        return $swagger;
    }

}
