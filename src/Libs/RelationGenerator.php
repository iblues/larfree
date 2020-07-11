<?php


namespace Larfree\Libs;

use Larfree\Models\Api;
use ReflectionClass;

/**
 * 关联关系生成器.
 * 通过反射
 * @author Blues
 * Class RelationGenerator
 * @package Larfree\Libs
 */
class RelationGenerator
{
    /**
     * 关联关系
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author Larfree
     * @override
     */
//    public function user(){
//        return $this->callLink(__FUNCTION__);
//    }
    protected $model;
    protected $path;

    function __construct(Api $model)
    {
        $this->model = $model;
        $reflector = new ReflectionClass($this->model);
        $this->path = $reflector->getFileName();
    }

    /**
     * 根据Link生成文档
     * @author Blues
     *
     */
    function generator()
    {
        $link = array_flip($this->model->getLink());
        dump($link);
        $schemas = $this->model->getSchemas();
        foreach ($link as $method) {
            if (!method_exists($this->model, $method)) {
                if(isset($link[$method])){
                    $key = $link[$method];
                    $this->add2File($schemas[$key],$method);
                }
//
            }
        }
    }

    /**
     * 生成到文件
     * @param $method
     * @author Blues
     *
     */
    protected function add2File($link,$method)
    {

        $relation = ucfirst($link['link']['model'][0]);
        $content =<<<FILE

    /**
     * {$link['key']} {$link['name']} 关联关系
     * @return \Illuminate\Database\Eloquent\Relations\\{$relation}
     * @author Larfree
     * @override
     */
    public function {$method}(){
        return \$this->callLink(__FUNCTION__);
    }

FILE;
        $fileContent = file_get_contents($this->path);

        $fileContent = substr($fileContent,0,strripos($fileContent,'}'));
        $fileContent = $fileContent.$content."\n}";
//        dump($fileContent);
        file_put_contents($this->path,$fileContent);

    }



}