#1 在config下的Schemas中配置文件的规则
    
##1.1分组

    配置文件分别分组放在文件夹中,如Address,Admin,Common,Configs,Test等文件夹

##1.2联表查询实例
   ###1.2.1 一对多联表
   
    'province_id'=>[
                'name'=>'省',
                'tip'=>'',
                'type'=>'select',
                'link'=>[
                    'model'=>[
                        'belongsTo',
                        'App\\Models\\Address\\AddressProvince',
                        'province_id',
                        'id',
                    ],
                    'select'=>['id','name'],
                    'field'=>['id','name'],
                ],
            ],
    注:link中为联表查询 ,'model'为关联表的方式,名称和字段,'select'查询的字段,'field'为
   ###1.2.2 多对多查询
    'link'=>[ 
         model'=>[
              belongsToMany',
              'App\\Models\\Address\\AddressCity',
         ],
         'select'=>['id','name'],
         'field'=>['id','name'],
    ],
    注:多对多查询只需要写好联表方式和联表的model,就会自动生成关联表,其他就和一对多一样
#1.3显示字段
    在Components文件夹中,Test中Test.php为例
    1.其中包括table,add,edit,detail.
    2.如果不想在后台中显示其中一个字段,就不
    
            

    