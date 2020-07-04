# Omnisocial前后端代码只需要维护一份的解决办法

标签（空格分隔）： 工作记录

---

### 问题
我们现在的代码结构是有一个前端的文件夹，有一个后端的文件夹，如下图：
![文件结构](assets/markdown-img-paste-20170515102131477.png)

在`omnisocial-backend`和`omnisocial-fronted`下各有一份`nikejdi`的文件。
下面是nikejdi的文件结构图：

![nikejdi文件结构图](assets/markdown-img-paste-20170515102433134.png)

这样导致我们在开发的时候需要在前后端的代码下都新建分支，并且改代码的时候需要时刻记得：
1. 如果修改前端的代码需要修改`omnisocial-fronted`下的nikejdi下面static和webapp文件夹里面的代码
2. 如果需要修改后端API的代码则需要修改`omnisocial-backend`下`backend`文件夹下的代码

久而久之，总难免有改错的时候，而且两边的代码还一不小心出现因为git不同步产生的冲突。

### 解决办法
我们可以通过使用软链接的方式来使我们的代码始终保持只有一份。
但是由于omnisocial中前后端代码在不同的docker里面，所以我们要做如下改动。

#### 步骤

##### 1. 在backend下面建一个指向前端nikejdi模块的软链接
```
cd   你的目录/omnisocials/omnisocials-backend/src/modules/


# 删除原有backend里的nikejdi
rm -rf nikejdi/

# 建立指向前端nikejdi模块的软链接
ln -s /srv/omnisocials-frontend/src/modules/nikejdi nikejdi
(tip:或者同时需要omnisocials-backend/src/backend/modules执行软链)
```

##### 2. 修改docker的配置文件
在backend所在的容器里添加frontend的卷标。
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

**YML文件修改的时候要注意缩进**

##### 3. 重启Docker
在`omnisocials`的根目录下面执行：
```
./build.sh up with_worker
```




