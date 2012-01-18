#!/usr/bin/perl

# install perl module under pfSense with
# 1. setenv PACKAGESITE ftp://ftp-archive.freebsd.org/pub/FreeBSD-Archive/old-releases/i386/8.1-RELEASE/packages/Latest/
# 2. pkg_add -r p5-Net-SMTP-TLS

use Net::SMTP::TLS;

# command line arguments
my $REG_MAC=$ARGV[0];
my $REG_NAME=$ARGV[1];
my $REG_EMAIL=$ARGV[2];
my $REG_IP=$ARGV[3];
my $REG_OWNER=$ARGV[4];
my $REG_HOSTNAME=$ARGV[5];

my $SUBJECT="pfSense: New user: " . $REG_OWNER . " " . $REG_NAME . " " . $REG_EMAIL;
my $BODY=$REG_OWNER . "\n" . $REG_MAC . "\n" . $REG_NAME . "\n" . $REG_EMAIL . "\n" . $REG_IP . "\n". $REG_HOSTNAME . "\n" . $REG_DATE . "\n" . "https://172.16.1.2/pkg.php?xml=freeradius.xml";

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

