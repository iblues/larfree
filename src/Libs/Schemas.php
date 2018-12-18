<?php
/**
 * 用来解析配置的核心
 * User: blues
 * Date: 2017/9/20/020
 * Time: 18:03
 */
namespace Larfree\Libs;


class Schemas
{
    /**
     * 获取主结构
     * @param $name
     * 返回false 就代表不存在
     */
    public static function getSchemas($name){
        $fileName = config_path().'/Schemas/Schemas/'.self::fomartName($name).'.php';
//        echo $fileName;
//        echo "\r\n";
        if(file_exists($fileName))
            $data = include $fileName;
        else
            return false;
        //如果没有设就默认为id;
        $data['pk'] = isset($data['pk'])?$data['pk']:'id';
        if( $data['pk'] && @$data['detail'][$data['pk']] ){
            $data['detail'][$data['pk']]['pk']=true;
        }
        foreach($data['detail'] as $k=>$v){
            $data['detail'][$k]['key']=$k;
        }
        array_walk($data['detail'],array('self','loadComponentConfg'));
        return $data['detail'];
    }


    /**
     * componet那边可能对config会进行额外处理.
     * 会调用对于的config方法
     */
    static public function loadComponentConfg(&$config){
        //自动设置Multi
        if(!isset($config['multi']) && isset($config['link']) && isset($config['link']['model']) ){
            switch ($config['link']['model'][0]){
                case 'hasMany':
                case 'belongsToMany':
                    $config['multi']=true;

                    break;
                default:
                    $config['multi']=false;
                    break;
            }
        }
        //自动设置as字段
        if(isset($config['link']) && !isset($config['link']['as'])  && isset($config['link']['model'])) {
            switch ($config['link']['model'][0]) {
                case 'hasMany':
                case 'belongsToMany':
                    //这2个链表的不用管
                    break;
                default:
                    if( substr($config['key'],-3) == '_id')
                        $config['link']['as'] = substr($config['key'],0,-3);
                    break;
            }
        }
        $class='App\Components\Field\\'.ucfirst($config['type']);
        if(method_exists($class,'config')){
            $config = $class::config($config);
        }
    }


    static protected function array_merge($def,$new){
        $config='';
        if (isset($new['config'])) {
            $config = self::array_merge($def['config'], $new['config']);
        }
        $def = array_merge($def,$new);
        if($config)
            $def['config']=$config;
//        dump($def);
        return $def;
//        foreach($def as $k=>$v){
//            if(is_array($v) ){
//                $def[$k] = self::array_merge($def[$k],@$new[$k]);
//            }else{
//                if(isset($new[$k])){
//                    echo $new[$k];
//                    $def[$k]=$new[$k];
//                }
//            }
//        }
//        return $def;
    }
    /**
     * Component和api通用
     * 进行字段和规则的合并.
     *   '*',代表所有
     *   'upload'=>['v'=>'min:4'], //增加并重写
     *   'select'=>false,//排除upload
     *   'user_id' //增加
     * @param $schemas
     * @param $apiSchemas
     * @return array
     */
    static protected function getFilterField($schemas,$apiSchemas){

        $newSchemas=[];
        //如果有*就全部加上
        if(in_array('*',array_keys($apiSchemas))){
            $newSchemas = $schemas;
//            unset($apiSchemas['*']);
        }

        //没有额外的,就用配置的
        if(!is_array($apiSchemas)){
            return $newSchemas;
        }

        //如果有排除字段,也全部加上,再来排除
        if(in_array(false,$apiSchemas)){
            $newSchemas = $schemas;
        }


        //如果直接没有值.连*都没有 认为都没有
        if($apiSchemas) {
            foreach ($apiSchemas as $k => $v) {
                if($v=='*'){
                    continue;
                }

                //等于false 就排除该字段
                if($v===false){
                    unset($newSchemas[$k]);
                }elseif($v){
                    //重写字段结构
                    if(@$schemas[$k]){
                        $newSchemas[$k] = array_merge($schemas[$k],$v);
                    }
                    else{
                        $newSchemas[$k]=$v;
                    }

                }
                //添加进去的字段
                if( is_numeric($k) ){
                    if(!@$newSchemas[$v]) {
                        $newSchemas[$v] = isset($schemas[$v])?$schemas[$v]:'';
                    }
                }
            }

            return $newSchemas;
//            return $fields = array_flip($fields);
        }else{
            return [];
        }
    }



    /**
     * 调整下字段规则
     * 如果没有值就返回false
     * 把[A,B=>'123']处理成[A=>'',B=>'123']方便接下来进行字段处理
     * @param $validate
     * @param $defValidate
     * @return array || *
     */
    static protected function formatFields($schemas){
        $new = [];
        //如果有单独设置字段就处理
        if (isset($schemas) && is_array($schemas)) {
            foreach($schemas as $k=>$v) {
                if($v===false){
                    $new[$k] = false;
                }elseif (is_array($v)) {
                    if(!isset($v['key']))
                        $v['key']=$k;
                    $new[$k] = $v;
                } else {
                    $new[$v] = ['key'=>$v];
                }
            }
        }else{
            //没有设置,那就不过滤
            return false;
        }
        return $new;
    }


    /**
     * 处理自定义消息
     * @param $defValidate
     * @param $new
     * @param $method
     * @return array
     */
    static protected function formatValidate($defValidate,$fields){


        $messages = [];
        //处理格式,自定义消息
        array_walk($defValidate,function(&$item,$key)use(&$messages){
            $rule = array_keys($item);
            foreach($item as $k=>$v){
                if($v) {
                    $k = explode(':',$k)[0];
                    $messages[$key .'.' .$k] = $v;
                }
            }
            $rule = implode('|',$rule);
            $item = $rule;
        });
        $newValidate=['rules'=>$defValidate,'msg'=>$messages];
        return $newValidate;
    }

    /**
     * 处理用于获取文件的文件名  下划线转驼峰  点转/
     * @param $file
     * @return string
     */
    static protected function fomartName($file){
        if(stripos($file,'.')){
            $file =str_ireplace('.','/',$file);
        }
        if(stripos($file,'/')) {
            return ucfirst(lineToHump(dirname($file))) .'/'. ucfirst(lineToHump(basename($file)));
        }else{
            return ucfirst(lineToHump(basename($file)));
        }
    }


    /**
     * 获取所有的配置
     * @return array
     */
    static function getAllSchemas(){
        $path = config_path().'/Schemas/Schemas';
        $list = dirToArray($path);
        return $list;
    }

    /**
     * 获取所有的配置
     * @return array
     */
    static function getAllSchemasConfig(){
        $path = config_path().'/Schemas/Schemas';
        $list = self::getAllSchemas();
        $lists=[];
        foreach ($list  as  $module=>$file) {
            $lists[] = array_map(function ($file) use ($path,$module) {
                $filePath = $path.'/'.$module. '/' . $file;
                $data = include($filePath);
                $data['key'] = humpToLine($module.'.'.basename($file, '.php'));
                $data['key']  = ucfirst($data['key']);
                return $data;
            }, $file);
        }
        return $lists;
    }
    /**
     * 获取所有的配置
     * @return array
     */
    static function getAllConfig(){
        $path = config_path().'/Schemas/Schemas/Config';
        $list = scandir($path);
        array_shift($list);
        array_shift($list);
        $lists = array_map(function($file)use($path){
            $data= include($path.'/'.$file);
            $data['key']=humpToLine(basename($file,'.php'));
            return $data;
        },$list);
        return $lists;
    }

}