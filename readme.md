Xross Entity Map (XEM)
======================

XEM is a open and crowd sourced method of providing mapping of different tv scraper data.

Many websites use different numberings, names and season titles for the same show.
XEM lets you create a map for an easy overview of all the different counting and naming systems.

In action here http://thexem.info/


## Resolving Dependencies

### PHP 5.x
Only PHP 5.x is supported at this time due to legacy codeigniter used.

* php 5.4+ -- http://php.net/manual/en/ini.core.php#ini.short-open-tag

> short_open_tag in php.ini is now default to Off, which breaks xem. Change this back to On to fix.

**FIX:** `short_open_tag = On`


### mySQL (>8 and <8.0.33)

#### Database Access

In MySQL 8.0 the *caching_sha2_password* is the default authentication plugin rather than *mysql_native_password*, which is the default method in MySQL 5.7 and prior.
This project uses codeigniter 2.x which does not supprt this newer auth, so you must add a user with the legacy auth for it to work.

If you see this sort of error, it is because mssqli driver <> db is broken due to the auth:
```
    Message: mysqli_real_escape_string() expects parameter 1 to be mysqli, boolean given
    Filename: mysqli/mysqli_driver.php
    Line Number: 316
```

As of MySQL **8.0.34**, the mysql_native_password authentication plugin is deprecated and subject to removal in a future version of MySQL.

`CREATE USER 'nativeuser'@'localhost'IDENTIFIED WITH mysql_native_password BY 'password';`
or
`ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'password';`


#### Database mode

We do not want `STRICT_TRANS_TABLES` or `ONLY_FULL_GROUP_BY` enabled as it can cause the search feature or drafts to not work.

* mysql 5.7.5+ -- http://mysqlserverteam.com/mysql-5-7-only_full_group_by-improved-recognizing-functional-dependencies-enabled-by-default/

> If you see that the search box returns 'no shows found' when you know thats a lie.. its probably due to "sql_mode=only_full_group_by"

**FIX:** `SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));`

---

* mysql 5.7+ -- https://dev.mysql.com/doc/refman/5.7/en/sql-mode.html#sqlmode_strict_trans_tables

> If you see that creating a draft does not work, it's most likely due to the fact the backend isnt able to create a draft due to invalid sql.

**FIX:** `SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'STRICT_TRANS_TABLES',''));`


#### Database my.cnf

Rather than doing temp work arounds for the current db session, it is best to just set the workarounds in the server global config and resart. 
Ideally before you do the inital setup.


#### mysql >8:
Already has sensible unicode defaults, just need to fixup auth and modes.
```
[mysqld]
default_authentication_plugin = mysql_native_password
sql_mode = "NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION"
```

#### mysql 5.7:
Legacy mysql had not great unicode defaults which can lead to issues, to prevent we use newer/expanded ascii set.
```
[mysqld]
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci
sql_mode = "NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"
```
