## Spring MVC

### Introduce

1. feature
   - Born to have integration with spring
   - Support Rest API and JSON response
   - DispatcherServlet
   - Handler and filter
   - ModelAndView.
   - Request-response model
2. framework diagram

### Quick Start

- web.xml: config ServletDispatcher

  ```xml
  <!-- Servlet Filters -->
  <filter>
      <description>Multipart MIME handling filter for Cocoon</description>
      <display-name>Cocoon multipart filter</display-name>
      <filter-name>CocoonMultipartFilter</filter-name>
      <filter-class>org.apache.cocoon.servlet.multipart.MultipartFilter</filter-class>
  </filter>

  <!-- Filter mappings -->
  <filter-mapping>
      <filter-name>CocoonMultipartFilter</filter-name>
      <servlet-name>Cocoon</servlet-name>
  </filter-mapping>

  <!-- Servlet Context Listener -->
  <listener>
      <listener-class>org.springframework.web.context.ContextLoaderListener</listener-class>
  </listener>

  <!-- Servlet Configuration -->
  <servlet>
      <descriptio/>
      <display-name>DispatcherServlet</display-name>
      <servlet-name>DispatcherServlet</servlet-name>
      <servlet-class>org.springframework.web.servlet.DispatcherServlet</servlet-class>
      <init-param>
        <param-name>encoding</param-name>
        <param-value>UTF-8</param-value>
      </init-param>
      <init-param>
        <!-- create container: param-name default: /WEB-INF/<servlet-name>-servlet.xml -->
        <param-name>contextConfigLocation</param-name>
        <param-value>classpath:ApplicationContext.xml</param-value>
      </init-param>
      <!-- speciy Servlet create time: created when request arrival; created on startup[default]. -->
      <!-- number is squence of loading Servlet -->
      <load-on-startup>1</load-on-startup>
  </servlet>
  <!-- URL space mappings -->
  <servlet-mapping>
      <servlet-name>DispatcherServlet</servlet-name>
      <!-- defience between /* and /: /* donot handle .jsp request -->
      <url-pattern>/</url-pattern>
  </servlet-mapping>
  ```

- applicationContext.xml: config container
  ```xml
  <!-- spring container -->
  <context:component-scan />
  <!-- ViewResolver： f-b will not ned this -->
  <bean class="org.springframework.web.servlet.view.InternalResourceViewResolver">
    <property name="prefix" value="/WEB-INF/views/"/>
    <property name="suffix" value=".jsp"/>
  </bean>
  <bean/>
  <context:placeholder/>
  ```

### Controller

1. Annotation

   ```java
   @Controller
   @RestController
   @RequestMapping
   @Resource // javax.annotation.Resource
   @Autowired
   @ModelAttribute // Add to Method or Parameter
   @SessionAttributes 
   @PathVariable // Get path value
   @requestParam // Get paramters
   @ResponseBody
   @Component
   @Repository
   ```

2. DispatcherServlet

### Handler

1. handlerMapping
2. HandlerAdapter
3. Handler

### ModelAndView

1. viewResolver
2. LocalResolver
3. view

### Filter

### Listener

### Sub-structure

### LifeCycle

### Processing flows

1. start up application

   - start up tomcat will load `DispatcherServlet` in /webapp/WEB-INF/web.xml,
   - then load spring config file `ApplicationContext.xml` to create container as configed in web.xml
   - spring will scan component and work as annatation
   - @controller will marked requestHandler, then can handle request.

2. UI request
   - request arrive `web.xml` and pattern with tag <url-pattern>,
   - then get which DispatcherServlet will handle this request
   - then this request will be sended to @controller
   - @controller method will return result[json]
   - ViewResolver will get result and combine `physical view path: prefix + result + suffix`
   - forwaord to specify VIEW

### Rest API

### JOSN
