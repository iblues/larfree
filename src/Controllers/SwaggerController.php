<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\TestController;
use Illuminate\Http\Request;
use Larfree\Libs\Swagger;

//可以这么用变量
//define("API_HOST", ($env === "production") ? "example.com" : "localhost");
//SWG\Swagger(host=API_HOST)
class SwaggerController extends Controller
{

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
     *
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
        $swagger = \Swagger\scan(app_path('Http/Controllers/'));

        $swagger->paths = $this->parseAction($swagger->paths);

        //转成array
        $doc = json_decode(json_encode($swagger),true);
        $doc['paths']['/test/{id}']['get']['responses'][403]['schema']=[
            'type'=>'object',
            'properties'=>[
                'name'=>[
                    'type'=>'string',
                    'description'=>'测试',
                    'example'=>1
                ],
                'name2'=>[
                    'type'=>'array',
                    'description'=>'测试',
                    'items'=>[
                        'type'=>'string',
                        'description'=>'测试',
                        'enum'=>[
                           '1:测试',2,3
                        ],
                        'example'=>1
                    ],
                ],
            ]
        ];



        $doc = $this->getParameters($doc);
        return response()->json($doc, 200);
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

                    $controller = \App::make(\App\Http\Controllers\Api\TestController::class);
                    $validate = $controller->getValidation($method);
                    dump($validate);

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
//                    $action = @$methods[$method];
//                    dump($action);
//                    $doc = json_decode(json_encode($swagger),true);
//                    print_r($action);
                    if(@$action->_context) {
                        $file = $action->_context->getRootContext();
                        $method =  $action->_context->method;
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
