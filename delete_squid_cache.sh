#!/usr/local/bin/bash

/usr/local/sbin/squid -k shutdown
/bin/sleep 60
/bin/rm -rf /var/squid/cache/*
/bin/sleep 60
/usr/local/sbin/squid -z

