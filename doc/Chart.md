#配置统计的相关
    [
        title=>'主标题',
        sub_title=>'副标题",
        unit=>'单位',
        url=>'',//api接口
        type=>['line','bar']
        x=>[
            'name'=>'时间',
            'dataType'=>'date',
            'field'=>'created_at',  //
        ],
        y=>[
            [
                'name'=>'平均客单价',
                'dataType'=>'number',
                'data'=>'..',
                'field'=>[money=>abs,avg]//mysql统计函数
                'api'=>[
                            '@chart'=>'abs(avg(money))/10:create_at'
                            'create_at$'=>'>123'
                        ]
           ],
           [
               'name'=>'平均客单价',
               'dataType'=>'number',
               'data'=>'..',
               'field'=>[money=>abs,avg]//mysql统计函数
               'api'=>[
                           '@chart'=>'abs(avg(money))/10:create_at'
                           'create_at$'=>'>123'
                       ]
           ],
            
        ],//可能多个y
        
    ]


//今日订单数量  sum     group created_at
//新增订单金额  sum     group created_at
//评价客单价    avg     group created_at

//不同分类产品的平均价  avg    group cat

//不同国家订单所占比例  count  group courny

//投诉的比例  count group status


//普通统计.   多列表格 多次统计


