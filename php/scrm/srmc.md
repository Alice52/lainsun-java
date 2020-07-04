## srmc 初始化添加 module
 * 安装环境: 包括搜狗输入法、Google Chrome 浏览器、Visual Studio Code、docker
 // 关闭现在运行的容器
 * docker rm -f $(docker ps -aq)
 // 删除这个四个文件夹
 * sudo rm -rf omnisocials-backend omnisocials-frontend data log
 // 拉取最新代码，并开启容器服务
 * git pull
 * ./build.sh init
 * ./build.sh up
 // 创建用户
 * docker exec -it omnisocials-backend bash
 * cd src/
 * ./yii management/account/create dev@qq.com
 * exit
 // 建立[软链接](#软链)
 * cd omnisocials-backend/src/modules
 * rm -rf nikehbl
 * ln -s /srv/omnisocials-frontend/src/modules/nikehbl nikehbl
 // 切换到有UI的分支
 * cd /srv/omnisocials-frontend/src/modules/nikehbl
 * git fetch
 * git checkout feat-add-graphic-management
 * cd ~/workspace/omnisocials
  // 重启服务，注意不需要init
 * ./build.sh up

## 软链
在`omnisocials`的根目录下面有一个叫做`docker-compose.yml`的文件。
找到下面这段代码:
```yml
omnisocials-backend:
  build:
    context: ./omnisocials-backend
    dockerfile: docker/php-fpm/Dockerfile
  mem_limit: 256m
  hostname: omnisocials-backend
  container_name: omnisocials-backend
  volumes:
    - ./omnisocials-backend:/srv/omnisocials-backend
```

在卷标下面添加一行，变成：
```yml
volumes:
  - ./omnisocials-backend:/srv/omnisocials-backend
  - ./omnisocials-frontend:/srv/omnisocials-frontend
```