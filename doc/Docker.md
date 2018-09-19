docker run -i -t -d -p 20:20 -p 21:21 -p 80:80 -p 443:443 -p 888:888 -p 8888:8888  --privileged=true --name larfree bt-init /auto_service.sh


//带共享文件夹的 需要先给母鸡 加一个www
docker run -i -t -d -p 20:20 -p 21:21 -p 80:80 -p 443:443 -p 888:888 -p 8888:8888  --privileged=true -v /www:/wwwroot --name  larfree bt-init /auto_service.sh



1.登录
docker login --username=ap0521j1q@aliyun.com registry.cn-shenzhen.aliyuncs.com


2.pull
docker pull registry.cn-shenzhen.aliyuncs.com/54blues/larfree:[镜像版本号]

3.推送
docker login --username=ap0521j1q@aliyun.com registry.cn-shenzhen.aliyuncs.com

docker tag [ImageId] registry.cn-shenzhen.aliyuncs.com/54blues/larfree:[镜像版本号]

docker push registry.cn-shenzhen.aliyuncs.com/54blues/larfree:[镜像版本号]