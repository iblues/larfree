#1在schemas文件夹中定义规则

##1.1实例

    'title'=>[
        'name'=>'标题',
        'tip'=>'<span style="color:red">测试HTML</span>',
        'type'=>'text',
        'rule'=>['required_with:select','min:2'=>'最小2位','max:10'=>'最大10位'],
    ],

有多种格式可以选择
1.简单定义,仅定义一个必填

    'rule'=>'required' 
    
2.自定义返回消息

    'rule'=>['required'=>'自定义提示']

3.多个条件,自定义消息和默认消息混合

    'rule'=>['required_with:select','min:2'=>'最小2位','max:10'=>'最大10位'],
    
4.多个规则 规则参考

##1.2常用规则参考  
sometimes 当有该字段才启用验证 
  
required 必填. 慎用

required_with:字段名  当其他字段存在时,才必填

in:1,2,3   必须是1,2,3中的一个才行

integer   必须是数字

array     必须是数组

between:min,max 长度必须在2个之间

email     必须是有效有效

phone     必须是有效手机号  规则暂未添加

max:5     最长多少

min:2     最小多少

URL       必须是有效URL

date      必须是有效日期

confirmed 验证的字段必须和 foo_confirmation 的字段值一致

####更多请参考
https://d.laravel-china.org/docs/5.5/validation#available-validation-rules

#2. 各个不同API的应用

##2.1实例

    'admin.in'=>[
        'index'=>[
            'upload'=>false,//只排除upload
        ],
        'show'=>[
            '*' //允许所有字段
        ],
        'store'=>[
            'upload'=>['rule'=>['min:5','max:4'=>'测试']],
            'select'=>false,//排除select
        ],
        //只允许title,select字段
        'update'=>[
            'title'=>['rule'=>'min:4'],
            'select'
        ],
    ],
    
##2.2规则讲解
2.1.规则

2.1.1 当为*的时候,允许接收所有的字段,

2.1.2 当`'select'=>false` 为false的时候  意思是 
排除该字段,会自动过滤. 
#### 注意!只要有false,等于自动添加了`*`

2.1.3 当`'upload'=>['rule'=>['min:5','max:4'=>'测试']]` 重置对应的规则,验证规则


##2.3 in和out和admin
admin就是后台专用
in是指传入参数,不存在的会自动隐藏
out是指输出字段 (如果不允许输出的 会自动隐藏)



##2.4 在控制器中单独制定规则

    public $in=[
        'store'=>[
            '*',
            'datetime'=>[
                'name'=>'日期',
                'rule'=>['required','date'],
            ],
            'price2'=>[
                'name'=>'日期4',
                'rule'=>['required','min:2'=>'最小2位'],
            ],
            'select'=>[
                'rule'=>['required'],
            ]
        ]
    ];

请注意 是否加上* 如果没有* 代表不使用配置结构的
