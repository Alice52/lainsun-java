#常用命令记录
标签: 工作记录

-----

## 操作docker 的实例
```
docker  //查看docker 的命令
docker ps // 查看当前docker的进程

#复制宿主机文件到容器内部
~/workspace/omnisocials/omnisocials-backend$ docker cp docker/worker/bin/mongoexport 077ea818edc5:/usr/bin/

docker cp docker/worker/bin/mongoexport 077ea818edc5:/usr/bin/
#复制容器内部的文件到宿主机
docker cp containerId:/opt/testnew/file.txt /opt/test/

docker exec -it ba0 /bin/bash   //进入到指定的实例中
```
## 小程序
```
微信公众平台
u: 1761066822@qq.com
p: myweixin_6565210
```
```
  appid: 'wx0824cd9111deab65',
  secret: 'b17fbc624f09fc26df22510948af51f3',
```
## 小程序初始化数据库
```
拉完代码后进入docker, backend/src xia下执行初始化数据库脚本语句  ./yii ntc/ntc-init/init  + 你的channelId
```
## 手机代理
```
#service 端口 8888
polipo start

#启动COW 端口 7777
./cow &
```
##清除redis缓存
```
echo FLUSHALL|redis-cli -h localhost -p 6378
```

##删除日志文件
```
~/pro/omnisocials/omnisocials-backend/src/backend/runtime$ sudo rm -rf logs/
```
##curl请求nike的地址

```
curl -l -H "Content-type: application/json" -X POST -d '{"grant_type":"refresh_token","refresh_token":"aGHcndF44AEV8Y7XJPjqfBm0dC2AQRHv","client_id":"JDoCYsp1ykqqgqQDPo2lIx4Szv6ABJOU"}' https://api.nike.com/oauth/2.0/token
```
##远程window电脑
```
rdesktop -f -a 32 -n AUGMENTUM\Sonya.Guo -u AUGMENTUM\Sonya.Guo -0  192.168.22.110

用户名: AUGMENTUM\Sonya.Guo   密码:abc123_
```

##吃药小程序
```
AppID(小程序ID) wxbbf4f2a77471aa45

AppSecret：568455948a03b9a0d1bee5ec2be47d34

\\192.168.1.20\Development\NikeJDI\Baier\吃药小助手

http://git.augmentum.com.cn/scrm/miniprogram-template-pilot/issues/2

```
## directive定义的位置
```
/home/eddielu/git/omnisocials/omnisocials-frontend/src/static/portal/modules/core/directives
```
## 调试h5代码
```
在~/pro/omnisocials执行 ./build.sh ssh -p 8081:8081
然后进入到 omnisocials-h5/src执行 MODULE=nikejdi npm run h5dev
```
##远程链接stating环境的mongo
```
ssh eddielu@192.168.22.188
ssh root@scrm-staging
mongo staging-mongo-1.quncrm.com/admin -uroot -psr1g87rs
```
##从Staging的数据库导出数据为json格式,并复制到本地
```
##需要在安装mongo的机器上执行
mongoexport -h staging-mongo-1.quncrm.com -uroot -psr1g87rs --authenticationDatabase=admin -d portal-tenants-shared -c member -q '{ "accountId" : ObjectId("5925227141460602dc35c6f1")}' -o 20181015OpenId.json
##需要在授权登录mongo机器的机器上执行
scp -r root@scrm-staging:20181015OpenId.json ~
```
##omnisocials代码init up没有报错但是访问wm.com空白解决
```
frontend软链backend：
	cd /srv/omnisocials-frontend/src/
	ln -s /srv/omnisocials-backend/src/backend backend
```
## ln 添加软链
```
# cd ~/pro/omnisocials/omnisocials-backend/src/backend/modules
ln -s /srv/omnisocials-backend/src/modules/bb/backend bb
```
```
# cd ~/pro/omnisocials/omnisocials-backend/src/modules/
ln -s /srv/omnisocials-frontend/src/modules/nikejdi nikejdi
```

```
# cd ~/pro/omnisocials/omnisocials-h5/src/static/h5/modules
ln -s /srv/omnisocials-h5/src/modules/poc/h5/ poc
```

```
# cd ~/pro/omnisocials/omnisocials-frontend/src
ln -s /srv/omnisocials-backend/src/backend/ backend
```
##长连接转换为短连接
```
curl -L https://u.quncrm.com/s/ --data-urlencode 'url=https://wc.nike.com.cn/56fa4377ba1b82a4378b4798/h5/subscription/index.html#!/entrancetemplate?channelId=56fa45c00cf272e7678e2d99&promoId=5a9fb6124ab9780450010053&eventSub=unsubscribed'
```
##php模糊搜索查询多个字段
```php
if (array_key_exists('searchName', $params) && '' != $params['searchName']) {
  $searchKeyGroup =[];
  $hasRedeemCode = ['code.qrcodeData' => $params['searchName']];
  $hasImgUrl = ['code.imgUrl' => $params['searchName']];
  $hasUpmId = ['upmId' => $params['searchName']];
  $hasNumber = ['number' => $params['searchName']];
  $searchKeyGroup[] = $hasUpmId;
  $searchKeyGroup[] = $hasRedeemCode;
  $searchKeyGroup[] = $hasImgUrl;
  $searchKeyGroup[] = $hasNumber;

  $search = [
      '$or' => $searchKeyGroup,
      'code' => [
          '$ne' => '未中奖'
      ]
  ];
  $condition = array_merge($condition, $search);
}
```
##Mongo的操作记录
###mongo更新操作
```
db.getCollection('examinee').update({'examId': "59c9afffbf402219d4adf6d9"},{$set:{'isNotice': '0'}},{multi:true})
```
###导入csv的时候指定字段的类型
```
--columnsHaveTypes
```
###类型NumberLog 转 String
```
db.nikeTopMember.find({}).forEach(function(x){
    x.upmId = x.upmId + "";
    x.upmId = x.upmId.toString();
    db.nikeTopMember.save(x);
});
```
###本地导入mongo csv表格
```
首先需要把本地的文件导入到mongo的docker容器内（330b31d2ee1b为容器的id）
docker cp /home/yt00647/Rose/gongzuo/4周年/member.csv 330b31d2ee1b:/
mongoimport -h localhost:27017 -uadmin -pAbc123__ --authenticationDatabase=admin -d portal-tenants-shared -c nikeTopMember --type csv --headerline --ignoreBlanks --file /topmember.csv
```
###产品环境导出数据
```
mongoexport -d portal-tenants-nike -c subscriptionPromoMember -q '{ "accountId" : ObjectId("56fa4377ba1b82a4378b4798"), "promoId" : ObjectId("5a7fe1e1e6288b0afd015753")}' -o react_promo_member.json
```
## chrome调试手机端页面的6s宽高
```
宽X高  375X603
```
##使用background-image时需要设定的样式
```
position: absolute;
width: .6rem;
height: .6rem;
background-size: contain;
background-repeat: no-repeat;
background-image: url(https://cdn.quncrm.com/poc/management/close.png);
```
##staging删除BB模块 member数据（根据openId和accoundId）
```
https://staging-ajax.quncrm.com/5ab4c7a22e724a08cd58d322/api/bb/member/delete-member-by-openid-and-accountid?openId=o-C6rwz17JZ-tu66HMAzgSDfLImI&accountId=5ab4c7a22e724a08cd58d322
```
##登录链接
###staging账号
```

#bb
https://staging.quncrm.com/5ab4c7a22e724a08cd58d322/h5/bb/index.html#!/login?channelId=5ab4c9354aae6600209c910b
#women
https://staging.quncrm.com/5a7bdfe0804ed1084f350d61/h5/women/index.html#!/login?channelId=5a7be154aac49b00222fb857
#nrc
https://staging.quncrm.com/59534eaef5096803a24d9f81/webapp/nrc/user/login?channelId=5a2ddabea9046300221f6c7d
```
###prod账号
```
#bb
https://www.quncrm.com/554c6f65ba1b8202108b457c/h5/bb/index.html#!/login?channelId=55559a910cf285fc6a96c7b4
#women
https://www.quncrm.com/570e34d2905e88d46f8b4582/h5/women/index.html#!/login?channelId=570f10300cf26d358f23475f
#nrc
https://www.quncrm.com/570e348e905e88d66f8b4578/webapp/nrc/user/login?channelId=570f0fa10cf2d730fe30cdc4
```
##项目指令路径
```
omnisocials/omnisocials-frontend/src/static/portal/modules/core/directives
```
##angular模态框关闭并传参 data可选
```
$modalInstance.close(data)
```
##mongo索引
```
db.nikeUrlConfig.ensureIndex({isDeleted:1, title:1}, {background: true})
db.nikeUrlConfig.ensureIndex({isDeleted:1}, {background: true})
db.nikeUserView.ensureIndex({urlId:1, openId:1}, {background: true})
```
##查询端口占用的pid
```
sudo fuser -n tcp 8081
```
##git 提交时使用上一次提交的记录.(提交之后分之树上没有这个提交的点)
```
git add .
git commit -a --amend
git push -f
```
##mongo export 导出时显示no such key
```
#原因是缺少了mongoexport
~/workspace/omnisocials/omnisocials-backend$ docker cp docker/worker/bin/mongoexport 077ea818edc5:/usr/bin/
```
##执行,mongo 报错
```
F CONTROL [main] Failed global initialization: BadValue: Invalid or no user locale set. Please ensure LANG and/or LC_* environment variables are set correctly.
解决办法：
vim /etc/profile
export LC_ALL=C
或者直接在终端里输入 export LC_ALL=C。
```
##omnisocials 项目Build之后的目录
```
/home/yt00658/workspace/omnisocials/omnisocials-frontend/src/frontend/web/build
```
##Staging容器操作命令
```
- exec inside container:
    - kubectl get pods, for usp, add option '-n usp'
    - kubectl exec -it ${podName} bash, for usp, add option '-n usp'

    kubectl exec -it portal-backend-65578d85b8-pjfqg  bash
    kubectl exec -it portal-backend-799966bbbd-c5lq9 bash
```