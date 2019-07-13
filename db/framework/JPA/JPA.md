- [JPA](#jpa)
  - [简介](#%E7%AE%80%E4%BB%8B)
  - [Quick Start](#quick-start)
  - [JPA Annotation](#jpa-annotation)
  - [JPA API](#jpa-api)
    - [Persistence](#persistence)
    - [EntityManager](#entitymanager)
    - [EntityTransaction](#entitytransaction)
  - [Mapping Relation](#mapping-relation)
    - [单向多对一](#%E5%8D%95%E5%90%91%E5%A4%9A%E5%AF%B9%E4%B8%80)
    - [单向一对多](#%E5%8D%95%E5%90%91%E4%B8%80%E5%AF%B9%E5%A4%9A)
    - [双向多对一](#%E5%8F%8C%E5%90%91%E5%A4%9A%E5%AF%B9%E4%B8%80)
    - [双向一对一](#%E5%8F%8C%E5%90%91%E4%B8%80%E5%AF%B9%E4%B8%80)
    - [双向多对多](#%E5%8F%8C%E5%90%91%E5%A4%9A%E5%AF%B9%E5%A4%9A)
  - [JAP Cache](#jap-cache)
    - [二级缓存](#%E4%BA%8C%E7%BA%A7%E7%BC%93%E5%AD%98)
  - [JPQL](#jpql)
  - [Integration Spring](#integration-spring)
    - [LocalEntityManagerFactoryBean](#localentitymanagerfactorybean)
    - [JNDI](#jndi)
    - [LocalContainerEntityManagerFactoryBean](#localcontainerentitymanagerfactorybean)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## JPA

### 简介

- 1. JPA 是由 sun 公司制定的 `ORM` 规范; JDBC 是由 sun 公司制定的 `连接数据库` 规范.
- 2. Hibernate 是一种实现了 JPA 规范的 ORM 框架, `JPA 是 Hibernate 功能的一个子集`.
- 3. 优点:
  ```
  1. 标准化
  2. 简单易用, 集成方便
  3. 可媲美JDBC的查询能力[JPQL]
  4. 支持面向对象的高级特性
  ```
- 4. JPA 技术范畴:
  ```
  1. ORM 映射元数据: XML 和 注解
  2. JPA 的 API
  3. 查询语言(JPQL)
  ```

### Quick Start

- 1. create maven project, and the dependency is on the following:

  ```xml
  <!--  hibernate 依赖 -->
  <!-- https://mvnrepository.com/artifact/org.hibernate/hibernate-entitymanager -->
  <dependency>
      <groupId>org.hibernate</groupId>
      <artifactId>hibernate-entitymanager</artifactId>
      <version>5.4.1.Final</version>
  </dependency>

  <!-- mysql 驱动 -->
  <!-- https://mvnrepository.com/artifact/mysql/mysql-connector-java -->
  <dependency>
      <groupId>mysql</groupId>
      <artifactId>mysql-connector-java</artifactId>
      <version>8.0.13</version>
  </dependency>
  ```

- 2. Confige persistence.xml, including but not limit: persistence-unit's name, provider of JPA, Data source, show_sql, format_sql. **`Notice the persistence.xml file msut be in source/ META-INF/.`** The demo configration is on the following:

  ```xml
  <persistence-unit name="NewPersistenceUnit">
      <!--
        配置使用什么ORM 产品作为JPA 的实现
          1.实际上配置的是 javax.persistence.spi.PersistenceProvider 接口的实现类
          2.若JPA项目中只有一个JPA的实现产品, 则也可以不配置该节点.
      -->
      <provider>org.hibernate.jpa.HibernatePersistenceProvider</provider>
      <!-- 添加持久化类-->
      <class>com.augmentum.jpa.JPAEntity</class>

      <properties>
          <!-- 连接数据库的基本信息 -->
          <property name="hibernate.connection.url" value="jdbc:mysql://101.132.45.28:3306/jpa?useSSL=false&amp;serverTimezone=Asia/Shanghai"/>
          <property name="hibernate.connection.driver_class" value="com.mysql.cj.jdbc.Driver"/>
          <property name="hibernate.connection.username" value="root"/>
          <property name="hibernate.connection.password" value="Yu1252068782?"/>

          <!-- 配置JPA 实现产品的基本属性, 配置hibernate 的基本属性 -->
          <!-- 自动显示SQL -->
          <property name="hibernate.show_sql" value="true"/>
          <!-- 格式化sql -->
          <property name="hibernate.format_sql" value="true"/>
          <!--生成数据表的策略-->
          <!--注意这个属性, 自动生成的文件前面没有 hibernate, 要加上 hibernate -->
          <property name="hibernate.hbm2ddl.auto" value="update"/>
          <!-- 使用 MySQL8Dialect -->
          <property name="hibernate.dialect" value="org.hibernate.dialect.MySQL8Dialect"/>
      </properties>
  ```

- 3. create the jpa entity.

  ```java
  @Table(name = "JPAEntity")
  @Entity
  public class JPAEntity {

      private Integer id;
      private String lastName;

      @GeneratedValue(strategy = GenerationType.AUTO)
      @Id
      public Integer getId() {
          return id;
      }

      public void setId(Integer id) {
          this.id = id;
      }

      @Column(name = "Last_Name")
      public String getLastName() {
          return lastName;
      }

      public void setLastName(String lastName) {
          this.lastName = lastName;
      }
  }
  ```

- 4. Finsh to create table info in DB by script.
  ```java
  // 1. 创建 EntityManagerFactory
  String persistenceUnitName = "NewPersistenceUnit";
  EntityManagerFactory entityManagerFactory = Persistence.createEntityManagerFactory(persistenceUnitName);
  // 2. 创建 EntityManager
  EntityManager entityManager = entityManagerFactory.createEntityManager();
  // 3. 开启事务
  EntityTransaction transaction = entityManager.getTransaction();
  transaction.begin();
  // 4. 进行持久化操作
  JPAEntity jpaEntity= new JPAEntity();
  jpaEntity.setAge(13);
  jpaEntity.setEmail("jellily@qq.com");
  jpaEntity.setLastName("jellily");
  entityManager.persist(jpaEntity);
  // 5. 提交事务
  transaction.commit();
  // 6. 关闭 EntityManager
  entityManager.close();
  // 7. 关闭 EntityManagerFactory
  entityManagerFactory.close();
  ```
- 5. Check in the table info in DB. That is OK!

### JPA Annotation

- @Entity
  - 表明该类为实体类, 将映射到指定的数据库表.
- @Table: 实体类与数据库表名称不一致.

  ```java
  @Table(name = "TABLE_NAME")
  ```

  - name 属性: 指定数据库表名
  - 用 table 来生成主键详解: 数据库中有一张数据表 `jpa_id_generators` 用于生产主键值. `好处在于数据库的迁移性`
    ```java
    @TableGenerator(name="ID_GENERATOR",
      table="jpa_id_generators",
      pkColumnName="PK_NAME",
      pkColumnValue="CUSTOMER_ID",
      valueColumnName="PK_VALUE",
      allocationSize=100)
    @GeneratedValue(strategy=GenerationType.TABLE,generator="ID_GENERATOR")
    ```
  - catalog 和 schema: 分别用于设置所属数据库目录和模式

- @Id
  - 表明主键列
  - [推荐]置于属性的 getter 方法之前
- @GeneratedValue
  ```java
  @GeneratedValue(strategy = GenerationType.AUTO)
  ```
  - 标注主键的生成策略
  - type
    - IDENTITY: 自增主键字段
    - AUTO[默认]: JPA 自动选择合适的策略
    - SEQUENCE: 序列产生主键, @SequenceGenerator 注解指定序列名, MySql 不支持这种方式
    - TABLE: 通过表产生主键, 框架借由表模拟序列产生主键, 使用该策略可以使应用更易于数据库移植
- @Basic: 默认的字段注解[不写]
- @Column: 指定实体的属性对应数据表的字段
  - name: 数据库字段名
  - columnDefinition: 数据库字段数据类型
  - unique
  - nullable
  - length
- @Transient
  - 不映射到数据库字段
- @Temporal: 指定 Date 数据类型的精度; 数据库中的 Date 类型有三种: TEAR, TIME, DATE, DATETIME, TIMESTAMP
  ```java
  @Temporal(TemporalType.TIME)
  ```
  - type:
    - TIME: 时间
    - DATE: 日期
    - DATETIME: 时间日期

### JPA API

#### Persistence

- createEntityManagerFactory(String persistenceUnitName) 用来创建 `EntityManagerFactory` 实例

#### EntityManagerFactory

- createEntityManager(): 用来创建 `EntityManager` 实例
- createEntityManager(Map map): map 提供 EntityManager 的属性
- isOpen(): 检查 EntityManagerFactory 是否处于打开状态, 默认打开[只有调用 close()方法才关闭]
- close()：关闭 EntityManagerFactory, 释放资源

#### EntityManager: `持久化核心`

##### Knowledge

- 实体的状态:
  ```java
  新建状态: 新创建的对象, 尚未拥有持久性主键.
  持久化状态: 已经拥有持久性主键并和持久化建立了上下文环境
  游离状态: 拥有持久化主键, 但是没有与持久化建立上下文环境
  删除状态: 拥有持久化主键, 已经和持久化建立上下文环境, 但是从数据库中删除.
  ```

##### API

- find(Class<T> entityClass,Object primaryKey)
- getReference(Class<T> entityClass,Object primaryKey)
- persistence(T entity)
- remove(T entity)
- merge(T entity)
- EntityTransaction
- flush()
- setFlushMode(FlushModeType flushMode)
- getFlushMode()
- refresh(T entity)
- clear()
- contains(T entity)
- isOpen()
- close()
- createQuery (String qlString)
- createNamedQuery (String name)
- createNativeQuery (String sqlString)
- createNativeQuery (String sqls, String resultSetMapping)
- getTransaction()

#### EntityTransaction

- begin ()
- commit ()
- rollback ()
- setRollbackOnly ()
- getRollbackOnly ()
- isActive ()

### Mapping Relation

#### 单向多对一

#### 单向一对多

#### 双向多对一

#### 双向一对一

#### 双向多对多

### JAP Cache

#### 二级缓存

### JPQL

- 使用 Hibernate 的查询缓存
- ORDER BY 和 GROUP BY
- 关联查询
- 子查询 和 JPQL 函数
- UPDATE 和 DELETE

### Integration Spring

#### LocalEntityManagerFactoryBean

#### JNDI

#### LocalContainerEntityManagerFactoryBean
