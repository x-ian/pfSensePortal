#!/usr/local/bin/bash

/usr/local/sbin/squid -k shutdown
/bin/sleep 10
/bin/rm -rf /var/squid/cache/*
/usr/local/sbin/squid -z

