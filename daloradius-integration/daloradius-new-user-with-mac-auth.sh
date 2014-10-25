#!/bin/bash

# Add new user with MAC Auth to DR

BASEDIR=`dirname $0`
source $BASEDIR/../config.txt
PATH=$PATH:/usr/local/bin

MAC=`echo $1 | sed -f $BASEDIR/urlencode.sed`
FIRSTNAME=`echo $2 | sed -f $BASEDIR/urlencode.sed`
LASTNAME=`echo $3 | sed -f $BASEDIR/urlencode.sed`
EMAIL=`echo $4 | sed -f $BASEDIR/urlencode.sed`
COMPANY=`echo $5 | sed -f $BASEDIR/urlencode.sed`
GROUP="$6" #`echo $6 | sed -f $BASEDIR/urlencode.sed`
IP=`echo $7 | sed -f $BASEDIR/urlencode.sed`
HOSTNAME=`echo $8 | sed -f $BASEDIR/urlencode.sed`
MAC_VENDOR=`echo $9 | sed -f $BASEDIR/urlencode.sed`

$BASEDIR/daloradius-login.sh

curl -b cookies.txt --url "$DR_SERVER/daloradius/mng-new.php" --user $DR_HTTP_USER_PASSWD --data "authType=macAuth&macaddress=$MAC&group_macaddress%5B%5D=$GROUP&firstname=$FIRSTNAME&lastname=$LASTNAME&email=$EMAIL&department=&company=$COMPANY&workphone=&homephone=&mobilephone=&address=$HOSTNAME&city=$MAC_VENDOR&state=&country=&zip=&notes=$IP&portalLoginPassword=&submit=Apply&bi_contactperson=&bi_company=&bi_email=&bi_phone=&bi_address=&bi_city=&bi_state=&bi_country=&bi_zip=&bi_postalinvoice=&bi_faxinvoice=&bi_emailinvoice=&bi_paymentmethod=&bi_cash=&bi_creditcardname=&bi_creditcardnumber=&bi_creditcardverification=&bi_creditcardtype=&bi_creditcardexp=&bi_lead=&bi_coupon=&bi_ordertaker=&bi_notes=&bi_billdue=&bi_nextinvoicedue="

rm cookies.txt
