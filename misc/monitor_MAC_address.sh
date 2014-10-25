#!/usr/local/bin/bash

#MAC="00:25:00:48:60:10"
MAC="b8:e8:56:03:99:d4"

if [ $? -eq 0 ]; then
  BASEDIR=/home/pfSensePortal
  SUBJECT="MAC found"
  BODY="SYSTEM ALIVE"
  /usr/bin/perl -I /usr/local/lib/perl5/site_perl/5.10.1/ -I   /usr/local/lib/perl5/site_perl/5.10.1/mach send_gmail.perl "$SUBJECT" "$BODY"

  echo "system active "
else
  echo "not active"
fi