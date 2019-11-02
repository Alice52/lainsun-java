## spring

### introduce

#### spring feature

- definition: Spring 是一个 IOC(DI) 和 AOP 容器的开源的为简化企业级开发框架
- feature
  - 非侵入式: 不依赖于 Spring 的 API(轻量级)
  - 依赖注入: DI(`Dependency Injection`), 反转控制(IOC)最经典的实现
  - 面向切面编程: AOP(`Aspect Oriented Programming`)
  - 容器: Spring 是一个容器, 包含管理应用对象的生命周期
  - 组件化: Spring 实现了使用简单的组件配置组合成一个复杂的应用. 在 Spring 中可以使用 XML 和 Java 注解组合这些对象

#### overview

- overview: history, design philosophy, feedback, getting started.
- core: ioc container, events, resources, i18n, validation, data binding, type conversion, spel, aop.
- testing: mock objects, testcontext framework, spring mvc test, webtestclient.
- data access: transactions, dao support, jdbc, o/r mapping, xml marshalling.
- web servlet: spring mvc, websocket, sockjs, stomp messaging.
- web reactive: spring webflux, webclient, websocket.
- integration: remoting, jms, jca, jmx, email, tasks, scheduling, caching.
- languages:kotlin, groovy, dynamic languages.

#### spring modules schematic diagram

![avatar](/static/image/spring/spring-module.png)

### IOC

1. introduce

> special char, such as '<' in config xml file

- use <: &lt
- <![CDATA[]]>

  ```xml
  <property name="bookName">
      <value><![CDATA[<<受活>>]]></value>
  </property>
  ```

> when IOC is created, then all of beans will be created.
> class inhert diagram
> ![avatar](/static/image/spring/bean-factory.png)

2. property and member variables

- property: setxxx
- member variables: variable

3. get bean:

- common bean

```java
Person person =  ctx.getBean("person", Person.class);
Person person = (Person) ctx.getBean("person");
Person person = ctx.getBean( Person.class);
```

- FactoryBean: **`getObject`**

```java
<bean id="person" class="cn.edu.ntu.spring.ioc.PersonFactoryBean"/>
public class PersonFactoryBean implements FactoryBean<Person> {
  @Override
  public Person getObject() throws Exception {
      return new Person(10, new Date(), "zack", true, new Address(), "zzhang_xz@163.com", 200.00);
  }

  /** @return Specify Bean Type */
  @Override
  public Class<?> getObjectType() {
      return null;
  }

  /** @return whether is singleton */
  @Override
  public boolean isSingleton() {
      return true;
  }
}
```

4. DI

```xml
<bean id="personAbstarct" abstract="true">
    <property name="contry" value="China"/>
    <property name="gender" value="0"/>
</bean>
<!-- Bean Inhert: will inhert personAbstarct property when person donnot provide -->
<bean id="person" class="cn.edu.ntu.spring.eitity.Person" parent="personAbstarct">
    <property name="email" value="zzhang_xz@163.com"/>
    <property name="name" value="zack"/>
    <property name="gender" value="0"/>
    <property name="birthDay">
        <null/>
    </property>
    <property name="age" value="20"/>
    <!-- REF and CASCADE-->
    <property name="address" ref="address"/>
    <property name="address.street" value="suining"/>
    <!-- special char -->
    <property name="bookName">
        <value><![CDATA[<<受活>>]]></value>
    </property>
</bean>

<bean id="personCon" class="cn.edu.ntu.spring.eitity.Person">
    <constructor-arg value="" index="0" type="java.lang.Integer"/>
    <constructor-arg value="" index="1" type="java.lang.String"/>
    ...
</bean>

<bean id="personFP" class="cn.edu.ntu.spring.eitity.Person" p:age="2" p:name="zack"/>

<!-- COLLECTION and MAP -->
<!-- <list/> <set/> -->
<list>
  <ref bean= "book01"/>
  <ref bean= "book02"/>
</list>

<!-- <map/> -->
<property name="bookMap">
  <map>
      <entry key="STRING" value-ref="book"/>
      <entry key="STRING" value-ref="book2"/>
  </map>
</property>

<array/>
```

5. Bean Scope

- config in xml
  ```xml
  <bean id="" class="" scope="singleton"></bean>
  ```
- specification

  |    type     |              description              |          when create          |
  | :---------: | :-----------------------------------: | :---------------------------: |
  | `singleton` |        only one bean instance         |       when created IOC        |
  |  prototype  | create new instance when get per time |    when get bean insatnce     |
  |   request   |     new instance for per request      | used by WebApplicationCOntext |
  |   session   | new instance for per request session  | used by WebApplicationCOntext |

6. Bean Lifecycle

   - create bean by `BeanFactory`
   - set property for bean
   - pass bean to postProcessBeforeInitialization
   - bean init method
   - pass bean to postProcessAfterInitialization
   - `use bean`
   - bean destroy method

   ```java
   public class CustomBeanPostProcessor implements BeanPostProcessor {
       private static final Logger LOG = LoggerFactory.getLogger(BeanLifecycle.class);

       /**
       * @param bean
       * @param beanName
       * @return bean
       * @throws BeansException
       * @function do property validate
       */
       @Override
       public Object postProcessBeforeInitialization(Object bean, String beanName)
           throws BeansException {

           Optional.ofNullable(beanName)
               .filter(name -> name.equals("person4LifeCycle"))
               .ifPresent(
                   name ->
                       LOG.info("bean lifecycle: step3: pass bean to postProcessBeforeInitialization"));

           return bean;
       }

           /**
           * @param bean
           * @param beanName
           * @return bean
           * @throws BeansException
           * @function validate init method whether work
           */
       @Override
       public Object postProcessAfterInitialization(Object bean, String beanName) throws BeansException {

           Optional.ofNullable(beanName)
               .filter(name -> name.equals("person4LifeCycle"))
               .ifPresent(
                   name -> LOG.info("bean lifecycle: step5: pass bean to postProcessAfterInitialization"));
           return bean;
       }
   }
   ```

7. connect to database
   ```xml
   <!--mysql connector-->
   <dependency>
       <groupId>mysql</groupId>
       <artifactId>mysql-connector-java</artifactId>
       <version>8.0.17</version>
   </dependency>
   <dependency>
       <groupId>com.mchange</groupId>
       <artifactId>c3p0</artifactId>
       <version>0.9.5.2</version>
   </dependency>
   ```
   ```properties
   jdbc.user=root
   jdbc.password=Yu1252068782?
   jdbc.driverClass=com.mysql.cj.jdbc.Driver
   jdbc.jdbcUrl=jdbc:mysql://101.132.45.28:3306/jpa?useSSL=FALSE
   ```
   ```xml
   <context:property-placeholder location="classpath*:data-source.properties"/>
   <bean id="dataSource" class="com.mchange.v2.c3p0.ComboPooledDataSource">
       <property name="user" value="${jdbc.user}"></property>
       <property name="password" value="${jdbc.password}"></property>
       <property name="driverClass" value="${jdbc.driverClass}"></property>
       <property name="jdbcUrl" value="${jdbc.jdbcUrl}"></property>
       <!-- other property -->
   </bean>
   ```
   ```java
   @Test
   public void  testConnection() {
       ComboPooledDataSource comboPooledDataSource = ctx.getBean("dataSource", ComboPooledDataSource.class);
       LOG.info("get dataSource connection bean: {} from IOC container success.", comboPooledDataSource);
   }
   ```
8. AutoWire[less-use]

   - byName: according to beanName[<bean/>] and propertyName[setXXX]
   - btType: according to beanType and only one match will be autowire

   ```xml
    <bean id="address" class="cn.edu.ntu.spring.entity.Address" p:zipCode="220000" p:province="JiangSu" p:city="XuZhou"/>
    <bean id="person" class="cn.edu.ntu.spring.entity.Person" autowire="byName">
        <property name="gender" value="true"/>
        <property name="age" value="18"/>
        <property name="name" value="zack"/>
        <property name="bookName">
            <value><![CDATA[<<受活>>]]]></value>
        </property>
        <property name="email" value="zzhang_xz@163.com"/>
    </bean>
   ```

   - Annotation AutoWire

   ```xml
    <context:component-scan> will register AutowiredAnnotationBeanPostProcessor, which can wire properties labeled by @Autowired, @Resource or @Inject

    1. byType first, otherwise byName, otherwise throw exception
    2. use @Qualifier() point out autowire bean
   ```

9. Annotation: Equivalent to config in xml, default id-name is `Initial letter lowercase`

   - @Component(value="ID_NAME")
   - @Repository
   - @Service
   - @Controller

10. MVC

    - diagram
      ![avatar](/static/image/spring/MVC.png)
    - interface donot need add Annotation
    - xml

    ```xml
    <context:component-scan base-package="cn.edu.ntu.spring.mvc" use-default-filters="VALUE">
        <!-- use-default-filters="false" -->
        <!-- IOC manager all @Controller -->
        <context:exclude-filter type="annotation" expression="org.springframework.stereotype.Controller"/>
        <!-- IOC just manage PersonController -->
        <context:exclude-filter type="assignable" expression="cn.edu.ntu.spring.mvc.controller.PersonController"/>

        <!-- use-default-filters="true" -->
        <!-- IOC donnot manager all @Service -->
        <context:include-filter type="annotation" expression="org.springframework.stereotype.Service"/>
    </context:component-scan>
    ```

---

---

### AOP

#### AOP Before

- demand

  ```java
  private static final Logger LOG = LoggerFactory.getLogger(ArithmeticCalculator.class);

  @Override
  public int add(int a, int b) {
      LOG.info("The method add begins with [" + a + ", " + b + "]");
      int result = a + b;
      LOG.info("The method add ends with [" + result + "]");
      return result;
  }

  @Override
  public int sub(int a, int b) {
      LOG.info("The method sub begins with [" + a + ", " + b + "]");
      int result = a - b;
      LOG.info("The method sub ends with [" + result + "]");
      return result;
  }

  @Override
  public int mul(int a, int b) {
      LOG.info("The method mul begins with [" + a + ", " + b + "]");
      int result = a * b;
      LOG.info("The method mul ends with [" + result + "]");
      return result;
  }

  @Override
  public int div(int a, int b) {
      LOG.info("The method div begins with [" + a + ", " + b + "]");
      int result = a / b;
      LOG.info("The method div ends with [" + result + "]");
      return result;
  }
  ```

- proxy:

  ```java
  原理:
    使用一个代理将对象包装起来, 然后用该代理对象取代原始对象. 任何对原始对象的调用都要通过代理. 代理对象决定是否以及何时将方法调用转到原始对象上

  type:
    1. based on Interface: JDK
    2. based on Inhert: Cglib, Javassist

  code:
    private static final Logger LOG = LoggerFactory.getLogger(ArithmeticCalculatorProxy.class);

    private ArithmeticCalculator targetCalculator;

    public ArithmeticCalculatorProxy(ArithmeticCalculator calculator) {
        this.targetCalculator = calculator;
    }

    public Object getProxy() {

        // for proxy object load
        ClassLoader loader = targetCalculator.getClass().getClassLoader();
        // tell me what function the proxy object have, and make sure proxy and target expose same method. And it is the reason od CAST proxy to ArithmeticCalculator
        Class<?>[] interfaces = targetCalculator.getClass().getInterfaces();
        Object proxyObject =
            Proxy.newProxyInstance(
                loader,
                interfaces,
                (proxy, method, args) -> {
                // proxy is the proxy object, but fewer use
                LOG.info("The method {} begins with {}", method.getName(), Arrays.asList(args));
                Object result = method.invoke(targetCalculator, args);
                LOG.info("The method {} ends with [{}]", method.getName(), result);
                return result;
                });

        return proxyObject;
    }
  ```

- diagram
  ![avatar](/static/image/spring/aop-proxy.png)

#### AOP

1. definition
   - 横切关注点: 从每个方法中抽取出来的同一类非核心业务
   - 切面(Aspect): 封装横切关注点信息的类, 每个关注点体现为一个通知方法
   - 通知(Advice): 切面必须要完成的各个具体工作
   - 目标(Target): 被通知的对象
   - 代理(Proxy): 向目标对象应用通知之后创建的代理对象
   - 连接点(Joinpoint): 横切关注点在程序代码中的具体体现, 对应程序执行的某个特定位置
   - 切入点(pointcut): 定位连接点的方式. `如果把连接点看作数据库中的记录, 那么切入点就是查询条件`

![avatar](/static/image/spring/aop.png)

2. AspectJ more performance than spring AOP

   - AspectJ Type

   ```markdown
   1. @Before: 前置通知, 在方法执行之前执行. 不能获取结果
   2. @After: 后置通知, 在方法执行之后执行. `永远都会执行`, 不能获取结果
   3. @AfterRunning: 返回通知, 在方法返回结果之后执行, 可以获取结果
   4. @AfterThrowing: 异常通知, 在方法抛出异常之后执行. 可以指定异常
   5. @Around: 环绕通知, 围绕着方法执行
   ```

3. usage

   - xml

   ```xml
   <aop:config>
       <aop:aspect ref="loggingAspect" order="2">
           <aop:pointcut id="pointcut" expression="execution(* cn.edu.ntu.spring.aop.before.proxy.*.*(..))"/>

           <aop:before method="preAdvice" pointcut-ref="pointcut"/>
           <aop:after method="postAdvice" pointcut-ref="pointcut"/>
           <aop:after-returning method="reAdvice" pointcut-ref="pointcut" returning="result"/>
           <aop:after-throwing method="throwingAdvice" pointcut-ref="pointcut" throwing="ex"/>

           <aop:around method="aroundAdvice" pointcut-ref="pointcut"/>
       </aop:aspect>
   </aop:config>
   ```

   - Annotation

   ```xml
   <!-- enable annotation AspectJ: **`为切面中通知能作用到的目标类生成代理`** -->
   <!-- enable aspectj with annotation: generate proxy for Aspect's Advice-->
   <aop:aspectj-autoproxy/>

   <!-- DEPENDENCY -->
   <dependency>
       <groupId>org.springframework</groupId>
       <artifactId>spring-aop</artifactId>
       <version>5.2.0.RELEASE</version>
   </dependency>
   <dependency>
       <groupId>org.aspectj</groupId>
       <artifactId>aspectjrt</artifactId>
       <version>1.9.4</version>
   </dependency>
   ```

   ```java
   @Pointcut(value = "execution(* cn.edu.ntu.spring.aop.before.proxy.*.*(..))")
   public void pointCut() {}

   @Before("pointCut()")
   public void preAdvice(JoinPoint joinPoint) {
       String methodName = joinPoint.getSignature().getName();
       Logger LOG = AspectUtil.getTargetLogger(this.LOGGER, joinPoint);

       LOG.info(
           "Before Advice, exec method {} with args {}",
           methodName,
           getTargetArgs(joinPoint, methodName));
   }

   @AfterReturning(value = "pointCut()", returning = "result")
   public void reAdvice(JoinPoint joinPoint, Object result) {
       Logger LOG = AspectUtil.getTargetLogger(this.LOGGER, joinPoint);
       String methodName = joinPoint.getSignature().getName();

       LOG.info("Return Advice, exec method {} end and result is {}", methodName, result);
   }
   ```

### JdbcTemplate

1. COMMON API

   - JdbcTemplate.update(String, Object...)
   - JdbcTemplate.batchUpdate(String, List<Object[]>): no transaction
   - JdbcTemplate.queryForObject(String, RowMapper<Department>, Object...)
   - JdbcTemplate.query(String, RowMapper<Department>, Object...)
   - JdbcTemplate.queryForObject(String, Class, Object...)

2. NamedParameterJdbcTemplate

   ```xml
   <!-- INSERT INTO depts (dept_name) VALUES (:deptName) -->
   <bean id="namedTemplate" class="org.springframework.jdbc.core.namedparam.NamedParameterJdbcTemplate">
       <constructor-arg ref="dataSource"/>
   </bean>

   <!-- NamedParameterJdbcTemplate.update(String sql, Map<String, ?> map) -->
   ```

   ```java
    String sql = "insert into demo_user(name, age, birthDay, email, salary) values(:name,:age,:birthDay,:email, :salary)";

    Person person = new Person();
    person.setAge(20);
    person.setBirthDay(new Date());
    person.setEmail("zzhang_xz@163.com");
    person.setName("zack" + UUID.randomUUID().toString());
    person.setGender(true);
    person.setSalary(100.00);
    SqlParameterSource parameterSource = new BeanPropertySqlParameterSource(person);
    namedParameterJdbcTemplate.update(sql, parameterSource);
   ```

### Transaction

1. souce code: impliment based on AOP

   - spring will genrate proxy for class using @Transactional annotation
   - then spring will contribute the method labled @Transactional to work as transaction

2. config

   - annotation

   ```xml
   <bean id="jpaTransactionManager" class="org.springframework.orm.jpa.JpaTransactionManager">
       <property name="entityManagerFactory" ref="entityManagerFactory"></property>
   </bean>

   <!--enabel trasaction annotation: @Transactional -->
   <tx:annotation-driven transaction-manager="jpaTransactionManager"/>

   @Transactional(propagation = Propagation.REQUIRES_NEW, readOnly = true, timeout = 3, isolation = Isolation.READ_COMMITTED, rollbackFor = {ClassNotFoundException.class})
   ```

   - xml

   ```xml
   <aop:config>
       <aop:pointcut id="txPointCut" expression="execution(* com.atguigu.tx.component.service.BookShopServiceImpl.purchase(..))"/>
       <!-- Association -->
       <aop:advisor advice-ref="myTx" pointcut-ref="txPointCut"/>
   </aop:config>

   <!-- config xml transaction  -->
   <tx:advice id="myTx" transaction-manager="transactionManager">
       <tx:attributes>
           <!-- set transaction property -->
           <tx:method name="find*" read-only="true"/>
           <tx:method name="get*" read-only="true"/>
           <tx:method name="purchase"  isolation="READ_COMMITTED"
               no-rollback-for="java.lang.ArithmeticException, java.lang.NullPointerException"
               propagation="REQUIRES_NEW"
               read-only="false"
               timeout="10"/>
           <!-- other methods besides before -->
           <tx:method name="*"/>
       </tx:attributes>
   </tx:advice>
   ```

3. @Transactional

   - transaction propagation: the caller labeled @Transactionan call method labeled @Transactional

     ```java
     // default: use the caller transaction, big transaction
     propagation = Propagation.REQUIRED
     // use callee transaction, small transaction; callee will hang up caller transaction
     propagation = Propagation.REQUIRES_NEW
     ```

   ![avatar](/static/image/spring/transaction-propagation.png)

   - [transaction Isolation](/db/laguage/mysql/mysql-basical.md#7-transaction)

     ```java
     // 脏读: 一个事务读取到了另外一个事务未提交的数据.
     // 不可重复读: 同一个事务中, 多次读取到的数据不一致.
     // 幻读: 一个事务读取数据时, 另外一个事务进行更新, 导致第一个事务读取到了没有更新的数据.

     // READ UNCOMMITTED
     // READ COMMITTED  -- 可以避免脏读
     // REPEATABLE READ -- 可以避免脏读、不可重复读和一部分幻读
     // SERIALIZABLE -- 可以避免脏读、不可重复读和幻读

     isolation = Isolation.READ_UNCOMMITTED
     isolation = Isolation.READ_COMMITTED
     isolation = Isolation.REPEATABLE_READ[DEFAULT]
     isolation = Isolation.SERIALIZABLE
     ```

   - transaction rollback

     ```java
     // default: spring will rollback all transaction, which occurs RuntimeException.
     rollbackFor = {EXCEPTION_NAME.class}
     rollbackForClassName = {EXCEPTION_NAME}
     noRollbackFor = {EXCEPTION_NAME.class}
     noRollbackForClassName = {EXCEPTION_NAME}
     ```

   - transaction readOnly: no update in db

     ```java
     readOnly = true
     readOnly = false
     ```

   - transaction timeout: set time before execute rollback

---

## Issue

1. Test use @Autowire
   ```java
   // add spring-test dependency
   @RunWith(SpringJUnit4ClassRunner.class)
   @ContextConfiguration("classpath:ApplicationContext.xml")
   ```

---

## Question

1. datasource.properties error

   ```xml
   <!-- config -->
   <!-- jdbc.username=root -->
   username=root
   <property name="user" value="${username}"></property>

   <!-- error -->
   cannot get connection for user "administrator"...

   <!-- solution -->
   <!-- use follow config -->
   jdbc.username=root
   ```
