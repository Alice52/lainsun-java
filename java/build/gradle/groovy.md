## groovy core syntax

1. groovy[DSL]: domain specific language

   - core thinking: 求专不求全, 解决特定问题

2. 变量

   - groovy 中都是包装类型
   - 变量定义:
     1. 只是自己的使用的变量尽量定义成 `def`
     2. 变量也用于其他模块: 强类型

3. 字符串

   - String: **双引号[可以包含变量]**, 单引号, 三引号
   - GString
   - method:
     1. java.lang.String
     2. DefaultGroovyMethods
     3. StringGroovyMethods

   ```groovy
   // 1. groovy 中都是包装类型
   int x = 20;
   println x.class;            // java.lang.Integer

   def y = 15.6
   println y.class;            // java.math.BigDecimal

   y = "str"
   println y.class;            // java.lang.String

   z = 'also string'
   println z.class;            // java.lang.String

   a = '''\
   line one.
   This is statement.
   '''
   println a;                  // java.lang.String

   def name = "zack"
   println name                // java.lang.String
   def helloTo = "hello ${name}"
   println helloTo
   println helloTo.class       // org.codehaus.groovy.runtime.GStringImpl

   def sum = "the sum of 2 and 3 equals ${2 + 3}"
   println sum

   def function = echo(sum);
   println function
   println function.class      // java.lang.String

   String echo(String message) {
       return message;
   }
   ```

4. list/map/range

   ```groovy
   def sum = 0;
   for (i in 0..9) {
       sum = sum + i
   }
   println sum

   // list
   sum = 0
   for (i in [1, 2, 5, 5]) {
       sum = sum + i
   }
   println sum

   // map
   sum = 0
   for (i in ['a': 1, 'b': 2, 'c': 3]) {
       sum = sum + i.value
   }
   println sum
   ```

5. switch

   ```groovy
   def x = 500
   def result
   switch (x) {
       case 'foo':
           result = 'string'
           break
       case [4, 5, 6, 'in-list']:
           result = 'list'
           break
       case 12..30:
           result = 'range'
           break
       case Integer:
           result = 'integer'
           break
       default: result = 'default'
   }

   println result
   ```

6. 闭包: clouser

   - this/owner/delegate

     ```groovy
     /*==================== this/owner/delegate =======================================*/
     def scriptClouser = {
         println "scriptClouser this: " + this                           // 闭包定义处的类
         println "scriptClouser owner: " + owner                         // 闭包定义处的类或者对象[闭包内定义的闭包的owner是闭包]
         println "scriptClouser delegate: " + delegate                   // 任意对象, 默认值是 owner 一致
     }
     scriptClouser.call()

     // inner class
     class Person {
         def static innerClassClouser = {
             println "innerClassClouser this: " + this
             println "innerClassClouser owner: " + owner
             println "innerClassClouser delegate: " + delegate
         }

         def static sav() {
             def innerClassClouser = {
                 println "innerClassMethodClouser this: " + this
                 println "innerClassMethodClouser owner: " + owner
                 println "innerClassMethodClouser delegate: " + delegate
             }
             innerClassClouser.call()
         }
     }

     Person.innerClassClouser.call()
     Person.sav()

     class Person2 {
         def innerClassClouser = {
             println "innerClassClouser this: " + this
             println "innerClassClouser owner: " + owner
             println "innerClassClouser delegate: " + delegate
         }

         def sav() {
             def innerClassClouser = {
                 println "innerClassMethodClouser this: " + this
                 println "innerClassMethodClouser owner: " + owner
                 println "innerClassMethodClouser delegate: " + delegate
             }
             innerClassClouser.call()
         }
     }

     Person2 p = new Person2()
     p.innerClassClouser.call()
     p.sav()

     def nestClouser = {
         println "outerClouser this: " + this
         println "outerClouser owner: " + owner
         println "outerClouser delegate: " + delegate

         def innerClouser = {
             println "innerClouser this: " + this
             println "innerClouser owner: " + owner
             println "innerClouser delegate: " + delegate
         }

         innerClouser.delegate = p // delegate = p
         innerClouser.call()
     }

     nestClouser.call()

     /*==================== 闭包的委托策略 =======================================*/

     class Student {
         String name;
         def pretty = {
             println "Student name is ${name}"
         }

         String toString() {
             pretty.call()
         }
     }

     class Teacher {
         String name;
     }

     def student = new Student(name: "zack")
     def teacher = new Teacher(name: "tim")
     student.toString()                                              // Student name is zack
     student.pretty.delegate = teacher
     student.pretty.resolveStrategy = Closure.DELEGATE_FIRST
     student.toString()                                              // Student name is tim
     ```

   - code

   ```groovy
   def clouser = { println("Hello Clouser!") }
   result = clouser.call();
   println result; // null

   def clouser2 = { String name, Integer age -> println("Hello ${name}, ${age}") }
   clouser2.call("zack", 4)

   def clouser3 = { println("Hello ${it}") }
   clouser3("zack")

   def clouser4 = { return "Hello ${it}" }
   def result4 = clouser4("zack")
   println result4
   ```

   - 与基本类型的使用

   ```groovy
   /*==================== basic type =======================================*/
   println(fat(5))

   int fat(int number) {
       int result = 1
       1.upto(number, { num -> result *= num })
       return result;
   }

   println(fatDown(5))

   int fatDown(int number) {
       int result = 1
       number.downto(1) { num -> result *= num }
       return result;
   }

   println(cal(101))

   int cal(int number) {
       int result = 0
       number.times {
           num -> result += num
       }
       return result;
   }
   ```

   - 与字符串类型的使用

   ```groovy
    /*==================== string type =======================================*/
    String str = 'the sum of 2 and 3 equals 5'

    str.each {
        t -> print t * 2
    }
    println ""

    def res = str.find {
        t -> t.isNumber()
    };
    println res

    def list = str.findAll {
        t -> t.isNumber()
    };
    println list.toListString()

    def any = str.any {
        t -> t.isNumber()
    };
    println any

    def every = str.every {
        t -> t.isNumber()
    };
    println every

    def collection = str.collect {
        t -> t.toUpperCase()
    };
    println collection // list type
   ```

   - 与数据结构类型的使用

   - 与文件类型的使用

7. json
8. xml
9. network
10. file
