#!/bin/bash

# takes the radius account information to summaries this in a complete csv file
# requires http://www.tummy.com/Community/software/radiuscontext/

TODAY=`date +%Y-%m-%d`
ACCOUNTING_CSV=accounting_`echo $TODAY`.csv
ACCOUNTING_DIR=accountingdir
RADIUS_DIR=radiusdir
REPORT_DIR=reportdir
#PFSENSE_CONFIG=/conf/config.xml
PFSENSE_CONFIG=./config.xml
MAX_DAYS_BACK=7

# --------------------------------------
# invoke radiusContext for last 7 detail file
# todo, make incremental updates
for RADIUS_DETAIL in `ls -r -1 $RADIUS_DIR | head -n $MAX_DAYS_BACK`; do
  ./radiusContext-1.93/raddetail $RADIUS_DIR/$RADIUS_DETAIL
  ./radiusContext-1.93/stdreport --generate csv -g csv --reportdir $REPORT_DIR
  mv $REPORT_DIR/index.csv $ACCOUNTING_DIR/index_$RADIUS_DETAIL.csv
  # todo, cleanup of radiusContext files, there should be a better way but i didn't look for one
  rm -rf $REPORT_DIR/*
  rm -f SessionData.db
done

# --------------------------------------
# get all ever registered mac addresses and users
grep "<username>" $PFSENSE_CONFIG | sed  "s/<username>//g" | sed "s/<\/username>//g" | tr -d "(" | tr -d ")" | tr -d " " | tr -d "\t" >mac_addresses.txt

# --------------------------------------
# process all radiuscontext csv files to get the daily values for every mac address
DAYS_AGO=0
for DAILY_RADIUSCONTEXT_CSV in `ls -r $ACCOUNTING_DIR`; do
  if [ $DAYS_AGO -eq $MAX_DAYS_BACK ]; then
	break
  fi
  let DAYS_AGO+=1
  for MAC in `cat mac_addresses.txt`; do 
    # index 6 is in
    IN=`grep $MAC $ACCOUNTING_DIR/$DAILY_RADIUSCONTEXT_CSV | cut -d, -f6`
    # index 7 is out
    OUT=`grep $MAC $ACCOUNTING_DIR/$DAILY_RADIUSCONTEXT_CSV | cut -d, -f7`

    # explicitly set to 0 if empty
    if [ -z "$IN" ]; then
	  IN=0
	fi
    if [ -z "$OUT" ]; then
	  OUT=0
	fi

    # todo, save this in a clever way as bash doesn't provide multi dimensional arrays
    eval A_${MAC}_${DAYS_AGO}_IN='$IN'
    eval A_${MAC}_${DAYS_AGO}_OUT='$OUT'
  done
done

# --------------------------------------
# export to complete data set to one csv
# create header
HEADER="MAC,MAC_DETAILS"
for (( i=1; i <=$MAX_DAYS_AGO; i++ )); do
  HEADER="`echo $HEADER`,OUT -$i days"
done
HEADER="`echo $HEADER`,separator"
for (( i=1; i <=$MAX_DAYS_AGO; i++ )); do
  HEADER="`echo $HEADER`,IN -$i days"
done
echo $HEADER >$ACCOUNTING_CSV
# create content
for MAC in `cat mac_addresses.txt`; do 
  # MAC_DETAILS are specific to APZUs registration process and may or may not be available
  MAC_DETAILS=`grep -A 10 $MAC $PFSENSE_CONFIG | grep description | sed -e "s/<description><\!\[CDATA\[//g" | sed -e "s/\]\]><\/description>//g" | tr -d "\t" | tr -d " "`
  LINE="$MAC,$MAC_DETAILS"
  for (( i=1; i <=$MAX_DAYS_AGO; i++ )); do
	OUT=$(eval echo \$A_${MAC}_${i}_OUT)
	LINE=`echo $LINE,$OUT`
  done
  LINE="`echo $LINE`,separator"
  for (( i=1; i <=$MAX_DAYS_AGO; i++ )); do
	IN=$(eval echo \$A_${MAC}_${i}_IN)
	LINE=`echo $LINE,$IN`
#	eval echo \$${MAC}_${DAYS_AGO}_IN
#	eval echo \$${MAC}_${DAYS_AGO}_OUT
  done
  echo $LINE >> $ACCOUNTING_CSV
done

rm mac_addresses.txt

