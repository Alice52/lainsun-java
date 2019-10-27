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

- introduce

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

- property and member variables

  - property: setxxx
  - member variables: variable

- get bean:

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

- DI

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

- ```xml

  ```
