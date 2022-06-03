### 间隙锁: 默认是 next-key, 会根据情况进行升级

1. 基于[非]唯一索引 + 只有插入时才会发生[解决幻读问题]
2. 加锁的基本单位是 (next-key lock）[本质锁的是 B+的 key],他是前开后闭原则
3. **在唯一索引的等值查询且值不存在时会退化为间隙锁**: 开区间
4. **在唯一索引的等值查询且值存在时会退化为 record lock**
5. **在唯一索引的非等值查询时标准 next-key lock**

```sql
CREATE TABLE `gaplock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 NOT NULL,
  `age` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  -- KEY `IDX_NAME` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4

-- age 上有普通索引
drop index IDX_AGE on gaplock;
create index IDX_AGE on gaplock(age);
show index from gaplock;

insert into gaplock values(5, 5, 5);
insert into gaplock values(10, 10, 10);
insert into gaplock values(15, 15, 15);
insert into gaplock values(20, 20, 20);
select * from gaplock;

set autocommit = 0;
rollback;
commit;
```

| id  | name | age |
| :-: | :--: | :-: |
|  5  |  5   |  5  |
| 10  |  10  | 10  |
| 15  |  15  | 15  |
| 20  |  20  | 20  |
| 25  |  25  | 25  |

- 此时会维护一个 `(-∞, 5] (5, 10] (10, 15] (15, 20] (20, 25] (25, +supernum]` 的间隙所

#### 1.间隙锁简单案例

| 步骤 |                      事务 A                      |                      事务 B                       |
| :--: | :----------------------------------------------: | :-----------------------------------------------: |
|  0   |                      begin;                      |                      begin;                       |
|  1   | select \* from gaplock where id = 11 for update; |                         -                         |
|  2   |                        -                         | select \* from gaplock where id = 12 for update;  |
|  3   |                        -                         |  insert into gaplock value(12,12,12); **block**   |
|  4   |                        -                         | insert into gaplock value(20,12,12); **un-block** |
|  5   |                     commit;                      |                         -                         |
|  6   |                        -                         |                   will un-block                   |

- 此时锁区间是 (10, 15): 因为 Id 是唯一性主键

| 步骤 |                      事务 A                      |                      事务 B                       |
| :--: | :----------------------------------------------: | :-----------------------------------------------: |
|  0   |                      begin;                      |                      begin;                       |
|  1   | select \* from gaplock where id = 10 for update; |                         -                         |
|  4   |                        -                         |  insert into gaplock value(9,9,9); **un-block**   |
|  4   |                        -                         |  insert into gaplock value(10,10,10); **block**   |
|  3   |                        -                         | insert into gaplock value(12,12,12); **un-block** |
|  5   |                     commit;                      |                         -                         |
|  6   |                        -                         |                   will un-block                   |

- 此时锁区间是 [10， 10]

| 步骤 |                      事务 A                      |                      事务 B                      |
| :--: | :----------------------------------------------: | :----------------------------------------------: |
|  0   |                      begin;                      |                      begin;                      |
|  1   | select \* from gaplock where id > 11 for update; |                        -                         |
|  2   |                        -                         | select \* from gaplock where id = 12 for update; |
|  3   |                        -                         |  insert into gaplock value(12,12,12); **block**  |
|  4   |                        -                         |  insert into gaplock value(20,12,12); **block**  |
|  5   |                     commit;                      |                        -                         |
|  6   |                        -                         |                  will un-block                   |

- 此时锁的是 (10, +∞)
- `select * from gaplock where id > 11 and id < 14 for update;` 会锁 (10, 15]
- `select * from gaplock where id > 11 and id <=15 for update;` 会锁 (10, 15]
- `select * from gaplock where id > 11 and id < 16 for update;`会锁 (10, 15] + (15, 20) = (10, 20)
- `select \* from gaplock where id = 10 for update;`: 此时只会锁 id=10 的记录[退化为行锁]

| 步骤 |                       事务 A                       |                     事务 B                      |
| :--: | :------------------------------------------------: | :---------------------------------------------: |
|  0   |                       begin;                       |                     begin;                      |
|  1   | select \* from gaplock where name = 11 for update; |                        -                        |
|  2   |                         -                          |   insert into gaplock value(2,2,2); **block**   |
|  2   |                         -                          | insert into gaplock value(12,12,12); **block**  |
|  3   |                         -                          | insert into gaplock value(20,12,12); **block**  |
|  3   |                         -                          | insert into gaplock value(222,12,12); **block** |
|  4   |                      commit;                       |                        -                        |
|  5   |                         -                          |                  will un-block                  |

- 此时的区间是锁表了: KEY `IDX_NAME` (`name`) 导致的

| 步骤 |                      事务 A                       |                        事务 B                         |
| :--: | :-----------------------------------------------: | :---------------------------------------------------: |
|  0   |                      begin;                       |                        begin;                         |
|  1   | select \* from gaplock where age = 11 for update; |                           -                           |
|  2   |                         -                         |    insert into gaplock value(2,2,2); **un-block**     |
|  2   |                         -                         |   insert into gaplock value(120,120,10); **block**    |
|  2   |                         -                         |    insert into gaplock value(12,12,12); **block**     |
|  3   |                         -                         |    insert into gaplock value(20,12,12); **block**     |
|  3   |                         -                         |    insert into gaplock value(222,12,12); **block**    |
|  3   |                         -                         | **insert into gaplock value(15,15,15);** **un-block** |
|  3   |                         -                         |   insert into gaplock value(16,16,16); **un-block**   |
|  4   |                      commit;                      |                           -                           |
|  5   |                         -                         |                     will un-block                     |

- 且只锁了 [10, 15) 这个区间 ??

| 步骤 |                      事务 A                       |                        事务 B                         |
| :--: | :-----------------------------------------------: | :---------------------------------------------------: |
|  0   |                      begin;                       |                        begin;                         |
|  1   | select \* from gaplock where age = 10 for update; |                           -                           |
|  2   |                         -                         |    insert into gaplock value(2,2,2); **un-block**     |
|  2   |                         -                         |   insert into gaplock value(1191,1151,5);**block**    |
|  2   |                         -                         |      insert into gaplock value(6,6,6); **block**      |
|  2   |                         -                         |   insert into gaplock value(116,116,10); **block**    |
|  3   |                         -                         |    insert into gaplock value(12,12,12); **block**     |
|  3   |                         -                         | **insert into gaplock value(15,15,15);** **un-block** |
|  3   |                         -                         |   insert into gaplock value(16,16,16); **un-block**   |
|  4   |                      commit;                      |                           -                           |
|  5   |                         -                         |                     will un-block                     |

- 此时锁了 [5~10) + [10~15) = [5~15)

| 步骤 |                      事务 A                       |                       事务 B                       |
| :--: | :-----------------------------------------------: | :------------------------------------------------: |
|  0   |                      begin;                       |                       begin;                       |
|  1   | select \* from gaplock where age > 11 for update; |                         -                          |
|  2   |                         -                         |   insert into gaplock value(2,2,2); **un-block**   |
|  2   |                         -                         |   insert into gaplock value(9,9,9);**un-block**    |
|  3   |                         -                         |   insert into gaplock value(10,10,10); **block**   |
|  3   |                         -                         | **insert into gaplock value(15,15,15);** **block** |
|  3   |                         -                         | insert into gaplock value(160,160,160); **block**  |
|  4   |                      commit;                      |                         -                          |
|  5   |                         -                         |                   will un-block                    |

- 此时锁了 [10~∞)

| 步骤 |                      事务 A                       |                       事务 B                       |
| :--: | :-----------------------------------------------: | :------------------------------------------------: |
|  0   |                      begin;                       |                       begin;                       |
|  1   | select \* from gaplock where age > 10 for update; |                         -                          |
|  2   |                         -                         |   insert into gaplock value(2,2,2); **un-block**   |
|  2   |                         -                         |   insert into gaplock value(9,9,9);**un-block**    |
|  3   |                         -                         |   insert into gaplock value(10,10,10); **block**   |
|  3   |                         -                         | **insert into gaplock value(15,15,15);** **block** |
|  3   |                         -                         | insert into gaplock value(160,160,160); **block**  |
|  4   |                      commit;                      |                         -                          |
|  5   |                         -                         |                   will un-block                    |

- 此时锁了 [10~∞)
