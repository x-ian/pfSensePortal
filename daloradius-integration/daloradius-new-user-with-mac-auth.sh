#!/bin/bash

# Add new user with MAC Auth to DR

BASEDIR=`dirname $0`
source $BASEDIR/../config.txt
PATH=$PATH:/usr/local/bin

MAC=$1
FIRSTNAME=$2
LASTNAME=$3
EMAIL=$4
COMPANY=$5
GROUP=$6 # e.g. APZUnet+user
NOTES=$7

$BASEDIR/daloradius-login.sh

curl -b cookies.txt --url $DR_SERVER/daloradius/mng-new.php" --user $DR_HTTP_USER_PASSWD --data "authType=macAuth&macaddress=$MAC&group_macaddress%5B%5D=$GROUP&firstname=$FIRSTNAME&lastname=$LASTNAME&email=$EMAIL&department=&company=$COMPANY&workphone=&homephone=&mobilephone=&address=&city=&state=&country=&zip=&notes=$NOTES&portalLoginPassword=&submit=Apply&bi_contactperson=&bi_company=&bi_email=&bi_phone=&bi_address=&bi_city=&bi_state=&bi_country=&bi_zip=&bi_postalinvoice=&bi_faxinvoice=&bi_emailinvoice=&bi_paymentmethod=&bi_cash=&bi_creditcardname=&bi_creditcardnumber=&bi_creditcardverification=&bi_creditcardtype=&bi_creditcardexp=&bi_lead=&bi_coupon=&bi_ordertaker=&bi_notes=&bi_billdue=&bi_nextinvoicedue="

rm cookies.txt