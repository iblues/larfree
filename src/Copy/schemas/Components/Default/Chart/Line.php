<?php
/**
 * 现状图统计
 */
use Illuminate\Support\Facades\Crypt;
return function($data,$component,$target){

    if(!array_filter($data))
        apiError('Schemas not exits');
    $def = [
        'title'=>'',
        'sub_title'=>'',
        'y_unit'=>'',
        'url'=>route('admin.api.root').'/{$COMPONENT_API}/?@chart={$COMPONENT}|'.$component.'.'.$target,
        'type'=>['line','bar'],
        'x'=>[
            'name'=>'日期',
            'dataType'=>'date',
            'format'=>'%Y-%m-%d',
            'field'=>'created_at',  //
            'max'=>'',
            'min'=>'',
        ],
        'y'=>[

        ]
//        'y'=>[
//            'dataType'=>'number',
//            'name'=>'最低气温',
//            'data'=>[
//                'field'=>'abs(avg(test2))/10 ', //原生sql统计语句  加密传输
//                'where'=>'id > 1',//原生sql 加密传输
//            ]
//        ]//可能多个y,读取几次
    ];

    $config = array_merges($def,$data);
    if(!isset($config['y'])){
        throw new \Larfree\Exceptions\SchemasException('y参数缺失',$config);
    }
    array_walk($config['y'],function(&$y,$k)use($config,$component,$target){
        $y['dataType'] = isset($y['dataType'])?:'number';

    });

    return $config;
};
