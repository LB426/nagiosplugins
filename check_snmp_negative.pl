#! /usr/bin/perl
use strict;
use warnings;

use Net::SNMP;

my $host = shift;
my $comm = shift || 'JSC_Kes_PubLiC';
my $oid = shift || '1.3.6.1.4.1.9.2.2.1.1.6.10201';
my ($session, $error) = Net::SNMP->session(-hostname => $host, -community => $comm,);
if (!defined $session){ printf "ERROR: %s.\n", $error; exit 1;}
my $result = $session->get_request(-varbindlist => [ $oid ],);
if (!defined $result){ printf "ERROR: %s.\n", $session->error(); $session->close();	exit 1;}
my $x = $result->{$oid} ;
#printf "The OID for host '%s' is %s.\n",$session->hostname(), $x;
if($x < 0){
	$x = 4294967295 + $x ;
}
printf "SNMP OK - %s | iso.%s=%s\n",$x,$oid,$x;
# SNMP OK - -1951489296 | iso.3.6.1.4.1.9.2.2.1.1.6.10201=-1951489296
$session->close();
exit(0);
