<?php
return [
    'title'=>'测试配置',
    'content'=>'配置的简介<br />配置的简介',
    'detail'=>[
        'title'=>[
            'name'=>'标题2',
            'tip'=>'',
            'type'=>'text',
        ],
        'content'=>[
            'name'=>'详情',
            'tip'=>'',
            'type'=>'textarea',
        ],
        'user_id'=>[
            'name'=>'用户id',
            'tip'=>'',
            'type'=>'select',
            'link'=>[
                'model'=>[
                    'belongsTo',
                    'App\\Models\\Common\\CommonUser',
                    'user_id',
                    'id'
                ],
                'as'=>'user',
//                    'where'=>[
//                        'status'=>0
//                    ],
                'select'=>['id','name'],
                'field'=>['id','name'],
            ],
        ],
        'select'=>[
            'name'=>'普通下拉',
            'tip'=>'',
            'type'=>'select',
//                'option'=>[1=>'数据1',2=>'数据2',3=>'数据3',4=>'数据4']
            'link'=>[
                'model'=>[
                    'belongsTo',
                    'App\\Models\\Common\\CommonUser',
                    'select',
                    'id',
                ],
//                    'where'=>[
//                        'status'=>1
//                    ],
                'select'=>['id','name','phone'],
                'field'=>['id','name','phone'],
            ],
//                'multi'=>true,
        ],
        'mselect'=>[
            'name'=>'多选下拉',
            'tip'=>'',
            'type'=>'select',
            'link'=>[
                'model'=>[
                    'belongsToMany',
                    'App\\Models\\Common\\CommonUser',
                ],
                'select'=>['id','name','phone'],
                'field'=>['id','name','phone'],
            ],
//                'append'=>true,//虚拟字段,附加上的
            'multi'=>true,
        ],
        'upload'=>[
            'name'=>'upload',
            'tip'=>'',
            'type'=>'image',
//                'multi'=>true,
        ],
        'file'=>[
            'name'=>'文件',
            'tip'=>'',
            'type'=>'image',
            'multi'=>true,
        ],
        'price'=>[
            'name'=>'price',
            'tip'=>'',
            'type'=>'number',
        ],
        'float'=>[
            'name'=>'float',
            'tip'=>'',
            'type'=>'text',
        ],
        'ip'=>[
            'name'=>'ip',
            'tip'=>'',
            'type'=>'text',
        ],
        'timestamp'=>[
            'name'=>'timestamp',
            'tip'=>'',
            'type'=>'timestamp',
        ],
        'datetime'=>[
            'name'=>'datetime',
            'tip'=>'',
            'type'=>'datetime',
        ],
    ],
];