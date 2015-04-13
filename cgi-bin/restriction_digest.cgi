#!/usr/bin/perl
use CGI qw(:standard);
use CGI::Carp qw( fatalsToBrowser );
use strict; 

print "Content-type: text/html\n\n";

my @RESTRICTION_SITES = ("AatII:GACGTC", "AbsI:CCTCGAGG", "Acc65I:GGTACC", "AciI:CCGC", "AclI:AACGTT", "AfeI:AGCGCT", "AflII:CTTAAG", "AgeI:ACCGGT", "AluI:AGCT", "ApaI:GGGCCC", "ApaLI:GTGCAC", "AscI:GGCGCGCC", "AseI:ATTAAT", "AsiSI:GCGATCGC", "AvrII:CCTAGG", "BamHI:GGATCC", "BbvCI:CCTCAGC", "BclI:TGATCA", "BfaI:CTAG", "BfuCI:GATC", "BglII:AGATCT", "BmgBI:CACGTC", "BmtI:GCTAGC", "BseYI:CCCAGC", "BsiWI:CGTACG", "BspDI:ATCGAT", "BspEI:TCCGGA", "BspHI:TCATGA", "BsrBI:CCGCTC", "BsrGI:TGTACA", "BssHII:GCGCGC", "BssSI:CACGAG", "BstBI:TTCGAA", "BstUI:CGCG", "BstZ17I:GTATAC", "ClaI:ATCGAT", "CviAII:CATG", "CviQI:GTAC", "DpnI:GATC", "DpnII:GATC", "DraI:TTTAAA", "EagI:CGGCCG", "Eco53kI:GAGCTC", "EcoRI:GAATTC", "EcoRV:GATATC", "FatI:CATG", "FseI:GGCCGGCC", "FspI:TGCGCA", "HaeIII:GGCC", "HhaI:GCGC", "HinP1I:GCGC", "HindIII:AAGCTT", "HpaI:GTTAAC", "HpaII:CCGG", "HpyCH4IV:ACGT", "HpyCH4V:TGCA", "KasI:GGCGCC", "KpnI:GGTACC", "MauBI:CGCGCGCG", "MboI:GATC", "MfeI:CAATTG", "MluI:ACGCGT", "MreI:CGCCGGCG", "MscI:TGGCCA", "MseI:TTAA", "MspI:CCGG", "NaeI:GCCGGC", "NarI:GGCGCC", "NcoI:CCATGG", "NdeI:CATATG", "NgoMIV:GCCGGC", "NheI:GCTAGC", "NlaIII:CATG", "NotI:GCGGCCGC", "NruI:TCGCGA", "NsiI:ATGCAT", "PacI:TTAATTAA", "PaeR7I:CTCGAG", "PciI:ACATGT", "PhoI:GGCC", "PmeI:GTTTAAAC", "PmlI:CACGTG", "PsiI:TTATAA", "PspOMI:GGGCCC", "PstI:CTGCAG", "PvuI:CGATCG", "PvuII:CAGCTG", "RsaI:GTAC", "SacI:GAGCTC", "SacII:CCGCGG", "SalI:GTCGAC", "Sau3AI:GATC", "SbfI:CCTGCAGG", "ScaI:AGTACT", "SfoI:GGCGCC", "SgrDI:CGTCGACG", "SmaI:CCCGGG", "SnaBI:TACGTA", "SpeI:ACTAGT", "SphI:GCATGC", "SrfI:GCCCGGGC", "SspI:AATATT", "StuI:AGGCCT", "SwaI:ATTTAAAT", "TaqI:TCGA", "TliI:CTCGAG", "Tsp509I:AATT", "TspMI:CCCGGG", "XbaI:TCTAGA", "XhoI:CTCGAG", "XmaI:CCCGGG", "ZraI:GACGTC");

my $sequence = param("sequence");
my $cut_num = param("cut_num");

$sequence =~ s/[^ATGCatcg]//g;
my $counter = 0;
my @enzyme_list_array;
my $enzyme_list;
my @cur_enzyme;
my $offset = 0;
my $position;

foreach(@RESTRICTION_SITES)
	{
	my $check_num = 0;
	my @position_array;
	@cur_enzyme = split(":",$_);
	$offset = 0;
	$position = index($sequence,$cur_enzyme[1],$offset);

	if ($cut_num == 1) #--1 cutters only--#
		{
		while ($position != (-1) && $check_num < 2)
			{
			$position_array[$check_num][0] = $cur_enzyme[0];
			$position_array[$check_num][1] = $position;
			$offset = $position + length($cur_enzyme[0]);	
			$position = index($sequence,$cur_enzyme[1],$offset);
			$check_num++;
			}
		
		if($check_num == 1)
			{			
			$enzyme_list_array[$counter][0] = $position_array[0][0];
			$enzyme_list_array[$counter][1] = $position_array[0][1];
			$counter++;
			}
		}
			
	elsif ($cut_num == 2) #--1 and 2 cutters--#
		{
		while ($position != (-1) && $check_num < 3)
			{
			$position_array[$check_num][0] = $cur_enzyme[0];
			$position_array[$check_num][1] = $position;
			$offset = $position + length($cur_enzyme[0]);	
			$position = index($sequence,$cur_enzyme[1],$offset);
			$check_num++;
			}
		
		if($check_num <= 2)
			{			
			for (my $i = 0; $i < $check_num; $i++)
				{
				$enzyme_list_array[$counter][0] = $position_array[$i][0];
				$enzyme_list_array[$counter][1] = $position_array[$i][1];
				$counter++;
				}
			}
		}
		
	elsif ($cut_num == 3) #--1, 2 and 3 cutters--#
		{
		while ($position != (-1) && $check_num < 4)
			{
			$position_array[$check_num][0] = $cur_enzyme[0];
			$position_array[$check_num][1] = $position;
			$offset = $position + length($cur_enzyme[0]);	
			$position = index($sequence,$cur_enzyme[1],$offset);
			$check_num++;
			}
		
		if($check_num <= 3)
			{			
			for (my $i = 0; $i < $check_num; $i++)
				{
				$enzyme_list_array[$counter][0] = $position_array[$i][0];
				$enzyme_list_array[$counter][1] = $position_array[$i][1];
				$counter++;
				}
			}
		}
		
	elsif ($cut_num == 4) #--All restriction sites--#
		{
		
		while ($position != (-1))
			{			
			$enzyme_list_array[$counter][0] = $cur_enzyme[0];
			$enzyme_list_array[$counter][1] = $position;
			
			$offset = $position + length($cur_enzyme[0]);	
			$position = index($sequence,$cur_enzyme[1],$offset);
			$counter++;
			}
		}
	}

@enzyme_list_array = sort { $a->[1] <=> $b->[1] } @enzyme_list_array;

for(my $i = 0; $i < $counter; $i++)
	{
	$enzyme_list .= $enzyme_list_array[$i][0]." ".$enzyme_list_array[$i][1].":";	
	}

print $enzyme_list;