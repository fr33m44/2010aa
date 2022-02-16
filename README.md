# 小吞AA制记账系统

![小吞AA制记账系统](https://github.com/fr33m44/aa/blob/master/screenshot-aa.png ''小吞AA制记账系统'')

小吞AA制记账系统 是2010年10月写的个人财务管理系统，采用php5+mysql5，基于smarty模板引擎。那个时候和同学合租，因为买菜之类的需要AA，就需要报账，所以需求出来了，就写了一个。可以添加、删除、修改，有公费、私费类别，公费可以结算，私费可以算自己的小帐。可以按比例结算，比如4：6的比例来结算。

## 安装

比如下载到目录c:\www\aa\，用PMA新建数据库，比如数据名字是expense，然后导入d:\www\aa\expense.sql，修改d:\www\aa\data\config.php 设置你的mysql数据库的用户名、密码、数据库名信息 就可以使用了。

默认的测试数据里面帐号用户名/密码有：admin/admin cc/cc sj/sj lgy/lgy hk/hk ，密码是 md5(“用户名”) ，你可以自行修改user表来管理用户。

