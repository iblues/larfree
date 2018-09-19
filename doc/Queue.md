# larfree

#队列
php artisan queue:listen
#广播
laravel-echo-server start
#webpack编译
npm run hot


[以下为守护进程的配置和安装]

#supervisor 安装
http://blog.csdn.net/woshixiaosimao/article/details/54315258
easy_install supervisor

[开机自启]
https://github.com/Supervisor/initscripts/blob/master/centos-systemd-etcs

[]手动启动]
supervisord -c /etc/supervisor/supervisord.conf

[队列重启]
php artisan queue:restart

supervisord 守护进程
[program:dj-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /www/wwwroot/dangjian.100jv.com/artisan queue:work  --tries=3
autostart=true
autorestart=true
user=www
numprocs=8  
redirect_stderr=true

[注意问题]
1.如果提示pythod/sokect找不到. supervisord -c /etc/supervisord.conf
2.注意配置的files = /etc/supervisor/*.ini 我这边是这个

supervisorctl           当前进程
supervisorctl reread     
supervisorctl update
supervisorctl start dj-queue


[然后后台执行]
screen -S yourname -> 新建一个叫yourname的session
screen -ls -> 列出当前所有的session
screen -r yourname -> 回到yourname这个session
screen -d yourname -> 远程detach某个session
screen -d -r yourname -> 结束当前session并回到yourname这个session

切换到一个桌面 执行


[im]
appId: dj
key: 46cdd3e5280e17cb04b188087f0bf824

demo 
http://dj.100jv.com:6001/apps/dj/status?auth_key=46cdd3e5280e17cb04b188087f0bf824

Status Get total number of clients, uptime of the server, and memory usage.
GET /apps/:APP_ID/status

Channels List of all channels.
GET /apps/:APP_ID/channels

Channel Get information about a particular channel.
GET /apps/:APP_ID/channels/:CHANNEL_NAME

Channel Users List of users on a channel.
GET /apps/:APP_ID/channels/:CHANNEL_NAME/users


#vue那边使用History模式
需要把ng的404 定向到index.html