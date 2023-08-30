## JVM

### Introduce

1. JVM 体系结构图
   ![avatar](/static/image/java/GC.bmp)

   - 栈是运行时的单位, 堆是存储时的单位

     ```java
     Class Files  ==>  类装载器子系统 Class loader
                       ||       ||
     ---------------------------------------------------------------
     |            运行时数据区[Runtime Data Area]                   |
     | 方法区[share]          Java 栈 NoGC             本地方法栈    |
     |  Method Area          Java Stack     Native Method Stack   |
     | 永久区是对其的实现                                            |
     |                                                            |
     |   堆[share]                      程序计数器                  |
     |     Heap              Program Counter Register             |
     |------------------------------------------------------------|
           || ||                          ||  ||
           执行引擎           ==>            本地方法接口     <== 本地方法库
     Execution Engine      <==         Native Interface
     ```

2. [ClassLoader](./ClassLoader.md)

![avatar](/static/image/java/class-loader.png)

![avatar](/static/image/java/javase-jvm-thread.png)

### Program Counter Register

1. 一个指向下一个执行方法的指针
2. 内存使用少
3. native 方法的 PC Register 是 null

### stack

![avatar](/static/image/java/javase-jvm-stack.png)

1. 设计初衷

   - 由于跨平台性的设计, Java 指令都是基于栈[8 位对齐]来设计的: 不同平台的 CPU 架构不同, 所以不能设计为基于寄存器的[16 位为对齐]
   - 基于栈的优势: 跨平台, 指令集小, 编译器容易实现
   - 劣势: 性能下降, 实现的同样的功能指令多
   - 栈顶缓存:
     1. 原因: 完成同一操作要更多的字节码指令, 意味更多的 IO, 由于操作数是存储在内存中的, 因此频繁地执行内存读/写操作必然会影响执行速度;
     2. Hotspot JVM 将栈顶元素全部缓存在物理 CPU 的寄存器中, 以此降低对内存的读/写次数, 提升执行引擎的执行效率

2. 简介:

   - 管理 Java 程序的运行, 保存方法的局部变量, 部分结果, 参与方法的调用和返回
   - 生命周期与线程的生命周期相同
   - 访问速度仅次于 PC 计数器[只涉及到入栈和出栈的操作]
   - 不存在垃圾回收问题: StackOverflow[深度/-Xss]
   - 线程私有

3. 存储: 方法栈帧[一个栈帧对应一个 Java 方法的调用]

   - [istore/iload]局部变量表: 是一个数字数组, 主要用于存储方法**参数**和定义在方法体内的**局部变量**

     1. 数据类型包括: 基本数据类型, 引用类型, 返回值类型[returnAddress 指向字节码指令的地址]
     2. 线程私有数据, 不存在线程安全问题
     3. 局部变量表的容量大小在编译期间被确定, 在方法运行期间不会改变[数组]
     4. 槽 Slot:
        - 这些数据类型在局部变量表中的存储空间用局部**变量槽 Slot**来表示
        - long 和 double 是 64 位占两个槽, 其余占一个
        - 所以局部变量表的容量就是槽的个数，在编译期间就是确定的

   - [iconst]操作数栈: 保存计算的中间结果, 计算过程中变量临时的存储空间
   - 帧数据区

     1. 动态链接[指向运行时常量池的方法引用]: 为了将符号引用[常量池中的一个字符串]转换为直接引用[指向方法的真正的入口]的
        - 动态链接指向的是方法区的运行时常量池 & 解析指向的是 class 文件中的常量池
        - 动态链接是在字节码解释执行时 & 解析是在类加载时
     2. 方法返回值: Java 方法有两种返回函数的方式: 不管使用哪种方式, 都会导致栈帧被弹出
        - 一种是正常的函数返回，使用 return 指令；
        - 另外一种是抛出异常
     3. 附加信息

4. 栈管运行, 堆管存储
5. 不发生 GC, 线程私有

6. relation in stack heap and method area

![avatar](/static/image/java/stack-heap-MA.png)

7. PermGen 负责加载 `rt.jar: JDK Class, Interface etc.`; 只有 jvm 销毁时才会释放这些资源

### native stack

1. 本地方法栈是线程私有的

### 堆体系结构概述

1. diagram

   ![avatar](/static/image/java/heap.png)

2. struct

   - [10]新生区: new/young[区域小, 存活率低]
     > [8]伊甸园区
     > [1]幸存者 0 区 [from 区][1]幸存者 1 区 [to 区]
   - [20]养老区: old/tenure[区域大, 存活率大]
   - [logic]永久区: implement of MethodArea

3. new Object() new 出来的对象放在 `伊甸园区`; 如果不停地 new, 新生区满了, 会触发 YGC

4. processor

   - new 出来的对象放在 `伊甸园区`
   - `伊甸园区` 满了出发 YGC
   - `伊甸园区` 中多次存活的数据到 `from` 区 $\color{red}{from 和 to 区进行交换}$
   - 15 次 YGC 后还存活的数据放到 `养老区`
   - `养老区` 满了出发 Full GC
   - 多次 FGC 后还是满的, 抛出 OOM Error

5. explain 4.3 交换

   - from 区 和 to 区并不是固定的, 每次 YGC 后会交换, 谁空谁是 to 区
   - YGC 时伊甸园区并须全清空, 幸存数据复制存入 From 区, 下次 YGC 将 from 区和伊甸园区都算作伊甸园区进行收割, 剩下的 to 区则成为存放存货数据的 from 区;

### 堆参数调优入门

- diagram
  ![avatar](/static/image/java/jvm.png)

1. -Xms: heap start, default 1/64 of physical memory
2. -Xmx: heap max, default 1/4 of physical memory
3. -Xmn: new
4. -XX:+PrintGCDetail:
5. -XX:MaxTenuringThreshold: 设置存过次数后移到 `老年区`
6. JDK1.7:

   - -XX:PermSize
   - -XX:MaxPermSize

7. JDK1.8:

   - pro env -Xms avlue is equals to -Xmx to avoid GC compare Memory for Application, which will lead to some odd question

8. code

   ```java
   long xms = Runtime.getRuntime().totalMemory();
   long xmx = Runtime.getRuntime().maxMemory();
   ```

### GCDetails

- diagram
  ![avatar](/static/image/java/FullGC.png)
  ![avatar](/static/image/java/GC.png)
- config

```xml
-Xms10m -Xmx10m -XX:+PrintGCDetails
```

- detail

```java
[GC (Allocation Failure)[PSYoungGen: 2048K{YGC前内存占用}->488K{YGC后内存占用}(2560K{新生区总内存})] 2048K{YGC前堆内存占用}->754K{YGC后堆内存占用}(9728K{JVM堆总大小}), 0.0010957 secs{YGC耗时}] [Times: user=0.00{YGC用户耗时} sys=0.00{YGC系统耗时}, real=0.00 secs{YGC实际耗时}]
[Full GC 区名: GC前大小 -> GC后大小(该区总空间), 耗时]

[GC (Allocation Failure) [PSYoungGen: 2536K->488K(2560K)] 2802K->1039K(9728K), 0.0015812 secs] [Times: user=0.00 sys=0.00, real=0.00 secs]
[Full GC (Ergonomics) [PSYoungGen: 1211K->0K(1536K)] [ParOldGen: 6525K->3418K(7168K)] 7736K->3418K(8704K), [Metaspace: 4761K->4761K(1056768K)], 0.0058887 secs] [Times: user=0.00 sys=0.00, real=0.01 secs]
[GC (Allocation Failure) [PSYoungGen: 40K->32K(2048K)] 6273K->6264K(9216K), 0.0003920 secs] [Times: user=0.00 sys=0.00, real=0.00 secs]
[Full GC (Ergonomics) [PSYoungGen: 32K->0K(2048K)] [ParOldGen: 6232K->4603K(7168K)] 6264K->4603K(9216K), [Metaspace: 4762K->4762K(1056768K)], 0.0139514 secs] [Times: user=0.05 sys=0.00, real=0.01 secs]
[GC (Allocation Failure) [PSYoungGen: 20K->32K(2048K)] 6030K->6042K(9216K), 0.0008025 secs] [Times: user=0.00 sys=0.00, real=0.00 secs]
[Full GC (Allocation Failure) Exception in thread "main" java.lang.OutOfMemoryError: Java heap space
[PSYoungGen: 0K->0K(2048K)] [ParOldGen: 5284K->5219K(7168K)] 5284K->5219K(9216K), [Metaspace: 4763K->4763K(1056768K)], 0.0103089 secs] [Times: user=0.02 sys=0.03, real=0.01 secs]
    at java.util.Arrays.copyOf(Arrays.java:3332)
Heap
 PSYoungGen      total 2048K, used 61K [0x00000000ffd00000, 0x0000000100000000, 0x0000000100000000)
    at java.lang.AbstractStringBuilder.ensureCapacityInternal(AbstractStringBuilder.java:124)
  eden space 1024K, 5% used [0x00000000ffd00000,0x00000000ffd0f548,0x00000000ffe00000)
    at java.lang.AbstractStringBuilder.append(AbstractStringBuilder.java:674)
  from space 1024K, 0% used [0x00000000fff00000,0x00000000fff00000,0x0000000100000000)
    at java.lang.StringBuilder.append(StringBuilder.java:208)
  to   space 1024K, 0% used [0x00000000ffe00000,0x00000000ffe00000,0x00000000fff00000)
    at OOM.main(OOM.java:18)
 ParOldGen       total 7168K, used 5219K [0x00000000ff600000, 0x00000000ffd00000, 0x00000000ffd00000)
  object space 7168K, 72% used [0x00000000ff600000,0x00000000ffb18c68,0x00000000ffd00000)
 Metaspace       used 4797K, capacity 4992K, committed 5248K, reserved 1056768K
  class space    used 529K, capacity 560K, committed 640K, reserved 1048576K

Process finished with exit code 1
```

### GC 4 算法: 没有最好的算法, 只能根据每代采用最合适的算法`[分代收集]`

1. ~~引用计数法~~: `无法解决循环依赖问题`, 维护计数器本身有消耗

   - 原理: 被引用的次数为 0, 就可以使用 System.gc() 进行回收
   - code

     ```java
     public class RefCountGC
     {
       private byte[] bigSize = new byte[2 * 1024 * 1024];//这个成员属性唯一的作用就是占用一点内存
       Object instance = null;

       public static void main(String[] args)
       {
         RefCountGC objectA = new RefCountGC();
         RefCountGC objectB = new RefCountGC();
         objectA.instance = objectB;
         objectB.instance = objectA;
         objectA = null;
         objectB = null;

         System.gc();
       }
     }
     ```

2. [GCRoot]复制算法(Copying): `新生代`

   - 原理: 从根集合 GCRoot 开始, 通过 Tracing 从统计是否存活
   - 过程:

     1. eden + From 复制到 To 区, 年龄 +1
     2. 清空 Eden + From
     3. 互换 From 和 To 区

   - feature:

     1. 没有碎片[YGC 时 eden+from 全空]
     2. **耗内存**[需要将幸存区数据复制到 To 区]

3. [GCRoot]标记清除(Mark-Sweep): `养老区`

   - 原理:

     1. 标记要回收的对象
     2. 统一回收

   - feature:

     1. 耗时少, 内存占用少
     2. 产生内存碎片
     3. GC 时程序不动

4. [GCRoot]标记[清除]压缩(Mark-Compact): `养老区`

   - 原理: 标记 + 清除 + 将存活对象整理到一端
   - feature:

     1. **无碎片**
     2. 需要移动对象的成本
     3. **耗时最长**
     4. GC 时程序不动

5. 种算法比较

   - 内存效率: 复制算法 > 标记清除算法 > 标记整理算法[此处的效率只是简单的对比时间复杂度, 实际情况不一定如此]
   - 内存整齐度: 复制算法 = 标记整理算法 > 标记清除算法
   - 内存利用率: 标记整理算法 = 标记清除算法 > 复制算法

### JMM

1. [link](./jmm.md)

### 小总结

1. Native is Deprecated.

### Reference

1. [JVM 调优总结](https://www.cnblogs.com/andy-zhou/p/5327288.html)
2. [JVM 内存结构](https://blog.csdn.net/weixin_43232955/article/details/107411378)
3. [JVM 栈](https://blog.csdn.net/weixin_43232955/article/details/107371310)
4. [JVM GC](https://blog.csdn.net/weixin_43232955/article/details/107876155)
