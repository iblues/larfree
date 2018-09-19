<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),




    /*  7牛的相关例子
        $disk = Storage::disk('qiniu');

        // create a file
        $disk->put('avatars/1', $fileContents);

        // check if a file exists
        $exists = $disk->has('file.jpg');

        // get timestamp
        $time = $disk->lastModified('file1.jpg');
        $time = $disk->getTimestamp('file1.jpg');

        // copy a file
        $disk->copy('old/file1.jpg', 'new/file1.jpg');

        // move a file
        $disk->move('old/file1.jpg', 'new/file1.jpg');

        // get file contents
        $contents = $disk->read('folder/my_file.txt');

        // fetch file
        $file = $disk->fetch('folder/my_file.txt');

        // get file url
        $url = $disk->getUrl('folder/my_file.txt');

        // get file upload token
        $token = $disk->getUploadToken('folder/my_file.txt');
        $token = $disk->getUploadToken('folder/my_file.txt', 3600);

     */


    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],

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


    ],

];
