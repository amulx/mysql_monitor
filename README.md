### 项目截图

![mysql_audit](mysql_audit.png)![mysql_audit](file://C:\Users\Administrator\Desktop\mysql_audit.png)

### 项目原理

mysql中监控的主要原理是开启mysql的general_log来记录mysql的历史执行语句，它有两种记录方式，默认是通过记录到文件方式，另外一种是通过直接记录到mysql库的general_log表中，为了更方便的查询，我选择的是记录到mysql数据库的方式。
