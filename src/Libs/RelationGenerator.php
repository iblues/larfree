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
        $reflector   = new ReflectionClass($this->model);
        $this->path  = $reflector->getFileName();
    }

    /**
     * 根据Link生成文档
     * @author Blues
     *
     */
    function generator($echo = 0)
    {
        $link    = array_flip($this->model->getLink());
        $schemas = $this->model->getSchemas();
        foreach ($link as $key => $method) {
            if (!method_exists($this->model, $key)) {
                if (isset($link[$key])) {
                    $filedName = $link[$key];
                    $this->add2File($schemas[$filedName], $key, $echo);
                }
//
            }
        }
    }

    /**
     * 生成到文件
     * @param $link
     * @param $method
     * @param $echo
     * @author Blues
     */
    protected function add2File($link, $method, $echo = 0)
    {
        $relation    = ucfirst($link['link']['model'][0]);
        $model    = ucfirst($link['link']['model'][1]);
        $json = json_encode($link['link']);
        $content     = <<<FILE

    /**
     * {$link['name']} {$link['key']} 关联关系
     * {$json}
     * @link \\{$model}
     * @return \Illuminate\Database\Eloquent\Relations\\{$relation}
     * @author Larfree
     * @override
     */
    public function {$method}(){
        return \$this->callLink(__FUNCTION__);
    }

FILE;
        $fileContent = file_get_contents($this->path);

        $fileContent = substr($fileContent, 0, strripos($fileContent, '}'));
        $fileContent = $fileContent.$content."\n}";
//        dump($fileContent);
        $flag = file_put_contents($this->path, $fileContent);
        if ($echo && $flag) {
            echo "file://".$this->path." add {$method}  Success!\r\n";
        }
        dd();
        return true;
    }


}