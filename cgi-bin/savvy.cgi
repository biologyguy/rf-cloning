#!/usr/bin/perl 
#use lib '/usr/lib/cgi-bin';
use SVGPlasmid;
use CGI qw(:standard);
use CGI::Carp qw( fatalsToBrowser );
use strict; 
 


my $plasmid_name	= param("plasmid_name");
my $size		 	= param("plasmid_size");
my $line_thickness	= param("line_thickness");

my $myplasmid = new SVGPlasmid(SIZE =>$size);
print $myplasmid->_init();
print $myplasmid->_draw_plasmid_backbone(
						NAME =>$plasmid_name,
						STROKE=>$line_thickness
						);

#########ENZYMES#################
sub transpose {
  map {
    my $j = $_;
    [ map $_[$_][$j], 0..$#_ ]
  } 0..$#{$_[0]};
}


my $enzymes	=	param("enzymes");
if ($enzymes)
	{
	my (@n, @p);
	my @multi_array;
	my @pairs	=	split(":", $enzymes);

	for(my $i=0; $i<@pairs ; $i++)
		{
		(@n[$i], @p[$i]) = split(" ", $pairs[$i]);
		$multi_array[$i][0] = $p[$i]; 
		$multi_array[$i][1] = $n[$i]; 
		}
	
	my @sorted = sort { $a->[0] <=> $b->[0] } @multi_array;	
	
	for(my $i=0; $i<@pairs ; $i++)
		{
		@n[$i] =  $sorted[$i][1];
		@p[$i] = $sorted[$i][0];
		}
	
	$myplasmid->_draw_enzymes(\@n, \@p);
	}
############## MCS ##############
my $mcs = param ("mcs");
if ($mcs)
	{
	my @a = split(",",$mcs);
	print $myplasmid->_draw_mcs(@a);
	}

######## Markers###########

my $markers			= param("markers"); #Get Input
if ($markers){
	my @marker_lines	= split("\n", $markers); #Split to lines

	foreach my $marker_lines(@marker_lines){
		my ($name, $start, $end, $arrow, $style, $color, $thickness)
			= split(" ", $marker_lines);
		print $myplasmid->_draw_markers(
			NAME		=>	$name, 
			START		=>	$start, 
			END			=>	$end, 
			ARROW		=> 	$arrow,
			STYLE		=>	$style,
			COLOR		=>	$color,
			THICKNESS	=>	$thickness);

	}
}
######## END MARKERS #######

print $myplasmid->_end();
