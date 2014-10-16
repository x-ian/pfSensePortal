#!/bin/bash

# Login and save cookie

BASEDIR=`dirname $0`
source $BASEDIR/../config.txt
PATH=$PATH:/usr/local/bin

curl -c cookies.txt --url "$DR_SERVER/daloradius/dologin.php" --user $DR_HTTP_USER_PASSWD --data "operator_user=$DR_USER&operator_pass=$DR_PASSWD&location=default"
