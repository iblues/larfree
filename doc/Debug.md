composer require itsgoingd/clockwork

查看办法 2种.
火狐或者chrome  https://pan.baidu.com/s/1Alt0z9cQSVta7UWnGI_nYA
安装clockwork 


 http://localhost/\__clockwork


日志

    logger('logger log');
    clock('clock log');

打点

    clock()->startEvent('topic-index', "请求话题相关数据");
    clock()->endEvent('topic-index');
    
    
    
几个配置

    // 是否开启 clockwork （true/false）
    CLOCKWORK_ENABLE
    // 是否允许 HOST/__clockwork 方式访问 （true/false）
    CLOCKWORK_WEB
    // 过期时间，自动清理过期时间之前的文件（单位：分钟）
    CLOCKWORK_STORAGE_EXPIRATION
    
git忽略

    /storage/clockwork/