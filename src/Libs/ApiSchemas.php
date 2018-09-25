<?php
/**
 * APi核心的相关
 * User: blues
 * Date: 2017/9/20/020
 * Time: 18:03
 */
namespace Larfree\Libs;


class ApiSchemas extends Schemas
{


    /**
     * 获取api允许的入和出的变量
     * @param $name
     * @param $group
     * @param $target
     * @param $extField 补充的变量
     * @return array
     * 期待返回false 所有字段 或者 具体字段结构
     */
    static function getApiAllowField($name,$group,$extField=[]){

        if(is_null($extField)){
            return false;
        }
        $schemas = self::getSchemas($name);
        //如果主结构不存在,代表是虚拟的表
        if($schemas!==false) {

            if (strlen($extField) > 0) {
                $extField = self::formatFields($extField);
                $schemas = self::getFilterField($schemas, $extField);
            }
        }else{
            //那么就直接使用$extField的结构
            $schemas = self::formatFields($extField);
        }
        return $schemas;
    }

    /**
     * 获取API对应验证规则
     * @param $name
     * @param $method
     * @return array
     */
    static function getValidate($name,$param=[]){
        $schemas = self::getSchemas($name);

        //当主文件不存在的时候,$param
        if($schemas!==false) {
            //如果有传入自定义参数,合并
            if (strlen($param) > 0) {
                $param = self::formatFields($param);
//               print_r($param);
                $schemas = self::getFilterField($schemas, $param);
            }
        }else{
            $schemas = self::formatFields($param);
        }
        $validate =[];
        //提取
        if($schemas) {
            foreach ($schemas as $item) {
                if (isset($item['rule'])) {
                    $validate[$item['key']] = self::formatFieldValidate($item['rule']);
                }
            }
        }

//        exit();
        //最后处理下格式,方便让laravel直接调用
        $validate = self::formatValidate(array_filter($validate),$schemas);
        return $validate;
    }


    /**
     * 获取APi文件夹的字段. 现在废除api文件夹.直接在控制器中写就定义 更加方便
     * @param $name
     * @param $target
     * @return array
     */
//    static function getApiFields($name,$group,$target){
//        $api = include dirname(dirname(dirname(__FILE__))).'/config/Schemas/Apis/'.self::fomartName($name).'.php';
//        $target = strtolower($target);
//        if(@$_ENV['ADMIN'])
//            $group = 'admin.'.$group;
//        return  self::formatFields(@$api[$group][$target]);
//    }





    /**
     * rule=>require
     * rule=>
     * rule=>['required','min:2'=>'最小2位','max:10'=>'最大10位'],
     * 转成[a=>b]的格式
     * @param $validate
     */
    static protected function formatFieldValidate($validate){

            $newValidate=[];
            if(is_string($validate)){
                $newValidate[$validate]='';
            }elseif(is_array($validate)) {

                array_walk($validate, function (&$item,&$key)use(&$newValidate) {
                    if(is_numeric($key)){
                        $key = $item;
                        $item = '';
                    }
                    $newValidate[$key]=$item;
                });
            }
            return $newValidate;
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
        //过滤rule为空的
        $defValidate = array_filter($defValidate,function($item){
            if($item)
                return true;
            else
                return false;
        });
        $newValidate=['rules'=>$defValidate,'msg'=>$messages];
        return $newValidate;
    }

}