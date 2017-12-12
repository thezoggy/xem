Xross Entity Map (XEM)
======================

XEM is a open and crowd sourced method of providing mapping of different tv scraper data.

Many websites use different numberings, names and season titles for the same show.
XEM lets you create a map for an easy overview of all the different counting and naming systems.

In action here http://thexem.de/


## Resolving Dependencies

- mysql 5.7.5+ -- http://mysqlserverteam.com/mysql-5-7-only_full_group_by-improved-recognizing-functional-dependencies-enabled-by-default/
If you see that the search box returns 'no shows found' when you know thats a lie.. its probably due to "sql_mode=only_full_group_by"
FIX: `SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));`

- php 5.4+ -- http://php.net/manual/en/ini.core.php#ini.short-open-tag
short_open_tag in php.ini is now default to Off, which breaks xem. Change this back to On to fix.
FIX: `short_open_tag = On`

