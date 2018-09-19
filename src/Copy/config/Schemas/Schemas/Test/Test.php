<?php
return [
    'detail'=>[
        
            'id'=>[
                'name'=>'id',
                'tip'=>'主键',
                'type'=>'text',
            ],
            'title'=>[
                'name'=>'标题',
                'tip'=>'<span style="color:red">测试HTML</span>',
                'type'=>'text',
                'rule'=>['required_with:select','min:2'=>'最小2位','max:10'=>'最大10位'],
            ],
            'has_many'=>[
                'name'=>'商品',
                'tip'=>'',
                'type'=>'select',
                'link'=>[
                    'model'=>[
                        'hasMany',
                        'App\\Models\\Test\\TestTestDetail',
                    ],
//                    'as'=>'has_many2',
                    'field'=>['id','uid','content','test_test_id'],
                    'select'=>['id','uid','content'],
                ],
//                'multi'=>true,  //hasMany会自动判断
            ],
            'user_id'=>[
                'name'=>'用户id',
                'tip'=>'用户信息',
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
                    'select'=>['id','name','phone'],
//                    'with'=>['coupon'],
                    'field'=>['id','name','phone'],
                ],
                //'multi'=>true,  //link会根据链表类型自动判断
            ],
            'select'=>[
                'name'=>'belongsToMany连表',
                'tip'=>'',
                'type'=>'select',

                'td_width'=>'200',//单独设置表格的宽度
//                'rule'=>'required',
//                'option'=>[1=>'数据1',2=>'数据2',3=>'数据3',4=>'数据4'],
                'link'=>[
                    'model'=>[
                        'belongsToMany',
                        'App\\Models\\Common\\CommonUser',
//                        'common_user_test_test',
//                        'test_test_id',
//                        'common_user_id',
                    ],
//                    'where'=>[
//                        'status'=>1
//                    ],
                    'select'=>['id','name','phone'],//select组建用
                    'field'=>['id','name','phone'],//字段筛选
                ],
                'multi'=>true,
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
                    'select'=>['id','id','name'],
                    'field'=>['id','id','name'],
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
            'created_at'=>[
                'name'=>'created_at',
                'tip'=>'',
                'type'=>'timestamp',
            ],
            'updated_at'=>[
                'name'=>'updated_at',
                'tip'=>'',
                'type'=>'timestamp',
            ],
            'content'=>[
                'name'=>'详情',
                'tip'=>'',
                'type'=>'editor',
            ],
            'users'=>[
                'name'=>'管理的用户',
                'tip'=>'',
                'type'=>'select',
                'link'=>[
                    'model'=>[
                        'belongsToMany',
                        'App\\Models\\Test\\TestTestDetail',
//                        'user_user',
//                        'user_id',
//                        'user_sub_id',
//                        'id',
//                        'id'
                    ],
    //                    foreignPivotKey = null, $relatedPivotKey = null,
    //                    $parentKey = null, $relatedKey = null, $relation
                    'select'=>['id','content'],
                    'field'=>['id','content'],
                ],
                //                'append'=>true,//虚拟字段,附加上的
                'multi'=>true,
            ],

    ],
];