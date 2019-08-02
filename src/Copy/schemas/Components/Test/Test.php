<?php
/**
 * 其他可以用组建默认的参数
 * 也可以自己指定
 */
return [
    'detail'=>[
        'line'=> [
            'title'=>'统计图表',
            'sub_title'=>'统计副标题',
            'x'=>[
                'format'=>'%Y-%m-%d',
                'name'=>'创建日期',
            ],
            'y'=>[
                [
                    'name'=>'测试1',
//                    'callback'=>'filter',//过滤时间,调用当前model的对应函数进行再
                    'sql'=>[
                        'field'=>'abs(avg(id)) ', //原生sql统计语句  加密传输
                        'where'=>'id>1',//原生sql 加密传输
                    ]
                ],[
                    'name'=>'测试2',
                    'sql'=>[
                        'field'=>'sum(id)/2', //原生sql统计语句  加密传输
                        'where'=>'id > 1',//原生sql 加密传输
                    ]
                ],

            ]
        ],
        'table'=>[
            'config'=>[
                'action'=>[
                    'del'=>null,
                ]
            ],
            'search'=>[
                'id',
                'title',
                'user_id',
                'select',
                'price',
                'datetime',
            ],
            'fields'=>[
                'id',
                'title',
                'content',
                'user_id',
                'has_many',
                'select',
                'upload',
                'file',
                'price',
                'float',
                'ip',
//                'timestamp',
//                'datetime',
                'created_at',
//                'updated_at'
             ],
        ],
        'add'=>[
            'fields'=>[
                '随便不重复'=>[
                    'name'=>'分组1',
                    'group_children'=>
                        ['title'=>['name'=>'换标题'],'user_id'],
                ],
                'group_2'=>[
                    'name'=>'分组2',
                    'group_children'=>['price','float','ip'],
                ],
                'has_many',
                'content',
    //            'user_id',
    //            'select',
                'upload',
                'file',
                'timestamp',
                'datetime'
             ],
        ],
        'edit'=>[
            'fields'=>[
                'title',
                'has_many',
            'content',
            'user_id',
            'select',
            'upload',
            'file',
            'price',
            'float',
            'ip',
            'timestamp',
            'datetime'
            ],
        ],
        'detail'=>[
            'fields'=>[
                'id',
            'title',
            'content',
            'user_id',
            'select',
            'upload',
            'file',
            'price',
            'float',
            'ip',
            'timestamp',
            'datetime',
            'created_at',
            'updated_at'
            ],
        ],
    ],
];