## active mq

### [install activemq](../common/docker/docker.md#install-activemq)

```shell
cd ACTIVEMQ_BIN
./activemq start xbean:file:/CONFIG_FILE_PATH
```

### mq dimension

- API send and receive
- MQ high avaliable
- MQ cluster and fault tolerant configuration
- MQ durable
- MQ delayed delivery and timed delivery
- MQ ACK
- Spring integration
- Implementation language

### JMS

![avatar](/static/image/mq/JMS.png)

#### compoent

- JMS Provider: mq server
- JMS Producer
- JMS Consumer
- JMS Message

  - message header

    |      type       |                function                |
    | :-------------: | :------------------------------------: |
    | JMSDestination  |          message destination           |
    | JMSDeliveryMode | DeliveryMode.NON_PERSISTENT/PERSISTENT |
    |  JMSExpiration  |            set expire time             |
    |   JMSPriorty    |             0-9 default 4              |
    |  JMSMessageID   |         unique message signal          |
    |       ...       |                  ...                   |

  - message property: use for `specific paramter`, such as SERVICE, ACTION; `avoid duplicate`, `specific mark`
  - message body: Encapsulate specific message content

    | message type  |           function            |
    | :-----------: | :---------------------------: |
    |  TextMessage  |         common string         |
    |  MapMessage   | key: string; value: java type |
    | BytesMessage  |            bytes[]            |
    | StreamMessage |       java stream data        |
    | ObjectMessage |       serialize object        |

#### high available

- cluster
- persistence and store in db

  - queue producer: the previous message will be restore.

  ```java
  // producer
  connection.start();
  ...
  producer.setDeliveryMode(DeliveryMode.NON_PERSISTENT);

  // consumer
  connection.start();
  ...
  MessageConsumer consumer = session.createConsumer(topic);
  ```

  - topic producer: the previous message will be missing.

  ```java
  // publisher
  producer.setDeliveryMode(DeliveryMode.NON_PERSISTENT);
  connection.start();

  // subscriber
  TopicSubscriber topicSubscriber = session.createDurableSubscriber(topic, UUID.randomUUID().toString());
  connection.start();
  ```

  - message:

  ```java
  message.setJMSDeliveryMode(DeliveryMode.PERSISTENT);
  ```

- acknowledge[consumer]

  |        type         |              function              | transaction |          code          |
  | :-----------------: | :--------------------------------: | :---------: | :--------------------: |
  |  AUTO_ACKNOWLEDGE   |          default auto ack          |    false    |                        |
  | CLIENT_ACKNOWLEDGE  |           Manual receipt           |    false    | message.acknowledge(); |
  | DUPS_OK_ACKNOWLEDGE |   allow part duplicated message    |    false    |
  | SESSION_TRANSACTED  | open transactin so ack is not work |    true     |

- transaction[producer]

  ```java
  // producer
  Session session = connection.createSession(true, Session.AUTO_ACKNOWLEDGE);
  ...
  session.commit(); // for rollback

  // consumer
  // if the consumer open transaction, must be use commit; otherwise it will lead message duplicated conusmer.
  ```

##### durable in db[mysql/kahadb/journal]

### JMS consumer and producer

- common
  - many consumer will consume message in fair.
- queue: load_balance
- topic: sub_pub
  - fisrt start consumer, or the message may not be consumed.
  - `subscribe status`
    - `没有订阅`: `订阅之前的消息不可收到`
    - `已订阅`: `订阅之后的消息可以收到`
    - `订阅后在线`: `消息都可以收到`
    - `订阅后离线`: `上线之后可以收到之前的消息-- clientID 必须一样; 如果 clientID 是 random 的, 则离线时的消息都不可以收到, 除非将message store in db`

### broker

- activemq instance: impliment by java code

### spring quick start

- dependencies

  ```xml
  <dependencies>
      <dependency>
          <groupId>org.apache.commons</groupId>
          <artifactId>commons-pool2</artifactId>
          <version>2.6.0</version>
      </dependency>
      <dependency>
          <groupId>org.apache.activemq</groupId>
          <artifactId>activemq-broker</artifactId>
          <version>5.14.3</version>
      </dependency>
      <dependency>
          <groupId>org.apache.activemq</groupId>
          <artifactId>activemq-client</artifactId>
          <version>5.14.3</version>
      </dependency>
      <dependency>
          <groupId>org.apache.activemq</groupId>
          <artifactId>activemq-pool</artifactId>
          <version>5.14.3</version>
      </dependency>

      <!--1. SPRING-->
      <dependency>
          <groupId>org.springframework</groupId>
          <artifactId>spring-core</artifactId>
          <version>4.3.18.RELEASE</version>
      </dependency>
      <dependency>
          <groupId>org.springframework</groupId>
          <artifactId>spring-context</artifactId>
          <version>4.3.18.RELEASE</version>
      </dependency>
      <dependency>
          <groupId>org.springframework</groupId>
          <artifactId>spring-jms</artifactId>
          <version>4.3.18.RELEASE</version>
      </dependency>

      <dependency>
          <groupId>org.apache.xbean</groupId>
          <artifactId>xbean-spring</artifactId>
          <version>3.16</version>
      </dependency>

      <!-- junit -->
      <dependency>
          <groupId>junit</groupId>
          <artifactId>junit</artifactId>
          <version>4.12</version>
          <scope>test</scope>
      </dependency>

      <!--LOGGER jar-->
      <dependency>
          <groupId>ch.qos.logback</groupId>
          <artifactId>logback-classic</artifactId>
          <version>1.2.3</version>
      </dependency>
      <dependency>
          <groupId>ch.qos.logback</groupId>
          <artifactId>logback-core</artifactId>
          <version>1.2.3</version>
      </dependency>
  </dependencies>
  ```

- application.context

  ```xml
  <?xml version="1.0" encoding="UTF-8"?>
  <beans xmlns="http://www.springframework.org/schema/beans"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xmlns:context="http://www.springframework.org/schema/context"
      xsi:schemaLocation="http://www.springframework.org/schema/beans http://www.springframework.org/schema/beans/spring-beans.xsd   http://www.springframework.org/schema/context http://www.springframework.org/schema/context/spring-context.xsd">

      <context:component-scan base-package="cn.edu.ntu.spring.integration.activemq"/>
      <context:property-placeholder location="classpath:application.properties"/>

      <!-- config producer -->
      <bean id="jmsFactory" class="org.apache.activemq.pool.PooledConnectionFactory" destroy-method="stop">
          <property name="connectionFactory">
              <bean class="org.apache.activemq.ActiveMQConnectionFactory">
                  <property name="brokerURL" value="${activemq.url}"/>
              </bean>
          </property>
          <property name="maxConnections" value="100"/>
      </bean>

      <!-- config queue -->
      <bean id="destinationQueue" class="org.apache.activemq.command.ActiveMQQueue">
          <constructor-arg index="0" value="p2p_queue"/>
      </bean>
      <!-- config topic -->
      <bean id="destinationTopic" class="org.apache.activemq.command.ActiveMQTopic">
          <constructor-arg index="0" value="sub_topic"/>
      </bean>

      <!-- config listener: do not need start consumer -->
      <bean id="jmsContainer" class="org.springframework.jms.listener.DefaultMessageListenerContainer">
          <property name="connectionFactory" ref="jmsFactory"/>
          <property name="destination" ref="destinationQueue"/>
          <property name="messageListener" ref="myMessageListener"/>
      </bean>

      <!-- config JMS tool -->
      <bean id="jmsTemplate" class="org.springframework.jms.core.JmsTemplate">
          <property name="connectionFactory" ref="jmsFactory"/>
          <property name="defaultDestination" ref="destinationQueue"/>
          <property name="messageConverter">
              <bean class="org.springframework.jms.support.converter.SimpleMessageConverter"/>
          </property>
      </bean>
  </beans>
  ```

- producer

  ```java
  @Service
  public class Producer2Queue {
    private static final Logger LOGGER = LoggerFactory.getLogger(Producer2Queue.class);

    @Autowired private JmsTemplate jmsTemplate;

    public static void main(String[] args) {
        ApplicationContext applicationContext =
            new ClassPathXmlApplicationContext("applicationContext.xml");
        Producer2Queue producer = (Producer2Queue) applicationContext.getBean("producer2Queue");
        producer.jmsTemplate.send(
            session -> {
            TextMessage message = session.createTextMessage("Spring integration ActiveMQ!");
            LOGGER.info("Send text message: {} success.", message);
            return message;
            });
        LOGGER.info("Send message over!");
    }
  }
  ```

- consumer

  ```java
  @Service
  public class ConsumerFromQueue {
      private static final Logger LOGGER = LoggerFactory.getLogger(ConsumerFromQueue.class);

      @Autowired
      private JmsTemplate jmsTemplate;

      public static void main(String[] args) {
          ApplicationContext applicationContext =
                  new ClassPathXmlApplicationContext("applicationContext.xml");
          ConsumerFromQueue consumer = (ConsumerFromQueue) applicationContext.getBean("consumerFromQueue");
          // just get one message from queue
          String value = (String) consumer.jmsTemplate.receiveAndConvert();
          LOGGER.info("Consume text message: {} success!", value);
      }
  }
  ```

- can config listener: do not need start consumer

  ```java
  @Component
  public class MyMessageListener implements MessageListener {
      private static final Logger LOGGER = LoggerFactory.getLogger(MyMessageListener.class);

      @Override
      public void onMessage(Message message) {
          Optional.ofNullable(message).ifPresent(mg-> {
              TextMessage textMessage = (TextMessage) mg;
              try {
                  String text = textMessage.getText();
                  LOGGER.info("Consume text message: {} success.", text);
              } catch (JMSException jmsException) {
                  LOGGER.info("Failed to consume text message: {}, cause by {}.", textMessage, jmsException);
              }
          });
      }
  }
  ```

### spring boot

- the message is default persisten
- sender: queue/topic will also work;

```java
@SendTo("${queue-reply}")
// will forward message from queue to queue, or topic to topic
```

- receiver: queue/topic will work according to application.yml;
  - yml config + listener destionantion

### transaport

- config

  ```xml
  <!-- default: tcp; nio: better performance -->
  <transportConnectors>
      <transportConnector name="nio" uri="nio://0.0.0.0:61616"/>
      <transportConnector name="auto+nio" uri="auto+nio://0.0.0.0:61616?maximumConnections=1000&amp;wireFormat.maxFrameSize=104857600"/>
      <!-- <transportConnector name="openwire" uri="tcp://0.0.0.0:61616?maximumConnections=1000&amp;wireFormat.maxFrameSize=104857600"/> -->
      <transportConnector name="amqp" uri="amqp://0.0.0.0:5672?maximumConnections=1000&amp;wireFormat.maxFrameSize=104857600"/>
      <transportConnector name="stomp" uri="stomp://0.0.0.0:61613?maximumConnections=1000&amp;wireFormat.maxFrameSize=104857600"/>
      <transportConnector name="mqtt" uri="mqtt://0.0.0.0:1883?maximumConnections=1000&amp;wireFormat.maxFrameSize=104857600"/>
      <transportConnector name="ws" uri="ws://0.0.0.0:61614?maximumConnections=1000&amp;wireFormat.maxFrameSize=104857600"/>
  </transportConnectors>
  ```

- v1.13.0: port is consistent
  ```xml
  <transportConnector name="auto+nio" uri="auto+nio://0.0.0.0:61616"/>
  <!--
    tcp://101.37.174.197:61616
    nio://101.37.174.197:61616
  -->
  ```
