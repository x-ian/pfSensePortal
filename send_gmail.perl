#!/usr/bin/perl

# install perl module under pfSense with
# 1. setenv PACKAGESITE ftp://ftp-archive.freebsd.org/pub/FreeBSD-Archive/old-releases/i386/8.1-RELEASE/packages/Latest/
# 2. pkg_add -r p5-Net-SMTP-TLS

use Net::SMTP::TLS;

my $SUBJECT=$ARGV[0];
my $BODY=$ARGV[1];
my $SENDER=$ARGV[2];
my $PASSWORD=$ARGV[3];
my $RECEIVER=$ARGV[4];

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

