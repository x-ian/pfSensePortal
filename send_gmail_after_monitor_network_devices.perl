#!/usr/bin/perl

# install perl module under pfSense with
# 1. setenv PACKAGESITE ftp://ftp-archive.freebsd.org/pub/FreeBSD-Archive/old-releases/i386/8.1-RELEASE/packages/Latest/
# 2. pkg_add -r p5-Net-SMTP-TLS

use Net::SMTP::TLS;

# command line arguments
my $DATETIME=$ARGV[0];

my $SUBJECT="172.16.1.2 - pfSense: Internal network devices check : " . $DATETIME;
my $BODY=$ARGV[1];

my $SENDER='mail@apzu.pih.org';
my $RECEIVER='apzu-it@apzu.pih.org';

#my $PASSWORD='changeme';
open FILE, "</home/pfSensePortal/send_gmail_config.txt";
$PASSWORD = do { local $/; <FILE> };

my $SMTP='smtp.gmail.com';
my $HELLO='smtp.gmail.com';
my $PORT=587;

my $mailer = new Net::SMTP::TLS(
  $SMTP,
  Hello =>$HELLO,
  Port=>$PORT,
  User=>$SENDER,
  Password=>$PASSWORD);
$mailer->mail($SENDER);
$mailer->to($RECEIVER);
$mailer->data;
$mailer->datasend("From: " . $SENDER . "\n");
$mailer->datasend("To: " . $RECEIVER . "\n");
$mailer->datasend("Subject: " . $SUBJECT . "\n");
$mailer->datasend("\n");
$mailer->datasend($BODY);
$mailer->dataend;
$mailer->quit;

