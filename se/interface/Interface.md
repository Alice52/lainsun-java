## V2

### 接口 & 抽象

1. **接口**: 抽象方法和常量的集合, java8 中引入 default
   - 接口是一系列方法的声明, 是一个抽象类型, 没有实现{`public abstract` 修饰}[扩展性{不同的实现}]
   - 仅方法签名 + 常数宣告{不能包含属性}
   - 类实现接口的时候, 必须实现接口中声明的所有方法
   - 可以多实现, **接口间可以多继承**
   - 接口可以声明变量: `Comparable x;`
2. 抽象**类**
   - 不能实例化
   - 含属性和方法
   - 子类继承抽象类, 必须实现抽象类中的所有抽象方法
   - 只能单继承
   - 作用: 代码复用 + 模板中必须重写的方法 + 多态的优雅实现方案
3. 区别
   - 使用接口来实现面向对象的**抽象**特性、**多态**特性, 基于接口而非实现的**设计原则**
   - 使用抽象类来实现面向对象的**继承特性**和**模板设计模式**等
   - 抽象: is-a; 接口: has-a
   - 抽象: 代码复用; 接口: 解耦{行为的抽象}
4. 特征标
   - 仅包括方法的 `名字`, `参数的数目` 和 `种类`
   - 不包括方法的**返回类型**, **参数的名字**以及所抛出来的**异常**
5. others
   - extends, implements: 先继承后实现
   - 类优先于接口. `如果一个子类继承的父类和接口有相同的方法实现. 那么子类继承父类的方法`
   - `子类型中的方法优先于父类型中的方法[就近原则]`

---

## V1

### 接口继承多个父接口

```java
+---------------+         +------------+
|  Interface A  |         |Interface B |
+-----------^---+         +---^--------+
            |                 |
            |                 |
            |                 |
            +-+------------+--+
              | Interface C|
              +------------+
```

```java
interface A {
    default String say(String name) {
        return "hello " + name;
    }
}
interface B {
    default String say(String name) {
        return "hi " + name;
    }
}
interface C extends A,B{
    // 这里编译就会报错: error: interface C inherits unrelated defaults for say(String) from types A and B
}

interface C extends A,B{
    default String say(String name) {
        return "greet " + name;
    }
}
```

### 接口多层继承

```java
+---------------+
|  Interface A |
+--------+------+
         |
         |
         |
+--------+------+
|  Interface b |
+-------+-------+
        |
        |
        |
+-------+--------+
|   Interface C  |
+----------------+
```

- 很容易知道 C 会继承 B 的默认方法, 包括直接定义的默认方法, 覆盖的默认方法, 以及隐式继承于 A1 接口的默认方法.

  ```java
  interface A {
      default void run() {
          System.out.println("A.run");
      }

      default void say(int a) {
          System.out.println("A");
      }
  }
  interface B extends A{
      default void say(int a) {
          System.out.println("B");
      }

      default void play() {
          System.out.println("B.play");
      }
  }
  interface C extends B{

  }
  ```

### 多层多继承

```java
 +---------------+
|  Interface A1 |
+--------+------+
         |
         |
         |
+--------+------+         +---------------+
|  Interface A2 |         |  Interface B  |
+-------+-------+         +---------+-----+
        |       +---------+---------^
        |       |
        |       |
+-------+-------++
|   Interface C  |
+----------------+

```

```java
interface A1 {
    default void say(int a) {
        System.out.println("A1");
    }
}

interface A2 extends A1 {

}

interface B {
    default void say(int a) {
        System.out.println("B");
    }
}
// 必须重新写具有相同特征标的方法
interface C extends A2,B{
    default void say(int a) {
        B.super.say(a);
    }
}
```

### 复杂的

```java
+--------------+
 | Interface A1 |
 +------+------++
        |      ^+-------+
        |               |
+-------+-------+       |
|  Interface A2 |       |
+------------+--+       |
             ^--++      |
                 |      |
              +--+------+-----+
              |  Interface C  |
              +---------------+
```

```java
interface A1 {
    default void say() {
        System.out.println("A1");
    }
}
interface A2 extends A1 {
    default void say() {
        System.out.println("A2");
    }
}
interface C extends A2,A1{

}
static class D implements C {

}
public static void main(String[] args) {
    D d = new D();
    d.say(); // A2
}
```

### 类和接口的复合

- `子类优先继承父类的方法, 如果父类没有相同签名的方法, 才继承接口的默认方法`

```java
+-------------+       +-----------+
| Interface A |       |  Class B  |
+-----------+-+       +-----+-----+
            ^-+    +--+-----^
              |    |
          +---+----+-+
          |  Class C |
          +----------+
```

```java
interface A {
    default void say() {
        System.out.println("A");
    }
}
static class B {
    public void say() {
        System.out.println("B");
    }
}
static class C extends B implements A{

}
public static void main(String[] args) {
    C c = new C();
    c.say(); //B
}
```
