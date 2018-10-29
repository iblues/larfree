#关于URL查询
@columns=title,id,users,mselect  //可以用来筛选字段

#关于搜索
http://larfree.test/api/test/test?@columns=title,id&title$=%25112%25&id$=<2|>2
$=AND
|=OR

##AND
name=123      
name$= %123%    
name$= >123|<123  筛选   >123 or <123

name$= >123,<123  筛选   >123 and <123

name$=[1,2,3]   筛选

##OR
name|= %123%  
name|= >123|<123  
name|= >123,<123 
name|=[1,2,3]

##否定
id!=123  
id!=[82001,38710]，即id满足 ! (id=82001 | id=38710)，可过滤黑名单的消息  
id!=%123%
 

##链表字段
name.title$='%'.123.'%'   name字段连表
name.count$>1|<3  关联的数据在 在1,3之间的

##多字段 
规则同上 
name|id$=123   
id|user.title$=%123%   
user.id|user.title$=123    


##其他
@column=id,sex,name    字段筛选
@sort = id.desc

##嵌套查询,需要利用json 未实现 以下未实现
{
    group|:{
        name$:'%员工%',
        type$:2,
    }
    group2|:{
        name$:'%老板%',
        type$:1,
    }
}


@having":"max(id)>=100"   


更新
praiseUserIdList+
praiseUserIdList-


"@column":"toId:parentId"  等于as