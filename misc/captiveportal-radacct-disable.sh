#!/bin/bash

# disable radacct of captive portal

# https://doc.pfsense.org/index.php/Using_the_PHP_pfSense_Shell
# https://forum.pfsense.org/index.php?topic=56956.0

BASEDIR=.. #/home/pfSensePortal

# get pfSense login details
source $BASEDIR/config.txt

# disconnect users while radacct is still enable to ensure accounting stop packets are sent
$BASEDIR/misc/captiveportal-disconnect-all-users.sh

# disable accounting
tee disable.pfSsh <<EOF
parse_config(true);
unset(\$config["captiveportal"]["$ZONE"]["radacct_enable"]);
write_config();
exec
exit
EOF

/usr/local/sbin/pfSsh.php < disable.pfSsh
rm disable.pfSsh

# make sure really all users are disconnected and no one 'sneaked in' 
$BASEDIR/misc/captiveportal-disconnect-all-users.sh
