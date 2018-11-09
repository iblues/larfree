七牛中配置
config/filesystems.php
.env

默认是七牛
UPLOAD_TYPE=qiniu

APP_URL=http://localhost

        //https://github.com/zgldh/qiniu-laravel-storage
        'qiniu' => [
            'driver'  => 'qiniu',
            'domains' => [
                'default'   => env('QINNIU_DOMAINS','phxl6xm0o.bkt.clouddn.com'),
                'https'     => '',//ssl域名
                'custom'    => '',//没啥用
            ],
            'access_key'=> env('QIUNIU_ACCESS_KEY','dDZZViiNtYgbS6OXtAOTTzaem51GXHMkaUlrMEGP'),
            'secret_key'=> env('QIUNIU_SECRET_KEY','SLA7BS8MurxNfy_USYNS4ejZTLCKKPNdqr4j-pWW'),
            'bucket'    => env('QIUNIU_BUCKET','test'),  //Bucket名字
            'notify_url'=> '',  //持久化处理回调地址
            'access'    => 'public'  //空间访问控制 public 或 private
        ],