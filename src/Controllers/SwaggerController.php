<?php

namespace Larfree\Controllers;

use App\Http\Controllers\Api\TestController;
use Illuminate\Http\Request;
use Larfree\Libs\Swagger;
use Illuminate\Routing\Controller as Controller;
use Larfree\Models\System\SystemDictionary;

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
                            'example'=>@$this->getMd5Content($description[1])['content']
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

        $config = SystemDictionary::get();
        $dictionary = ($config->toArray());//先获取所有的

        $return = json_decode( trim(file_get_contents($path)) ,1);
        $content = json_decode($return['content'],1);
        //给返回值添加注释
        $content = json_encode( $this->addReturnCode($content['data'],$dictionary,'') ,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $return['content'] = $content;
        return $return;

    }

    public function addReturnCode($content,$dictionary,$model=''){
        foreach ($content as $k=>$v){
//            $val = SystemDictionary::where('key',$k)->orderBy('id','desc')->first();
            foreach ($dictionary as $dict){
                if($dict['key'] == $k){
                    $val = $dict;
                    break;
                }
            }
            if(!is_array($v)) {
                if($val['value']!=null) {
                    $string = '  说明 : ' . $val['value'] ?? ' [资料缺失] ';
                }
                if($string)
                    $content[$k] =$v.str_pad('',35-strlen($k)-mb_strlen($v),' ') .$string;
            }else{
                $content[$k] = $this->addReturnCode($v,'');
            }
        }
        return $content;
    }
    public function getParameters($doc){
        $data = $doc['paths'];
        $methods=['get','post','put','delete','patch','delete'];
        foreach($data as $key=>&$swg){
            foreach($swg as $k=>&$action){
                if(in_array($k,$methods)){
                    $className = $action['x-class'];
                    $namespace = $action['x-file']['namespace'];
                    $method = $action['x-method'];
                    $class = $namespace.'\\'.$className;

                    $controller = \App::make($class);
                    if(method_exists($controller,'getParamDefine')) {
                        $param = $controller->getParamDefine($method);
                        $this->createParamDoc($param, @$action, $k);
                    }
                }
            }
        }
        $doc['paths']=$data;
        return $doc;
    }

    /**
     * 生成对于的文档
     */
    protected function createParamDoc($params,&$apiDoc,$method = 'index'){

        $existParam= @$apiDoc['parameters'];

        if(!$params)
            return [];

        if(!$existParam)
            $existParam=[];

        $doc = $existParam;
        foreach ($params as $param){

            if($param['key']=='*'){
                continue;
            }

            switch (@$param['type']){
                case 'select':
                    $type = 'integer';
                    break;
                case 'number':
                    $type = 'integer';
                    break;
                default :
                    $type = 'string';
            }

            if(@$param['sql_type']){
                $type = $param['sql_type'];
            }

            $name = isset($param['name'])?$param['name']:$param['key'];
            if(@$param['default']){
                $name .='<br />参考值:'.$param['default'];
            }
            if(@$param['rule'])
                $name .=  ' <br />验证规则:'. @print_r($param['rule'],1);

            if(@$param['tip'])
                $name .=  ' <br />提示:'. $param['tip'];

            if(@$param['param'])
                $name .=  ' <br />模型:'. '<pre><code>'.print_r($param['param'],1).'</code></pre>';

            //查询方式
            $in = 'query';
            if($param['key']=='id'){
                $in = 'path';
            }

            $example = @$param['example'];
            if(!$example)
            {
                $example = @$param['default'];
            }


            $docParam = [
                'name'=>$param['key'],
                'in'=>$in,
                'description'=>$name
                ,
                'required'=>false,
                'schema'=>[
                    'type'=>$type
                ],
                'example'=>$example,
            ];

            $exist=false;
            //合并自定义的参数
            foreach ($existParam as $k=>$v){
                if($v['name']== $param['key']){
                    $doc[$k] = array_merge($docParam,$v);
                    $exist = true;
                }
            }
            if(!$exist){
                $doc[] = $docParam;
            }


        }


        //post下可以排除id这个字段选项
        if($method == 'post'){
            foreach ($doc as $k=>$v){
                if($v['name']== 'id'){
                    unset($doc[$k]);
                }
            }
        }


        //put何
        $body = [];
        if($method=='post' || $method=='put'){
            foreach ($doc as $k=>$v){
                //query就是body
                if($v['in']== 'query'){
                    $body[] = $doc[$k];
                    unset($doc[$k]);
                }
            }
        }

        $doc = array_values($doc);
        if($body)
            $apiDoc['requestBody']['content']['application/json']['schema']=$this->ParamToBodyParam($body);
        $apiDoc['parameters'] = $doc;
    }


    /**
     * 把普通输入参数转换成body请求的参数
     * @param array $bdoy
     * @return mixed
     */
    protected function ParamToBodyParam(array $body){
        $properties = [];
        $example = [];
        foreach ($body as $k=>$v){
            $param=[];
            $param['title']=@$v['name'];
            $param['description']=@$v['description'];
            $param['type']=@$v['schema']['type'];
            $properties[@$v['name']]=$param;
            $example[$v['name']]=$v['example']??'';
        }
        $doc = ['properties'=>$properties,'type'=>'object','example'=>$example];

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
