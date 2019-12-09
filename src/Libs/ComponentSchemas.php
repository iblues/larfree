<?php
/**
 * APi核心的相关
 * User: blues
 * Date: 2017/9/20/020
 * Time: 18:03
 */
namespace Larfree\Libs;


class ComponentSchemas extends Schemas
{

    static function getSchemasConfig($name,$target)
    {
        $target = strtolower($target);


        $file = schemas_path('Components').'/'. self::fomartName($name) . '.php';

        $GlobalSchemas = self::getSchemas($name);//主结构
        if (file_exists($file)) {
            $Schemas = include $file;
            $Schemas = @$Schemas['detail'][$target];
            $field = self::formatFields(@$Schemas['fields']);
            $search = self::formatFields(@$Schemas['search']);
            $advSearch = self::formatFields(@$Schemas['adv_search']);
            $filter_field = [];
            //合并结构
            if($field) {
                foreach ($field as $key => $f) {
                    //如果有group_children字段,那是分组用的
                    if (!isset($f['group_children'])) {
                        $filter_field[$key] = '';//用来筛选字段
                        if ($f) {
                            $GlobalSchemas[$key] = $f + Arr::get($GlobalSchemas,$key,[]);
                        }
                    } else {
                        //是分组的,循环一次,合并结构
                        foreach ($f['group_children'] as $group_key => $group_field) {
                            if (is_array($group_field)) {
                                $filter_field[$group_key] = '';
                                $GlobalSchemas[$group_key] = $group_field + Arr::get($GlobalSchemas,$group_key,[]);
                            } else {
                                $filter_field[$group_field] = '';
                            }
                        }
                    }//endif
                }
                $filter_field = array_intersect_key($GlobalSchemas, $filter_field);


                //如果有分组的,对分组数据进行重构,以及字段排序
                array_walk($field, function (&$val, $key) use ($filter_field) {
                    if (isset($val['group_children'])) {
                        $group_children=[];
                        foreach($val['group_children'] as $k=>$v){
                            if(!is_array($v))
                                $group_children[$v] = $filter_field[$v];
                            else
                                $group_children[$k] = $filter_field[$k];
                        }
                        $val['group_children'] = $group_children;
                    } else {
                        $val = $filter_field[$key];
                    };
                });
            }
            //带有分组和其他结构的
            $Schemas['component_fields'] = $field;
            //转成1维数组的
            $Schemas['fields'] = $filter_field;
            //搜索的结构
            if(@$search) {
                foreach($search as $key =>$f){
                    if($f){
                        $GlobalSchemas[$key]=$f+$GlobalSchemas[$key];
                    }
                }
                $search = array_intersect_key($GlobalSchemas, $search);
                if($advSearch)
                    $advSearch = array_intersect_key($GlobalSchemas, $advSearch);
                $Schemas['search'] = $search;
                if($advSearch)
                    $Schemas['adv_search']= $advSearch;
            }
            $Schemas = array_filter($Schemas);
            return $Schemas;

        }else{
            $field = self::getSchemas($name);
            $Schemas  = ['fields'=>$field,'component_fields'=>$field];//主结构
        }
        return $Schemas;
    }

    /**
     * 获取组建的默认配置
     * @param $path  ui.tab
     * @return mixed
     */
    static public function getComponetDefConfig($path,$config,$target=''){
        $name = str_replace('.','/',$path);
        $cpath = schemas_path().'/Components/Default/'.self::fomartName($name).'.php';
        if(file_exists($cpath)) {
            $func = include schemas_path(). '/Components/Default/' . self::fomartName($name) . '.php';
            return $func($config,$path,$target);
        }else
            return [];
    }

    /**
     * 获取组建的最终参数
     * @param $url test.test|chart.line.line
     */
    static public function  getComponentConfig($schemas,$action){

        $target = explode('.',$action);
        //根据chart.line.chart  chart.line  chart 3种不同模式,进行解析

        switch (count($target)){
            case 1:
                $config = ComponentSchemas::getSchemasConfig($schemas,$target[0]);
                break;
            case 2:
                $config = ComponentSchemas::getSchemasConfig($schemas,$target[1]);
                $config = ComponentSchemas::getComponetDefConfig($action,$config,$target[1]);
                break;
            case 3:
                $config = ComponentSchemas::getSchemasConfig($schemas,$target[2]);
                $action = implode('.',array_slice($target,0,2));
                $config = ComponentSchemas::getComponetDefConfig($action,$config,$target[2]);
                break;
            default:
                throw new \Exception('参数格式错误');

        }
        return $config;
    }


}
