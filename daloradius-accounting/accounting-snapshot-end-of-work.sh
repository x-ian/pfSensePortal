#!/bin/bash

BASEDIR=`dirname $0`
source $BASEDIR/../config.txt

/usr/bin/mysql -u `echo $DR_MYSQL_USER` -p`echo $DR_MYSQL_PASSWD` radius <<EOF
set @today = date_format(now(), '%Y-%m-%d');

-- end of working hours
INSERT INTO daily_accounting (username, day, work_end, inputoctets_work_end, outputoctets_work_end)
  SELECT DISTINCT(radacct.username), @today, now(), SUM(radacct.acctinputoctets), SUM(radacct.acctoutputoctets)
  FROM radacct WHERE acctstarttime > @today GROUP BY username
ON DUPLICATE KEY UPDATE username=VALUES(username), day=VALUES(day), work_end=now(), inputoctets_work_end=VALUES(inputoctets_work_end), outputoctets_work_end=VALUES(outputoctets_work_end);
EOF
