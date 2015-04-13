#!/usr/bin/perl
use CGI qw(:standard);
use CGI::Carp qw( fatalsToBrowser );
use strict;
use Bio::Seq;
use Bio::Perl;
use Bio::SearchIO;
use Fcntl;
print "Content-type: text/html\n\n";

my $sequence = param("sequence");
$sequence =~ s/[^ATGCatcg]//g;

my $temp_path = "c:/wamp/cgi-bin/temp_files/";
my $blast_bin_path = "c:/wamp/cgi-bin/blast/bin/blastn";
my $common_feats_path = "c:/wamp/cgi-bin/common_features_db/common_features";

#my $temp_path = "usr/lib/cgi-bin/temp_files/";
#my $blast_bin_path = "usr/bin/blastn";
#my $common_feats_path = "usr/lib/cgi-bin/common_features_db/common_features";

my $rand_num = int(rand(1000000000));
my $sequence_obj = Bio::Seq->new(-seq => $sequence, -alphabet => 'dna', -is_circular => 1 );
sysopen(SEQ_FH, $temp_path.$rand_num.".seq", O_RDWR|O_CREAT, 0755) or die "Error creating sequence file. Please try again in a moment."; 
sysopen(QUERY_FH, $temp_path.$rand_num.".blast", O_RDWR|O_CREAT, 0755) or die "Error creating blast file. Please try again in a moment."; 

print SEQ_FH $sequence_obj->seq;
 
my $query_path = $temp_path.$rand_num.".seq";
my $out_path = $temp_path.$rand_num.".blast";
 
system($blast_bin_path, "-query", $query_path, "-db", $common_feats_path, "-out", $out_path); 
 
my $in = new Bio::SearchIO(-format => 'blast', 
                           -file   => $temp_path.$rand_num.'.blast');

while( my $result = $in->next_result ) 
	{
	## $result is a Bio::Search::Result::ResultI compliant object
	while( my $hit = $result->next_hit ) 
		{
		## $hit is a Bio::Search::Hit::HitI compliant object
		while( my $hsp = $hit->next_hsp ) 
			{
			## $hsp is a Bio::Search::HSP::HSPI compliant object
			if ( $hsp->percent_identity >= 98 ) 
				{
				my @cur_res_name = split(/[|]/, $hit->name);
				@cur_res_name = split(/[~]/, $cur_res_name[1]);
				my $hit_ratio = $hsp->length('hit') / $hit->length;
				
				if	($hit_ratio >= 0.95)
					{
					my @cur_res_range = $hsp->range('query');
					my $range;
					
					if ($hsp->strand('hit') == 1)
						{
						$range = $cur_res_range[0]." ".$cur_res_range[1];
						}
					else
						{
						$range = $cur_res_range[1]." ".$cur_res_range[0];
						}
					
					print $cur_res_name[0], " ", $range, " arrow_on Filled ", $cur_res_name[1]," 12\n";
					}
				}
			}  
		}
	}

#Clean up...
close(SEQ_FH);
unlink($temp_path.$rand_num.".seq");
$in->DESTROY;
close(QUERY_FH); 
unlink($temp_path.$rand_num.".blast");
