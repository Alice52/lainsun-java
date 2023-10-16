[toc]

## intros/overview

![avatar](/static/image/dist/gateway/kong-flow.png)

## deploy

## config: `Route >> Service >> Upstream >> Target`

1. route: 与服务关联, 多对一关系(location)
2. service: 上游服务的抽象, 通过 Kong 匹配到相应的请求要转发的地方(server)
3. upstream: 上游服务, 实现负载
4. target: upstream 负载下的每个节点(物理服务 | ip + port 的抽象)
5. consumer: 代表用户或应用(核心原则是可以为其添加插件)
6. plugin

## plugins 开发

## practice

1. oneid plugin with aacs

---

## reference

1. https://zhuanlan.zhihu.com/p/577842078
2. https://github.com/micro-services-roadmap/roadmap/issues/5
3. https://blog.csdn.net/lgxzzz/article/details/121683302
4. https://cloud.tencent.com/developer/article/2301049
