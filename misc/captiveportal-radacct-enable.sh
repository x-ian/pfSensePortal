#!/bin/bash

# enable radacct of captive portal

# https://doc.pfsense.org/index.php/Using_the_PHP_pfSense_Shell
# https://forum.pfsense.org/index.php?topic=56956.0

BASEDIR=.. #/home/pfSensePortal

# get pfSense login details
source $BASEDIR/config.txt

# enable accounting
tee enable.pfSsh <<EOF
parse_config(true);
\$config["captiveportal"]["$ZONE"]["radacct_enable"] = true;
write_config();
exec
exit
EOF

/usr/local/sbin/pfSsh.php < disable.pfSsh
rm enable.pfSsh

$BASEDIR/misc/captiveportal-disconnect-all-users.sh
