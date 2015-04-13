#!/usr/bin/perl
use CGI qw(:standard);
use CGI::Carp qw( fatalsToBrowser );
use strict;
use Bio::Seq;
use Bio::Perl;
use Bio::SearchIO;
use Fcntl;

delete @ENV{qw(IFS CDPATH ENV BASH_ENV)}; # Make %ENV safer

print "Content-type: text/html\n\n";
	
my $plasmid_seq = param("plasmid_seq");
$plasmid_seq =~ s/[^ATGCatcg]//g;

my $sequencing_seq = param("sequencing_seq");
$sequencing_seq =~ s/[^ATGCatcg]//g;

my $temp_path = "c:/wamp/cgi-bin/temp_files/";
my $blast_bin_path = "c:/wamp/cgi-bin/blast/bin/blastn";

#my $temp_path = "/usr/lib/cgi-bin/temp_files/";
#my $blast_bin_path = "/usr/bin/blastn";

my $rand_num = int(rand(1000000000));
my $plasmid_obj = Bio::Seq->new(-seq => $plasmid_seq, -alphabet => 'dna', -is_circular => 1 );
my $sequencing_obj = Bio::Seq->new(-seq => $sequencing_seq, -alphabet => 'dna', -is_circular => 0 );

sysopen(PLAS_FH, $temp_path.$rand_num."_plasmid.seq", O_RDWR|O_CREAT, 0755) or die "Error creating plasmid file. Please try again in a moment."; 
sysopen(SEQ_FH, $temp_path.$rand_num."_sequence.seq", O_RDWR|O_CREAT, 0755) or die "Error creating sequence file. Please try again in a moment."; 
sysopen(BLAST_FH, $temp_path.$rand_num.".blast", O_RDWR|O_CREAT, 0755) or die "Error creating blast file. Please try again in a moment."; 

print PLAS_FH ">".param("proj_name")."\n".$plasmid_obj->seq;
print SEQ_FH ">Sequencing result\n".$sequencing_obj->seq;

my $sequence_path = $temp_path.$rand_num."_sequence.seq";
my $plasmid_path = $temp_path.$rand_num."_plasmid.seq";
my $out_path = $temp_path.$rand_num.".blast";
 
system($blast_bin_path, "-query", $sequence_path, "-subject", $plasmid_path, "-out", $out_path, "-html"); 

my @file_conts = <BLAST_FH>;
foreach(@file_conts)
	{
	print $_;
	} 

#Clean up...
close(SEQ_FH);
unlink($temp_path.$rand_num."_sequence.seq");
close(PLAS_FH);
unlink($temp_path.$rand_num."_plasmid.seq");
close(BLAST_FH); 
unlink($temp_path.$rand_num.".blast");