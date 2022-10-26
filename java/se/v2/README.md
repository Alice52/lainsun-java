[toc]

# Java Se

1. overview

   ![avatar](/static/image/java/se/se-overview.png)

## 常见的对象

1. Integer
2. Object
3. String
4. Threadlocal

## collection & map

1. List: CopyOnWriteList
2. Queue: XxDeque
3. Set
4. Stack: Vector
5. Map: chm

## enum

1. grammar
2. theory
3. best usage

## interface & abstract

1. design pattern relative

## generic

1. 本质: 1
2. 实现原理: 2 - 2
3. 好处: 4
4. 泛型使用: 3[3*1]
5. 泛型参数:8
6. 泛型擦除:
   - 过程: 2
   - 影响: 5
7. conlusion: 2
8. 查看编译之后的代码: arthas

## juc

1. basic

   - 状态
   - 创建/打断
   - 操作: 顺序/交替

2. ThreadPoolExecutor

   - blockingqueue
   - reject strategy

3. ExecutorService

   - ForkJoinPool
   - CompletableFuture

4. volatile - CAS - AtomicInteger
5. AQS

6. lock

   - ReentrantLock
   - synchronized
   - ReadWriteLock
   - 升级
   - 类型

7. tools

   - CountDownLatch
   - CyclicBarrier
   - Semaphore

8. Threadlocal
9. collection thread safe

## jvm

1. basic

   - 内存分布

2. 四大引用
3. jmm

   - 内存泄露/溢出
   - OOM
   - Happened-before

4. classloader

   - spi

5. GC

   - GC 算法

6. 调优经验: 20 例
   - 工具的使用

## exception

## reflect

1. jdk proxy - aop

## java8

## others

1. design pattern in jdk
2. this
3. switch
4. instanceof/isAssignableFrom
5. 高 CPU 分析
