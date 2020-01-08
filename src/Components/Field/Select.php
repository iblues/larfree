<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/14/014
 * Time: 17:50
 */
namespace Larfree\Components\Field;
use Larfree\Components\Components;

class Select extends Components
{

    static public function component(&$path,&$config,$model){
        if(isset($config['link']) ) {
            $link = $config['link'];
            //编辑和搜索需要读取备选
            if (in_array($config['action'],['edit','search'])) {
                switch ($link['model'][0]) {
                    case 'belongsTo':
                    case 'belongsToMany':
                        $model = $link['model'][1];
                        $model = new $model;
                        if (isset($link['select'])) {
                            $model = $model->field($link['select']);
                        }
                        if (isset($link['where'])) {
                            $model = $model->where($link['where']);
                        }
                        //读取数据
                        $option = $model->take(500)->get()->toArray();
                }
            }else{
                //直接输出的 就把关联查询到的返回
                if($config['link']) {
                    //直接把关联查询到的返回
                    $linkName = @$config['link']['as']?$config['link']['as']:$config['key'].'_link';
                    //特殊处理
                    if($config['link']['model'][0]=='belongsToMany')
                            $linkName = $config['key'];
                    $option = $config['data'][$linkName];

                }
            }

            //如果有数据 赋值给option
            if(isset($option)) {
                $option = self::getSelectField($option, $link['field']);
                $config['option'] = $option;
            }else{
                $config['option']  = [];
            }

//            @if(isset($multi)&&$multi)
        }
        parent::component($path,$config,$model);

//        $config['data']=$response;
    }


    static public function getAttribute($config,&$array){
        if(isset($config['option'])){
            $value  = $array[$config['key']];
            return $array[$config['key'].'_link'] = @$config['option'][$value];
        }
    }

    /**
     * 根据selectField筛选出需要的字段
     * @param $option
     * @param $field
     */
    static protected function getSelectField($option,$field){
        //如果是1维数组 就封装成多维
        if(!is_array(head($option))){
            $option = [$option];
        }
        $options = [];
        $key = array_shift($field);
        foreach ($option as $k => $v) {
            $data=[];
//   echo $v['id'];
//   exit();
            foreach ($field as $f){
                $data[]=@$v[$f];
            }
            $options[@$v[$key]] = implode(' ',$data);
        }
        return $options;
    }
}
