<?php
/**
 * 用来解析配置的核心
 * User: blues
 * Date: 2017/9/20/020
 * Time: 18:03
 */
namespace Larfree\Libs;


use Illuminate\Support\Arr;
use Larfree\Exceptions\SchemasException;

class Schemas
{
    /**
     * 获取主结构
     * @param $name
     * 返回false 就代表不存在
     */
    public static function getSchemas($name){
        $fileName = schemas_path('Schemas').'/'.self::fomartName($name).'.php';
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
        array_walk($data['detail'],array('self','loadLinkConfig'));
        return $data['detail'];
    }


    /**
     * 处理Link相关的数据
     */
    static public function loadLinkConfig(&$config){
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
        //自动设置as字段, 如果没有设置as
        if(isset($config['link']) && !isset($config['link']['as']) ) {

            //如果没有设置model. key就是as
            if(!isset($config['link']['model'])){
                if(!is_array($config['link'])){
                    throw new SchemasException(json_encode($config,JSON_UNESCAPED_UNICODE).'link字段应为array');
                }
                $config['link']['as'] = $config['key'];
            }else {
                //如果设置了model 需要判断下
                switch ($config['link']['model'][0]) {
                    case 'hasMany':
                    case 'belongsToMany':
                        //这2个链表 表名就是自己
                        $config['link']['as'] = $config['key'];
                        break;
                    default:
                        //判断下如果是_id结尾的.智能处理下 去掉_id  user_id=user
                        if (substr($config['key'], -3) == '_id')
                            $config['link']['as'] = substr($config['key'], 0, -3);
                        else {
                            //否则自动加link 避免跟当前字段同名
                            $config['link']['as'] = $config['key'] . '_link';
                        }
                        break;
                }
            }
        }

        //再读取下Components相关. 看有没有需要处理的
        $larfreeClass = 'Larfree\Components\Field\\'.ucfirst($config['type']);
        $class='App\Components\Field\\'.ucfirst($config['type']);
        if(method_exists($larfreeClass,'config')){
            $config = $larfreeClass::config($config);
        } elseif (method_exists($class, 'config')) {
            $class::config($config);
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
        $path = schemas_path('Schemas');
        $list = dirToArray($path);
        return $list;
    }

    /**
     * 搜索有as等于key
     * @param $key
     * @param $Schemas
     * @return mixed
     * @author Blues
     *
     */
    static function searchLinkAs($key,$Schemas){
        foreach ($Schemas as $schema){
            if( Arr::get($schema,'link.as') == $key){
                return $schema;
            }
        }
    }

    /**
     * 获取所有的配置
     * @return array
     */
    static function getAllSchemasConfig(){
        $path = schemas_path().'/Schemas/Schemas';
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
        $path = schemas_path().'/Schemas/Config';
        $list = scandir($path);
        array_shift($list);
        array_shift($list);
        $lists = array_map(function($file)use($path){
            $data= include($path.'/'.$file);
            $data['key']=humpToLine(basename($file,'.php'));
            foreach ($data['detail'] as $key=>$val){
                //mock数据
                $data['detail'][$key]['value']=config('system.'.$data['key'].'.'.$key,'');
            }
            return $data;
        },$list);
        return $lists;
    }

}
