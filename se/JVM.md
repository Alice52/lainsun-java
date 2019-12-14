## JVM

### JVM 体系结构概述

1. JVM 体系结构图

```java
 Class Files  ==>  类装载器子系统 Class loader
                    ||       ||
  ---------------------------------------------------------------
  |            运行时数据区[Runtime Data Area]                   |
  | 方法区[share]          Java 栈 NoGC             本地方法栈    |
  |  Method Area          Java Stack     Native Method Stack   |
  |                                                            |
  |   堆[share]                      程序计数器                  |
  |     Heap              Program Counter Register             |
  |------------------------------------------------------------|
       || ||                          ||  ||
      执行引擎           ==>            本地方法接口     <== 本地方法库
  Execution Engine      <==         Native Interface
```

2. [ClassLoader](./ClassLoader.md)

- diagram
  ![avatar](/static/image/java/class-loader.png)

3. Native Method Stack/ Native Interface / Native Method lib

- native mehod is out of control java, it need call other lib or system resouce
  ```java
  // this hint thread based on system operation, has noting with java
  private native void start0();
  ```

4. Program Counter Register

- a point indicate next execte method, like `Duty watch`
- using little memory
- native method, PC Register is null

5. Method Area: PermGen space + Meta space

- store class `struct description`, means class template
  > Runtime constants pool
  > filed
  > constructor
  > method data
  > method content
- shared by all thread
- **`insatnce var store in heap`**

6. stack

- 栈管运行， 堆管存储
- no GC, and thread private, dependency on Thread lifeCycle
- store
  > 8 kinds basic data + Reference Object + instance method is allocated all in function stack memory
  > Local Variables: input or output args and variables in method
  > Operand Stack: record in stack and out stack action
  > Frame Data: includes Class file and mathods etc: `including 局部变量表， 操作数帧， 运行时常量池引用， 方法返回地址， 动态链接`

7. relation in stack heap and method area

- diagram
  ![avatar](/static/image/java/stack-heap-MA.png)

### 堆体系结构概述

1. diagram
   ![avatar](/static/image/java/heap.png)
2. struct

   > 新生区: new/young
   > 养老区: old/tenure
   > 永久区: java7 -- 永久区; java8 -- 元空间

3. new Object() new 出来的对象放在 `伊甸园区`; 如果不停地 new, 新生区满了, 会触发 YGC

4. processor
   > 1. new 出来的对象放在 `伊甸园区`
   > 2. `伊甸园区` 满了出发 YGC
   > 3. `伊甸园区` 中多次存活的数据到 `S0` --> `S1` 区 $\color{red}{交换}$
   > 4. 多次 YGC 后发现 新生区还是满的， `S1` 区数据放到 `养老区`
   > 5. `养老区` 满了出发 Full GC
   > 6. 多次 FGC 后还是满的， 抛出 OOM Exception

### 堆参数调优入门

### 小总结

1. Native is Deprecated.
