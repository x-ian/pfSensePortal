#!/bin/bash

BASEDIR=`dirname $0`
source $BASEDIR/../config.txt

# make sure every session is properly closed to start basically from 0 for up- and downloads
$BASEDIR/../misc/captiveportal-disconnect-all-users.sh

/usr/bin/mysql -u `echo $DR_MYSQL_USER` -p`echo $DR_MYSQL_PASSWD` radius <<EOF
set @today = date_format(now(), '%Y-%m-%d');

-- beginning of day
-- make sure all sessions are ended at midnight
INSERT INTO daily_accounting (username, day, day_beg, inputoctets_day_beg, outputoctets_day_beg)
  SELECT DISTINCT(radacct.username), @today, now(), SUM(radacct.acctinputoctets), SUM(radacct.acctoutputoctets)
  FROM radacct WHERE acctstarttime > @today GROUP BY username
ON DUPLICATE KEY UPDATE username=VALUES(username), day=VALUES(day), day_beg=now(), inputoctets_day_beg=VALUES(inputoctets_day_beg), outputoctets_day_beg=VALUES(outputoctets_day_beg);
EOF
