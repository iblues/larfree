七牛中配置
config/filesystems.php
.env

APP_URL=http://localhost

        //https://github.com/zgldh/qiniu-laravel-storage
        'qiniu' => [
            'driver'  => 'qiniu',
            'domains' => [
                'default'   => env('QINNIU_DOMAINS','ozpkvf5wf.bkt.clouddn.com'),
                'https'     => '',//ssl域名
                'custom'    => '',//没啥用
            ],
            'access_key'=> env('QIUNIU_ACCESS_KEY','KJYbadAj1DvJM8QC7jJpjpIS_ap_efM-P3qBYFql'),
            'secret_key'=> env('QIUNIU_SECRET_KEY','37HvPY7pg8CT8JXWETElGtjiJXysk1UAIsnwFYJO'),
            'bucket'    => env('QIUNIU_BUCKET','redgame'),  //Bucket名字
            'notify_url'=> '',  //持久化处理回调地址
            'access'    => 'public'  //空间访问控制 public 或 private
        ],