## 4. lock

### 4.1. table locks[perfer read]

- 偏向 myisam 存储引擎, 开销小, 加锁快, 无死锁, 锁定粒度大, 发生锁冲突的概率最高, 并发最低

- analysis

  ```sql
  show status like 'table%';

  +----------------------------+-------+---------------------------------------- +
  | variable_name              | value |             description                 |
  +----------------------------+-------+-----------------------------------------+
  | table_locks_immediate      | 843   | table lock time and immediate execute   |
  | table_locks_waited         | 0     | occur competition time due to table lock|
  +----------------------------+-------+-------+
  ```

- myisam 查询时会自动给表加读锁; 修改时会自动加写锁

- table lock operation

  ```sql
  -- lock table
  lock table tbale_name read/write, tbale_name read/write ···

  -- look up locked table
  show open tables;

  -- unlock table
  unlock tables;
  ```

#### 4.1.1 read lock

- env: session01 have read lock, session2 no limit

- session01:

  - [read lock table] session01 just can read lock table
  - [update lock table] cannot update this table
  - [read others] even cannot read other tables
  - [update others] cannot update operation until unlock

- session02:

  - [read lock table] can read session01 locked table: `because read lock is shared`
  - [update lock table] blocked by session01 until session01 unlock table, `then finish update operation`.
  - [read others] can read other tables
  - [update others] can update others table without limit

#### 4.1.2 write lock

- env: session01 have write lock, session2 no limit

- session01:

  - [read lock table] session01 just can read lock table
  - [update lock table] can update this table
  - [read others] even cannot read other tables
  - [update others] cannot update operation until unlock

- session02:

  - [read lock table] blocked by session01 until session01 unlock table: `because write lock is exclusive`
  - [update lock table] blocked by session01 until session01 unlock table, `then finish update operation`.
  - [read others] can read other tables
  - [update others] can update others table without limit

### 4.2 row locks[perfer write]

- 偏向 innodb 存储引擎, 开销大, 加锁慢; 会出现死锁; 锁定粒度最小, 发生锁冲突的概率最低, 并发度也最高

  ```sql
  -- disable auto commit
  set autocommit = 0;
  ```

- pre evn

  - innodb
  - disable auto commit all
  - 1. update sql, no commit, it can be read by itself session, and cannot be read by other session
    - only when all session commit, data can be read all session shared.
  - 2. if session2 all update this row, it will be blocked until session01 commited;
  - 3. if session2 update other rows, it will be ok without limit

- 无索引行锁升级为表锁

  ```sql
  -- b type is varchar, it will become table lock, other sessions update operation will be blokced
  update test_innodb_lock set a=40001 where b = 4000;
  -- type: all, extra: using where
  explain update test_innodb_lock set a=40001 where b = 4000; -- index invalid
  -- type: range, extra: using where
  explain update test_innodb_lock set a=40001 where b = '4000';  -- index valid
  ```

- 间隙锁危害

  - 定义: 当我们用范围条件而不是相等条件检索数据, 并请求共享或排他锁时, innodb 会给符合条件的已有数据记录的索引项加锁;
    对于键 值在条件范围内但并不存在的记录, 叫做 "间隙(gap)". innodb 也会对这个 "间隙" 加锁, 这种锁机制就是所谓的间隙锁(next-key 锁)

  - sql

  ```sql
  -- no a = 2 data,
  -- session01:
  update test_innodb_lock set b='40001' where a > 1 and a< 6;  -- ok

  --session02:
  insert into test_innodb_lock values(2, '20000');  -- blocked
  update test_innodb_lock b set b='2000' where a=2;  -- ok

  -- if and only if session01 commit, so other session can be un blocked.
  ```

- 常考如何锁定一行

  - sql

  ```sql
  set autocommit = 0;
  -- session01:
  begin;
  select * from table_name where id = 1 for update;
  -- commit;

  -- session2: it will blockd until session01 commit
  update table_name set column_name = 'xx' where id = 1;  -- blocked
  ```

- analysis

  ```sql
  -- look up
  show status like 'innodb_row_lock%';
  +-------------------------------+-------+
  | variable_name                 | value |
  +-------------------------------+-------+
  | innodb_row_lock_current_waits | 0     |
  | innodb_row_lock_time          | 56268 |
  | innodb_row_lock_time_avg      | 28134 |
  | innodb_row_lock_time_max      | 51008 |
  | innodb_row_lock_waits         | 2  ☆  |
  +-------------------------------+-------+
  ```

- 行锁优化建议
  - 尽可能让所有数据检索都通过索引来完成, 避免无索引行锁升级为表锁
  - 合理设计索引, 尽量缩小锁的范围
  - 尽可能较少检索条件, 避免间隙锁
  - 尽量控制事务大小, 减少锁定资源量和时间长度
  - 尽可能低级别事务隔离

### 4.3 leaf lock[less use]

- 开销和加锁时间界于表锁和行锁之间: 会出现死锁; 锁定粒度界于表锁和行锁之间, 并发度一般.

---

## 6. master-slave replication

### 6.1 theory of replication

1. slave 会从 master 读取 binlog 来进行数据同步
2. step
   - master 将改变记录到二进制日志[binary log]. 这些记录过程叫做二进制日志时间, binary log events
   - slave 将 master 的 binary log ebents 拷贝到它的中继日志[relay log]
   - slave 重做中继日志中的时间, 将改变应用到自己的数据库中. mysql 复制是异步的且串行化的

### 6.2 principle of replication

1. 每个 slave 只有一个 master
2. 每个 slave 只能有一个唯一的服务器 id
3. 每个 master 可以有多个 salve
4. 主从复制有延迟的问题

### 6.3 config

1. 条件
   - mysql 版本一致且后台以服务运行
2. 要求

   - 主从都配置在[mysqld]结点下, 都是小写

3. 修改 master 的配置文件

   - 1. [必须] 主服务器唯一 id: `server-id =1`
   - 2. [必须] 启用二进制日志: `log_bin=custom_path`
   - 3. [可选] 启动错误日志: `log_error=custom_path`
   - 4. [可选] 根目录: `basedir= /usr`
   - 5. [可选] 临时目录: `tmpdir= /tmp`
   - 6. [可选] 数据目录: `datadir= /var/lib/mysql`
   - 7. [可选] read-only=0: 表示 master 读写都可以
   - 8. [可选] 设置不要复制的数据库: `binlog_ignore_db= include_database_name`
   - 9. [可选] 设置需要复制的数据: `#binlog_do_db= include_database_name`

4. 修改 slave 的配置文件

   - 1. [必须] 从服务器唯一 id
   - 2. [可选] 启用二进制文件

5. 因修改过配置文件, 请 master and slave restart

6. master and slave 都关闭防火墙

7. master 建立账户并授权给 slave

   ```sql
   grant replication slave on *.* to 'zhangsan'@'slave_ip' identified by '123456';
   flush privileges;

   -- look up master status
   show master status;  -- file column: which file; position: where to slave
   ```

8. slave 配置需要复制的主机

   ```sql
   -- we should use show master status; to get new file and position each time
   change master to master_host = 'master_ip', master_user = 'zhangsan', master_password = '123456', master_log_file = 'filename', master_log_pos=position_number;

   start slave;

   -- llok up slave status and must slave_io_running:yes and slave_sql_running:yes
   show slave status;

   stop slave;
   ```

---

## sample

1. create table

```sql
create table db_test03(
  a int primary key not null auto_increment,
  c1 char(10),
  c2 char(10),
  c3 char(10),
  c4 char(10),
  c5 char(10)
);

insert into db_test03(c1,c2,c3,c4,c5) values('a1','a2', 'a3', 'a4','a5');
insert into db_test03(c1,c2,c3,c4,c5) values('b1','b2', 'b3', 'b4','b5');
insert into db_test03(c1,c2,c3,c4,c5) values('c1','c2', 'c3', 'c4','c5');
insert into db_test03(c1,c2,c3,c4,c5) values('d1','d2', 'd3', 'd4','d5');
insert into db_test03(c1,c2,c3,c4,c5) values('e1','e2', 'e3', 'e4','e5');
```

2. create index

```sql
select * from test03;
create index idx_c1_c2_c3_c4 on test03(c1, c2, c3, c4) ;
```

3. `=`

```sql
-- show index from db_test03;

-- type: ref ref: const,const,const,const extra: null
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c3 = 'a3' and c2 = 'a2' and c4 = 'a4' ;
-- type: ref ref: const,const,const,const extra: using index
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c3 = 'a3' and c2 = 'a2' and c4 = 'a4' ;

-- type: ref ref: const,const,const,const extra: using where
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c3 = 'a3' and c2 = 'a2' and c4 = 'a4' and c5 = 'a5' ;
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c3 = 'a3' and c2 = 'a2' and c4 = 'a4' and c5 = 'a5' ;
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c3 = 'a3' and c2 = 'a2' and c5 = 'a5'  and c4 = 'a4' ;
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c3 = 'a3' and c2 = 'a2' and c5 = 'a5'  and c4 = 'a4' ;

-- type: ref ref: const extra: using index condition; using where
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c3 = 'a3'  and c5 = 'a5'  and c4 = 'a4' ;
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c3 = 'a3'  and c5 = 'a5'  and c4 = 'a4' ;
-- type: ref ref: const,const,const,const extra: using index
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c3 = 'a3' and c2 = 'a2' and c4 = 'a4' ;

-- type: ref ref: const,const extra: using index condition
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c2 = 'a2' and c4 = 'a4' ;
-- type: ref ref: const,const extra: using where; using index
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' and c4 = 'a4' ;

-- type: ref ref: const,const extra: null
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c2 = 'a2' ;
-- type: ref ref: const,const extra: using index
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' ;

-- type:  all ref: null extra: using where
explain select c1, c2, c5 from db_test03 where c2 = 'a3' and c4 = 'a4'
-- type: index ref: null extra: using where; using index
explain select c1, c2 from db_test03 where c2 = 'a3' and c4 = 'a4'
```

4. `> <`

```sql
-- type: range ref: null extra: using index condition: 索引使用了 c1, c2, c3
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c2 = 'a2' and c3 > 'a3' and  c4 = 'a4';
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c2 = 'a2' and  c4 = 'a4' and c3 > 'a3' ;
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c2 = 'a2' and  c4 = 'a4' and c3 > 'a3' and c5 = 'a5';
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c2 = 'a2' and  c4 = 'a4' and c3 > 'a3' and c5 > 'a5';

-- type: range ref: null extra: using where; using index: 索引使用了 c1, c2, c3
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' and c3 > 'a3' and  c4 = 'a4';
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' and  c4 = 'a4' and c3 > 'a3';
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' and  c4 = 'a4' and c3 > 'a3' and c5 > 'a5';

-- type: ref ref: const extra: using index condition
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c3 > 'a3' and  c4 = 'a4';
-- type: ref ref: const extra: using where; using index
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c3 > 'a3' and  c4 = 'a4';

-- type: all ref: null extra: using where
explain select c1, c2, c3, c4, c5 from db_test03 where c5 > 'a5';
explain select c1, c2, c3, c4, c5 from db_test03 where c2 > 'a5';
explain select c1, c2, c3, c4, c5 from db_test03 where c1 > 'a5';

-- type: index extra: using where; using index
explain select c1, c2, c3, c4 from db_test03 where c5 > 'a5';
explain select c1, c2, c3, c4 from db_test03 where c2 > 'a5';
-- type: range extra: using where; using index
explain select c1, c2, c3, c4 from db_test03 where c1 > 'a5';
```

5. `order by`

```sql
-- type: ref ref: const,const extra: using index condition
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c2 = 'a2' and  c4 = 'a4' order by c3;
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c3;
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c3, c2;
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c2, c3;
-- type: ref ref: const,const extra: using index condition; using where
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c2 = 'a2' and c5 = 'a5' order by c2, c3;
-- type: ref ref: const,const extra: using where; using index
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' and  c4 = 'a4' order by c3;
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c3;
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c3, c2;
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c2, c3;
-- type: ref ref: const,const extra: using index condition; using where
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' and c5 = 'a5' order by c2, c3;

-- type: ref ref: const,const extra: using index condition
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c3, c4;
explain select c1, c2, c3, c4, c5 from db_test03 where c2 = 'a2' and c1 = 'a1' order by c3, c4;
-- type: ref ref: const,const extra: using where; using index
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c3, c4;
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c3, c4;

-- type: ref ref: const,const extra: using index condition; using filesort
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c4, c3;
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c4, c5;
-- type: ref ref: const,const extra: using where; using index; using filesort
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c4, c3;
-- type: ref ref: const,const extra: using index condition; using filesort
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c4, c5;
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c3, c5;

-- type: ref ref: const,const extra: using where; using index
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c3, c2;
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c3;

-- type: ref ref: const,const extra: using index condition; using filesort
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c5;
-- type: ref ref: const,const extra: using index condition; using filesort
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c5;

-- type: all extra: using where; using filesort
explain select c1, c2, c3, c4, c5 from db_test03 where c2 = 'a2' and  c4 = 'a4' order by c3;
-- type: index extra: using where; using index; using filesort
explain select c1, c2, c3, c4 from db_test03 where c2 = 'a2' and  c4 = 'a4' order by c3;

-- type: ref ref: const,const extra: using index condition
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c3;
-- type: ref ref: const,const extra: using where; using index
explain select c1, c3, c2, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c3;

-- type: all extra: using where; using filesort
explain select c1, c2, c3, c4, c5  from db_test03 where c1 = 'a2' order by c2;
-- type: index extra: using where; using index; using filesort
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a2' order by c2;

-- type: all extra: using where; using filesort
explain select c1, c2, c3, c4, c5  from db_test03 where c2 = 'a2' order by c3;
-- type: index extra: using where; using index; using filesort
explain select c1, c2, c3, c4 from db_test03 where c2 = 'a2' order by c3;

-- type: ref ref: const,const extra: using index condition
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c3;
explain select c1, c2, c3, c4, c5 from db_test03 where c2 = 'a2' and c1 = 'a1' order by c3;
explain select c1, c2, c3, c5, c4 from db_test03 where c2 = 'a2' and c1 = 'a1' order by c3;
-- type: ref ref: const,const extra: using where; using index
explain select c1, c3, c2, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c3;
explain select c1, c3, c2, c4 from db_test03 where c2 = 'a2' and c1 = 'a1' order by c3;

-- type: ref ref: const,const extra: using index condition; using filesort
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c4;
-- type: ref ref: const,const extra: using where; using index; using filesort
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' order by c4;

-- type: all extra: using filesort
explain select c1, c2, c3, c4, c5 from db_test03 order by c4;
explain select c1, c2, c3, c4, c5 from db_test03 order by c1;
explain select c1, c2, c3, c4, c5 from db_test03 order by c5;
-- type: index extra: using index; using filesort
explain select c1, c2, c3, c4 from db_test03 order by c4;
explain select c1, c2, c3, c4 from db_test03 order by c1;
explain select c1, c2, c3, c4 from db_test03 order by c5;
```

6. `group by`

```sql
-- type: ref ref: const extra: using index condition; using where
explain select c1, c2 c4, c5 from db_test03 where c1 = 'a1' and c4 = 'a4' and c5 ='a5' group by c2;
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c4 = 'a4' and c5 ='a5' group by c2, c3;
-- type: ref ref: const extra: using index condition; using where; using temporary; using filesort
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c4 = 'a4' and c5 ='a5' group by c3, c2;

-- type: ref ref: const extra: using where; using index
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c4 = 'a4' group by c2, c3;
explain select c1, c3, c2, c4 from db_test03 where c1 = 'a1' and c4 = 'a4' group by c2, c3;
explain select c1, c3, c2, c4 from db_test03 where c4 = 'a4' and c1 = 'a1' group by c2, c3;
-- type: ref ref: const extra: using where; using index; using temporary; using filesort
explain select c1, c3, c4 from db_test03 where c1 = 'a1' and c4 = 'a4' group by c3;
explain select c1, c2, c4 from db_test03 where c1 = 'a1' and c2 = 'a2' group by c4;
-- type: ref ref: const extra: using where; using index
explain select c1, c2, c3 from db_test03 where c1 = 'a1' and c3 = 'a4' group by c2;
-- type: ref ref: const extra: using where; using index
explain select c1, c3, c2 from db_test03 where c1 = 'a1' and c2 = 'a2' group by c3;

-- type: ref ref: const extra: using index condition; using temporary; using filesort
explain select c1, c2, c3, c4, c5 from db_test03 where c1 = 'a1' and c4 = 'a4' group by c2, c3, c5;
explain select c1, c2, c3, c4 from db_test03 where c1 = 'a1' and c4 = 'a4' group by c2, c3, c5;

-- type：index extra: using index
explain select c1 from db_test03 group by c1;
-- type：all extra: using temporary; using filesort
explain select c5 from db_test03 group by c5;

```

---

## issue

1. sql load sequence: from first
   ![avatar](/static/image/db/mysql-machine-sequence.png)
2. join
   > from a, b: 笛卡尔积
   > from a, b where a.bid = b.aid: inner join
3. union: merge result sets and remove duplicates
4. group by must used with order by
5. explain:
6. 复合 index 是有顺序的, 且 > < 之后的 index 会失效
7. 左连接应该加在右表上;
8. 小表做主表
9. 优先优化 nestedloop 的内层循环
10. 保证 join 语句的条件字段有 index
11. **`varchar 类型必须有单引号`**
12. 少用 or, 用它连接时会索引失效
13. 覆盖索引下 index 是永远不会失效的
14. index(a, b, c) 如果中间断了之后的索引都会失效; `> <`之后的索引也会失效; `like` 之后索引也会失效
15. order by 后的字段有序
16. 定值, 范围还是排序, 一般 order by 是给个范围
17. group by 基本上都需要进行排序, 会有临时表产生, **实质是先排序后分组**
18. exists: 将主查询中的数据放到子查询中验证, 根据验证结果(true/false)来决定主查询数据是否保留.
19. where 高于 having, 能写在 where 限定的条件就不要去 having 限定
20. **表读锁只允许读自己这张表, 其他 session 只阻塞修改这张表**
21. **表写锁只允许读写自己这张表, 其他 session 只阻塞读写这张表**
22. 表/行锁: `读锁会阻塞写, 但是不会堵塞读; 而写锁则会把读和写都堵塞`
23. 通过范围查找会锁定范围内所有的索引键值, 即使这个键值不存在.

## reference

1. https://blog.csdn.net/weixin_33755554/article/details/93881494
2. https://my.oschina.net/bigdataer/blog/1976010
