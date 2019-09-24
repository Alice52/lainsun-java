### common comand

    ```shell
    sudo docker version
    # auto start
    sudo systemctl enable docker
    # log location
    /var/lib/docker/containers

    # docker 开机自启动容器
    docker update --restart=always 镜像ID
    # docker log 查看
    sudo docker logs -f -t --tail 行数 容器名
    ```

### docker comand

    ```shell
    service docker start/stop
    docker version
    docker info
    docker --help
    # 查看所有本地镜像
    docker images
    # 删除指定的本地镜像
    docker rmi image-id
    # 我们经常去docker hub上检索镜像的详细信息，如镜像的TAG.
    # eg：docker search redis
    docker search 关键字
    # :tag是可选的，tag表示标签，多为软件的版本，默认是latest
    docker pull 镜像名:tag
    # [‐d：后台运行  ‐p: 将主机的端口映射到容器的一个端口    主机端口:容器内部的端口]
    docker run ‐d ‐p 8888:8080 tomcat
    # [进入 tomcat]
    docker exec -it 85b87053ec8c /bin/bash
    # [复制 war 到 tomcat]
    docker cp example.war 85b87053ec8c:/usr/local/tomcat/webapps
    # [查看容器的日志]
    docker logs container‐name/container‐id
    # 查看防火墙状态
    service firewalld status 
    # 关闭防火墙
    service firewalld stop
    # [查看运行中的容器]
    docker ps
    # [查看所有的容器]
    docker ps ‐a
    # [启动容器]
    docker container start 容器id
    # [停止运行中的容器]
    docker container stop 容器的id
    # [删除一个容器]
    docker container rm 容器id
    ```

### install mysql

    ```shell
    sudo docker pull mysql:5.7

    docker run -p 3306:3306 --name mysql -v /root/mysql/conf:/etc/mysql/conf.d -v /root/mysql/logs:/logs -v /root/mysql/data:/var/lib/mysql -e MYSQL_ROOT_PASSWORD='Yu1252068782?' -d mysql:5.7
    sudo docker exec -it mysql /bin/bash
    GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'Yu1252068782?' WITH GRANT OPTION;
    FLUSH  PRIVILEGES;
    docker logs --tail=200 -f mysql
    ```

### install redis

    ```shell
    docker run -d --name redis -p 6379:6379 -v /root/redis/data:/data -v /root/redis/conf/redis.conf:/usr/local/etc/redis/redis.conf  -v /root/redis/log:/logs redis:5.0 redis-server /usr/local/etc/redis/redis.conf --appendonly yes
    ```

### install rabbitmq

    ```shell
    docker run -d -p 5672:5672 -p 15672:15672 --name rabbitmq 3f92e6354d11

    docker run -d --name rabbitmq -p 5672:5672 -p 15672:15672 -v /root/rabbitmq/data:/var/lib/rabbitmq -v /root/rabbitmq/logs:/var/log/rabbitmq  --hostname rabbit -e RABBITMQ_DEFAULT_VHOST=/ -e RABBITMQ_DEFAULT_USER=guest -e RABBITMQ_DEFAULT_PASS=guest 3f92e6354d11
    ```

### install mongodb

    #where to log
    logpath=/var/log/mongodb/mongodb.log

    ```shell
    docker run -d --name mongo -p 27017:27017 -v /root/mongo/data/db:/data/db cdc6740b66a7

    docker run -d --name mongodb -p 27017:27017 -v /root/mongodb/configdb:/data/configdb/ -v /root/mongodb/logs:/var/log/mongodb -v /root/mongodb/data/db/:/var/lib/mongodb cdc6740b66a7
    docker exec -it CONTAINER_ID /bin/bash

    use admin
    db.createUser({
        user: "admin",
        pwd: "Yu1252068782?",
        roles: [ { role: "root", db: "admin" } ]
    });

    # test auth

    mongo --port 27017 -u admin -p Yu1252068782? --authenticationDatabase admin
    ```

### docker-tomcat

    ```shell
    sudo docker pull tomcat:8.5.40
    mkdir tomcat # /root
    docker run -d -p 8001:8080 --name tomcat8 -v /root/tomcat/conf/:/usr/local/tomcat/conf -v /root/tomcat/logs:/usr/local/tomcat/logs -v /root/tomcat/webapps/:/usr/local/tomcat/webapps tomcat

    sudo docker exec -it tomcat8 /bin/bash
    # look up log
    docker logs --tail=200 -f tomcat8
    ```

### mssql-server

### nginx

    ```shell
    docker pull nginx
    # get default conf
    docker run --name nginx-test -p 80:80 -d nginx
    docker cp nginx-test:/etc/nginx/nginx.conf /root/nginx/conf/nginx.conf
    docker cp nginx-test:/etc/nginx/conf.d /root/nginx/conf/conf.d

    # delete container
    docker container stop CONTAINER_ID
    docker rm CONTAINER_ID

    # start new container
    docker run -d -p 80:80 --name nginx -v /root/nginx/www:/usr/share/nginx/html -v /root/nginx/conf/nginx.conf/nginx.conf:/etc/nginx/nginx.conf -v /root/nginx/conf/conf.d:/etc/nginx/conf.d -v /root/nginx/logs:/var/log/nginx nginx

    # set aoto start
    docker update --restart=always 镜像ID
    ```
