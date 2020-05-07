# nike-jdi-setup

标签（空格分隔）： 工作记录

---
# Setup NIKE JDI  From Docker

---

## 1. 开始的准备
### 1.1 安装 docker 环境
本地通过镜像安装 docker, docker-machine 和 docker-compose：
```
curl -sSL https://cdn.quncrm.com/docker.sh | sudo sh
```

### 1.2 本地添加 host(/etc/hosts)
```
127.0.0.1 wm.com
127.0.0.1 ajax.wm.com
192.168.161.58 docker.quncrm.com
```

### 1.3 删掉本地的nginx
```
sudo apt-get remove nginx
sudo apt-get autoremove
```

### 1.4 Clone项目
```
git clone git@git.augmentum.com.cn:scrm/omnisocials.git
```

### 1.5 添加快捷方式
添加 `[ -e <DIR>/omnisocials/alias.sh ] && source <DIR>/omnisocials/alias.sh` 到`~/.profile`

然后source这个文件
source ~/.profile

之后你就可以使用 docker 镜像中暴露出来的 docker-grunt, docker-php, docker-node, docker-npm 命令来做代码的构建。

## 2. 重新build (出了错从这一步开始重来)
```
# 2.1 关闭全部容器
docker rm -f $(docker ps -aq)

# 2.2 删除前后端代码数据库和log
sudo rm -rf omnisocials-backend omnisocials-frontend data log

# 2.3 获取最新代码
git pull

# 2.4 初始化项目
./build.sh init

【在init的时候可能会出现这样的问题
/srv/omnisocials-backend/updateModules.js:14
async function updateModules() {
      ^^^^^^^^
SyntaxError: Unexpected token function
    at Object.exports.runInThisContext (vm.js:76:16)
    at Module._compile (module.js:542:28)
    at Object.Module._extensions..js (module.js:579:10)
    at Module.load (module.js:487:32)
    at tryModuleLoad (module.js:446:12)
    at Function.Module._load (module.js:438:3)
    at Module.runMain (module.js:604:10)
    at run (bootstrap_node.js:394:7)
    at startup (bootstrap_node.js:149:9)
    at bootstrap_node.js:509:3
这个时候需要把backed和frontend的updateModules.js文件重新替换一下。替换为http://git.augmentum.com.cn/scrm/omnisocials-frontend/blob/afb302ce1b476be3b99b68ea75b318ff99ff9657/updateModules.js的文件。】

替换完成之后重新 执行
./build.sh init

成功之后执行

docker pull docker.quncrm.com/base/phpcli:7.1
docker pull docker.quncrm.com/base/phpfpm:7.1

# 2.5 启动项目(一定要加上with_worker)
./build.sh up with_worker

# 2.6 创建默认的用户
DEFAULT_USER=test@wm.com ./build.sh create_default_user

现在拉取新的代码执行上边这一步会失败:
按以下步骤执行:
1. 进入容器:
docker exec -it omnisocials-worker bash
supervisorctl reload
2. 在omnisocials-worker镜像中将mongoimport命令放在bin目录下面
在omnisocials-backend目录下面执行
cp docker/worker/bin/mongoimport /bin/
3. 在docker启动的mongodb中新增核心库portal-master和租户库portal-tenants-shared的用户
先进入mongo数据库容器
docker exec -it omnisocials-mongodb mongo admin -u admin -p Abc123__
然后添加用户
coreDb = db.getMongo().getDB('portal-master');
coreDb.createUser({user:'root',pwd:'root',roles:[{role:'dbOwner',db:'portal-master'}]});

tenantsDb = db.getMongo().getDB('portal-tenants-shared');
tenantsDb.createUser({user:'root',pwd:'root',roles:[{role:'dbOwner',db:'portal-tenants-shared'}]});
4. 执行完以上步骤重新执行以下创建默认用户命令就可以成功了
DEFAULT_USER=test@wm.com ./build.sh create_default_user
```

## 3. Init Nike JDI 模块
### 3.1 拉代码后端
```
cd omnisocials-backend/src/
rm modules/nikejdi/ -rf
git clone git@git.augmentum.com.cn:nike/nikejdi.git modules/nikejdi
docker-grunt linkmodule:nikejdi
echo FLUSHALL|redis-cli -h localhost -p 6378
```

### 3.2 拉前端代码
```
cd omnisocials-frontend/src/
rm modules/nikejdi/ -rf
git clone git@git.augmentum.com.cn:nike/nikejdi.git modules/nikejdi
// 如果想换分支的话
// cd modules/nikejdi/
// git checkout -b develop origin/develop
// git branch
// cd ../../
docker-grunt linkmodule:nikejdi
echo FLUSHALL|redis-cli -h localhost -p 6378
docker-grunt build
```

### 3.3 在DB中enable nikejdi
```
a = db.account.findOne()
a.availableExtMods.push('nikejdi');
db.account.save(a);
```
也可以用图形化工具,在wm DB中找到account, 在availableExtMods中添加nikejdi

## 4. 微信测试号配置
### 4.1 修改config.php
修改`omnisocials-backend/src/common/config/config.php` 中的`WECONNECT_DOMAIN`为  `http://staging.wxapi.quncrm.com`

### 4.2 配置微信测试号
1. 扫码登录
https://mp.weixin.qq.com/debug/cgi-bin/sandbox?t=sandbox/login

2. 在wm.com的开发者中心新增微信测试号
填写:
    1. 测试号名称
    2. AppID
    3. AppSecret
    4. 原始Id
    5. 接口配置URL
    6. 接口配置Token
    7. JS接口安全域名 : wm.com
    8. 网页服务－网页账号－OAuth2.0网页授权－授权回调页面域名: ajax.wm.com

## 5. 后台模块重新加载(很可能有模块没加载出来)
```
# 进入docker
./build.sh ssh
cd omnisocials-backend/src/

# 重新加载模块
./yii management/accounts/add-menus-and-mods
./yii management/module/migration-for-module-change
./yii module/scan
```

## 6. 手机访问wm.com和自己写的页面
>http://git.augmentum.com.cn/scrm/omnisocials#%E5%89%8D%E5%90%8E%E5%8F%B0%E5%88%86%E7%A6%BB%E5%90%8E%E6%88%91%E8%AF%A5%E6%80%8E%E4%B9%88%E8%AE%BE%E7%BD%AE-nginx-%E4%BD%BF%E7%94%A8-ip-%E5%9C%A8%E6%89%8B%E6%9C%BA%E7%AB%AF%E5%8E%BB%E8%AE%BF%E9%97%AE

## 一些网页
*channelId需要替换成自己的*
nike 登录
http://wm.com/webapp/nikejdi/user/login?channelId=58ec5ff324aa9a0017df5439&openId=123456&debug=1

nike活动
http://wm.com/webapp/nikejdi/user/benefits?channelId=58ec5ff324aa9a0017df5439&openId=2312321&debug=1&page=profile



