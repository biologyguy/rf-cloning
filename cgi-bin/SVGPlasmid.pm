use CGI::Carp qw(fatalsToBrowser);
package SVGPlasmid; 
##########################################################################
# CLASS      SVGPlasmid
# VERSION    0.0.1
# DATE       1/23/01
# AUTHOR     Malay
# EMAIL      curiouser@ccmb.ap.nic.in
# PURPOSE 	 To draw and send a SVG plamid map to client   

=head1 NAME

SVGPlasmid

=head1 AUTHOR

Malay
curiouser@ccmb.ap.nic.in
CCMB

=head1 VERSION

Version 0.0.1
1/23/01

=head1 SYNOPSIS

Todo: 

=cut


##########################################################################
# INCLUDED LIBRARY FILES
use strict;


##########################################################################
# CONSTANTS AND GLOBAL VARIABLES
# Todo: Define your constants here. Don't use global variables if you can
#       stay away from them, but declare them here as well. All variables
#       should be declared as "my" variables and given a default value.

##########################################################################
# FUNCTION DEFINITIONS

#-------------------------------------------------------------------------
# FUNCTION   "new" pseudo-keyword constructor for class
# RECEIVES   none
# RETURNS    The blessed thingy
# EXPECTS    none
# SETS       none
# DOES       Creates, blesses, and initializes the class member
sub new {
	my $class = shift;
	my $self = {};

	bless ($self, $class);
	%$self = @_;
	$SVGPlasmid::SIZE 		= 	$self->{SIZE};
	$SVGPlasmid::X_COORD 	= 	250;
	$SVGPlasmid::Y_COORD	=	250;
	$SVGPlasmid::RADIUS		=	150;
	$SVGPlasmid::WIDTH		=	800;
	$SVGPlasmid::HEIGHT		=	600;
	return $self;

}

#-------------------------------------------------------------------------
# FUNCTION   _init
# RECEIVES   Class member and whatever arguments go into the {}
# RETURNS    the class member
# EXPECTS    none
# SETS       All the defaults stuff
# DOES       Initializes the class member

sub _init { 
	my $self = shift;
	
	my $head =	qq	(Content-type: image/svg+xml\n\n);
	$head 	.= 	qq	(<?xml version='1.0' encoding='UTF-8'?>\n);
	$head 	.= 	qq	(<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 20001102//EN"
  "http://www.w3.org/TR/2000/CR-SVG-20001102/DTD/svg-20001102.dtd">\n);
#	$head	.=	qq	(<defs>);
#	$head 	.= 	qq	(<style type="text/css">\n);
#	$head 	.= 	qq	(<![CDATA[\n);
#	$head 	.= 	qq	(.backbone{fill:red;});
#	$head 	.= 	qq	(]]>\n);
#	$head 	.= 	qq	(</style>\n);
#	$head	.=	qq	(</defs>);	
	$head 	.= 	qq	(<svg xmlns="http://www.w3.org/2000/svg" width="$SVGPlasmid::WIDTH" height="$SVGPlasmid::HEIGHT">\n);
print $head;
return '';
}

#--------------------------------------------------------------------------
# FUNCTION	 	_draw_plasmid
# RECEIVES 		Nothing
# RETURNS		Nothing
# DOES			Draws each members
sub _draw_plasmid_backbone {
	my $self = shift;
	my $y	 = $SVGPlasmid::Y_COORD + 15;

	
	%$self = @_;
	my $PLASMID_NAME 	= 	$self->{NAME};
	my $STROKE			=	$self->{STROKE};	
	print qq(<circle fill="none" stroke="black" stroke-width="$STROKE" cx="$SVGPlasmid::X_COORD" cy="$SVGPlasmid::Y_COORD" r="$SVGPlasmid::RADIUS" />\n);
	print qq(<text x="$SVGPlasmid::X_COORD" y="$SVGPlasmid::Y_COORD" text-anchor="middle" font-size="12px" font-weight="bold" font-family="Arial">$PLASMID_NAME</text>\n);
	print qq(<text x="$SVGPlasmid::X_COORD" y="$y" text-anchor="middle" font-size="10px" font-family="Arial">$SVGPlasmid::SIZE bp</text>\n);
	return '';
	}

#--------------------------------------------------------------------------
sub _get_cord{
	my ($radius, $radian) = @_;
	my $x = $SVGPlasmid::X_COORD + ($radius * sin($radian));
	my $y = $SVGPlasmid::Y_COORD - ($radius * cos($radian));
	return $x,$y;
}

#--------------------------------------------------------------------------
sub _get_radian{
	my ($size, $position) = @_;
	my $PI = 4 * atan2(1,1); #Get the value of PI
	return ( (360/$size) * $position ) * ($PI / 180);
}

#--------------------------------------------------------------------------
sub _draw_markers {
	my $self = shift;
  	%$self = @_;
  	my $PI = 4 * atan2(1,1); #Get the value of PI
  	my $PLASMID_SIZE 	= 	$SVGPlasmid::SIZE;
  	my $RADIUS			=	$SVGPlasmid::RADIUS;
	my $INVERSE_FLAG 	= 	undef;
  	my $start_position 	= 	$self->{START};
	my $arrow_end		= 	$self->{END};
	my $end_position  	=	undef;
	my $THICKNESS 		= 	$self->{THICKNESS};
  	my $HALF_THICKNESS	= 	$self->{THICKNESS} / 2 ;
	my $MARKER_NAME		=	$self->{NAME};
	my $COLOR			=	$self->{COLOR};
	my $STYLE			=	$self->{STYLE};
	my $ARROW			=	$self->{ARROW};
	my $INNER_RADIUS	= 	$RADIUS - 	$HALF_THICKNESS;
	my $OUTER_RADIUS	=	$RADIUS	+	$HALF_THICKNESS;
	my $MID_POINT		= 	undef;
	my $MID_X			=	undef;
	my $MID_Y			=	undef;
	my $TEXT_RADIUS		= 	$RADIUS -($HALF_THICKNESS + 10);
	my $TEXT_ALIGN		= 	undef;
	my $ARROW_HEAD_THICKNESS = 0.05 * ( $PLASMID_SIZE /(2 * $PI)  );
	#my $ARROW_HEAD_THICKNESS = 15;
	#If style = open arrow set color to white
	if ($STYLE eq "Open"){
		$COLOR = "white";
	}

	if ($self->{START} > $self->{END}){
  		$INVERSE_FLAG 	= 	1;
		$MID_POINT		= 	(($start_position - $arrow_end) / 2) + $arrow_end;
		$end_position	=	$arrow_end + $ARROW_HEAD_THICKNESS; 
		($start_position, $end_position) = ($end_position, $start_position);
			
	} else {
		$MID_POINT		=  (($arrow_end - $start_position)) / 2 + $start_position;
		$end_position	=   $arrow_end - $ARROW_HEAD_THICKNESS;
	}
		
  	#Full start
	my $s_rad 		= 	_get_radian($PLASMID_SIZE, $start_position); 
  	
	#End of gene
	my $e_rad 		= 	_get_radian($PLASMID_SIZE, $end_position); 
  	
	#End of arrow
	my $a_rad		=	_get_radian($PLASMID_SIZE, $arrow_end);	

  	my $large_arc_flag = 0;
  	my $sweep_flag     = 1;
  
  	if ( ($e_rad - $s_rad)>$PI){
  		$large_arc_flag =1;
	}		 
  	#
	if ($INVERSE_FLAG ){
		$sweep_flag		=	0;
	}
	
	my $MID_RAD	=	_get_radian($PLASMID_SIZE, $MID_POINT);
	
	if($MID_RAD > $PI){
		$TEXT_ALIGN = "start";
	} else {
		$TEXT_ALIGN = "end";
	}
	
	($MID_X,$MID_Y)			  = _get_cord($TEXT_RADIUS, $MID_RAD);
	my ($startx, $starty)	  = _get_cord($RADIUS,$s_rad);
	my ($endx,$endy)          = _get_cord ($RADIUS,$e_rad);
	my ($inner_x1, $inner_y1) = _get_cord($INNER_RADIUS, $s_rad);
	my ($outer_x1, $outer_y1) = _get_cord($OUTER_RADIUS,$s_rad);
	my ($inner_x2, $inner_y2) = _get_cord($INNER_RADIUS, $e_rad);
	my ($outer_x2, $outer_y2) = _get_cord($OUTER_RADIUS,$e_rad);
	my ($arrowx, $arrowy)	=	_get_cord($RADIUS, $a_rad);
	my ($realend_x1, $realend_y1)=_get_cord($INNER_RADIUS, $a_rad);
	my ($realend_x2, $realend_y2)=_get_cord($OUTER_RADIUS, $a_rad);
	#The arrow-head
	my($vertice_1_x, $vertice_1_y) =_get_cord(($RADIUS-$THICKNESS), $e_rad);
	my($vertice_2_x, $vertice_2_y) =_get_cord(($RADIUS+$THICKNESS), $e_rad);

	if ($INVERSE_FLAG){
		($vertice_1_x, $vertice_1_y) =_get_cord(($RADIUS-$THICKNESS), $s_rad);
		($vertice_2_x, $vertice_2_y) =_get_cord(($RADIUS+$THICKNESS), $s_rad);
	 ($inner_x1, $inner_y1) = _get_cord($INNER_RADIUS, $e_rad);
	 ($outer_x1, $outer_y1) = _get_cord($OUTER_RADIUS,$e_rad);
	 ($inner_x2, $inner_y2) = _get_cord($INNER_RADIUS, $s_rad);
	 ($outer_x2, $outer_y2) = _get_cord($OUTER_RADIUS,$s_rad);
	
	}
	my $s = "";
	
	if ($ARROW eq "arrow_on"){	
	 	$s = qq(<path d="M $inner_x1 $inner_y1 L $outer_x1 $outer_y1 A $OUTER_RADIUS,$OUTER_RADIUS 0 $large_arc_flag,$sweep_flag $outer_x2 $outer_y2 L $vertice_2_x $vertice_2_y L $arrowx $arrowy L $vertice_1_x $vertice_1_y L $inner_x2 $inner_y2 A $INNER_RADIUS,$INNER_RADIUS 0 $large_arc_flag,);
	}

	if($ARROW eq "arrow_off"){
$s = qq(<path d="M $inner_x1 $inner_y1 L $outer_x1 $outer_y1 A $OUTER_RADIUS,$OUTER_RADIUS 0 $large_arc_flag,$sweep_flag $realend_x2 $realend_y2 L $realend_x1 $realend_y1 A $INNER_RADIUS,$INNER_RADIUS 0 $large_arc_flag,);
	}
	
	if ($sweep_flag == 0){
		$sweep_flag = 1;
	}
	else{
		$sweep_flag = 0;
	}

	
	$s .=$sweep_flag;
 	$s .= qq( $inner_x1 $inner_y1" fill="$COLOR" stroke="black" stroke-width="0.5" />\n);
	print $s;
	$MARKER_NAME =~ s/_/ /g;
	print qq(<text x="$MID_X" y="$MID_Y" text-anchor="$TEXT_ALIGN" font-size="10px" font-family="Arial" font-weight="bold">$MARKER_NAME</text>\n);
	return '';
}

#--------------------------------------------------------------------------
sub _get_svg_coords
	{
	my $quadrant = shift;
	my $enzyme = shift;
	my $x = shift;
	my $y = shift;
	my @return = @{(shift)};
	my $enzyme_text_length = length($enzyme);
	my @svg_coords;
	my $x1;
	my $x2;
	my $y1;
	my $y2;
	my $y_offset;
	my $test = "fail";
	#my $loop_break = 0;
		
	if ($quadrant == 1)
		{
		$x1 = $x+4;
		$x2 = $x + ($enzyme_text_length * 1.8);  #the '1.8' is currently an empirically determined coefficient (still need to sort this out...)
		$y1 = $y - 12;
		$y2 = $y - 3;
		$y_offset = -1;
		}
	
	elsif ($quadrant == 2)
		{
		$x1 = $x;
		$x2 = $x + ($enzyme_text_length * 1.8); 
		$y1 = $y - 9;
		$y2 = $y ;
		$y_offset = 1;
		}
	
	elsif ($quadrant == 3)
		{
		$x1 = $x - ($enzyme_text_length * 1.8);
		$x2 = $x; 
		$y1 = $y - 9;
		$y2 = $y ;
		$y_offset = 1;
		}
		
	elsif ($quadrant == 4)
		{
		$x1 = $x - ($enzyme_text_length * 1.8);
		$x2 = $x; 
		$y1 = $y - 12;
		$y2 = $y - 1;
		$y_offset = -1;
		}
	
	while ($test eq "fail")
		{
		for (my $i = 0; $i <= $#return; $i++)
			{
			#check to see if there is any overlap between the new element, and one of the elements already in the return array
			if (($y1 >= $return[$i][2] && $y1 <= $return[$i][3] && $x1 >= $return[$i][0] && $x1 <= $return[$i][1])||($y1 >= $return[$i][2] && $y1 <= $return[$i][3] && $x2 >= $return[$i][0] && $x2 <= $return[$i][1])||($y2 >= $return[$i][2] && $y2 <= $return[$i][3] && $x1 >= $return[$i][0] && $x1 <= $return[$i][1])||($y2 >= $return[$i][2] && $y2 <= $return[$i][3] && $x2 >= $return[$i][0] && $x2 <= $return[$i][1]))
				{
				$test = "fail";
				$y1 += $y_offset;
				$y2 += $y_offset;						
				#$loop_break++;
				last;
				}
			else
				{
				#print qq(<holder>$return[$i][0] $return[$i][1] $return[$i][2] $return[$i][3]</holder>\n);
				$test = "pass";
				}
			}
		}
		
	@svg_coords = ($x1, $x2, $y1, $y2);
	my @coords = (\@svg_coords, $x1, $y2); #Must be the last line to return @coords.
	}

#--------------------------------------------------------------------------
sub _draw_enzymes
	{
	my $self			=	shift;
	my ($name,$position)=	@_;
	my $PLASMID_SIZE	=	$SVGPlasmid::SIZE;
	my $radius			=	$SVGPlasmid::RADIUS;
	my $end_radius		=	$radius + 25;
	my $text_radius		=  $end_radius + 5;
	my $PI = 4 * atan2(1,1);
	my @quadrant1;
	my @quadrant2;
	my @quadrant3;
	my @quadrant4;
	my @first_set = (0,0,0,0);
	my @return1;
	$return1[0] = \@first_set;
	my @return2;
	$return2[0] = \@first_set;
	my @return3;
	$return3[0] = \@first_set;
	my @return4;
	$return4[0] = \@first_set; 
	my @test_repeat_array;
	my @repeat_array;
	
	for(my $i=0; $i<@$position; $i++)
		{
		if (grep( /$name->[$i]/, @test_repeat_array))
			{
			push(@repeat_array,$name->[$i]); 
			}
		else
			{
			push(@test_repeat_array,$name->[$i]);
			}
		}
	
	for( my $i= 0; $i< @$position; $i++)
		{
		my $rad	= _get_radian($PLASMID_SIZE,$position->[$i]);
		if ($rad < ($PI * 0.5)) 		# < 90 degrees
			{
			my @push_values = ($name->[$i],$position->[$i],$rad);
			push(@quadrant1,\@push_values); 
			}
		elsif ($rad < $PI) 	# < 180 degrees
			{
			my @push_values = ($name->[$i],$position->[$i],$rad);
			push(@quadrant2,\@push_values);
			}
		
		elsif ($rad < ($PI * 1.5)) 	# < 270 degrees
			{
			my @push_values = ($name->[$i],$position->[$i],$rad);
			push(@quadrant3,\@push_values);
			}
		
		else  					# up to 360 degrees
			{
			my @push_values = ($name->[$i],$position->[$i],$rad);
			push(@quadrant4,\@push_values);
			}
		}
	
	#sort the quadrant arrays, so the following loops will pass the enzymes in the best order for drawing
	@quadrant1 = reverse sort { $a->[1] <=> $b->[1] } @quadrant1;
	@quadrant2 = sort { $a->[1] <=> $b->[1] } @quadrant2;
	@quadrant3 = reverse sort { $a->[1] <=> $b->[1] } @quadrant3;
	@quadrant4 = sort { $a->[1] <=> $b->[1] } @quadrant4;
	
	my $counter = 1;
	foreach(@quadrant1)
		{
		my ($x,$y) = _get_cord($radius, $_->[2]);
		my ($x1,$y1)	=	_get_cord($end_radius,$_->[2]);
		my @coords = &_get_svg_coords(1,$_[0]." ".$_[1],$x1,$y1,\@return1);
		my $text_x = $coords[1];
		my $text_y = $coords[2];
		my @next_coords = ($coords[0][0],$coords[0][1],$coords[0][2],$coords[0][3]);
		my $fill = "black";
		
		push(@return1,\@next_coords);

		my $temp = $_->[0];
		if (grep( /$temp/, @repeat_array))
			{
			$fill = "red"; 
			}
			
		print qq(<path d="M $x $y L $x1 $y1" fill="none" stroke="black" stroke-width="0.5" />\n);
		print qq(<text x="$text_x" y="$text_y" text-anchor="start" font-size="9px" font-family="Courier New" fill="$fill">$_->[0] $_->[1]</text>\n);
		}
	
	$counter = 1;
	foreach(@quadrant2)
		{
		my ($x,$y) = _get_cord($radius, $_->[2]);
		my ($x1,$y1)	=	_get_cord($end_radius,$_->[2]);
		my @coords = &_get_svg_coords(2,$_[0]." ".$_[1],$x1,$y1,\@return2);
		my $text_x = $coords[1];
		my $text_y = $coords[2];
		my @next_coords = ($coords[0][0],$coords[0][1],$coords[0][2],$coords[0][3]);
		my $fill = "black";
		
		push(@return2,\@next_coords);
		
		my $temp = $_->[0];
		if (grep( /$temp/, @repeat_array))
			{
			$fill = "red"; 
			}
			
		print qq(<path d="M $x $y L $x1 $y1" fill="none" stroke="black" stroke-width="0.5" />\n);
		print qq(<text x="$text_x" y="$text_y" text-anchor="start" font-size="9px" font-family="Courier New" fill="$fill">$_->[0] $_->[1]</text>\n);
		$counter++;
		}
	$counter = 1;
	foreach(@quadrant3)
		{
		my ($x,$y) = _get_cord($radius, $_->[2]);
		my ($x1,$y1)	=	_get_cord($end_radius,$_->[2]);
		my @coords = &_get_svg_coords(3,$_[0]." ".$_[1],$x1,$y1,\@return3);
		my $text_x = $coords[1];
		my $text_y = $coords[2];
		my @next_coords = ($coords[0][0],$coords[0][1],$coords[0][2],$coords[0][3]);
		my $fill = "black";
		
		push(@return3,\@next_coords);
		
		my $temp = $_->[0];
		if (grep( /$temp/, @repeat_array))
			{
			$fill = "red"; 
			}
			
		print qq(<path d="M $x $y L $x1 $y1" fill="none" stroke="black" stroke-width="0.5" />\n);
		print qq(<text x="$text_x" y="$text_y" text-anchor="start" font-size="9px" font-family="Courier New" fill="$fill">$_->[0] $_->[1]</text>\n);
		$counter++;
		}
	$counter = 1;
	foreach(@quadrant4)
		{
		my ($x,$y) = _get_cord($radius, $_->[2]);
		my ($x1,$y1)	=	_get_cord($end_radius,$_->[2]);
		my @coords = &_get_svg_coords(4,$_[0]." ".$_[1],$x1,$y1,\@return4);
		my $text_x = $coords[1];
		my $text_y = $coords[2];
		my @next_coords = ($coords[0][0],$coords[0][1],$coords[0][2],$coords[0][3]);
		my $fill = "black";

		push(@return4,\@next_coords);
		
		my $temp = $_->[0];
		if (grep( /$temp/, @repeat_array))
			{
			$fill = "red"; 
			}
			
		print qq(<path d="M $x $y L $x1 $y1" fill="none" stroke="black" stroke-width="0.5" />\n);
		print qq(<text x="$text_x" y="$text_y" text-anchor="start" font-size="9px" font-family="Courier New" fill="$fill">$_->[0] $_->[1]</text>\n);
		$counter++;
		}
		return '';
	}
#--------------------------------------------------------------------------
sub _draw_mcs{
	my $self			=	shift;
	my $start			=	shift;
	my $end				=	pop (@_);
	#print "$end\n";
	
	my @enzymes			= 	@_;
	#print @enzymes, "\n";
	my $PLASMID_SIZE	=	$SVGPlasmid::SIZE;
	my $radius			=	$SVGPlasmid::RADIUS;
	my $end_radius		=	$radius + 75;
	my $text_coord_x;
	my $text_coord_y;
	my $INVERSE_FLAG	= 	undef; 	 
	
	my $PI = 4 * atan2(1,1);
	
	my $rad1			= 	_get_radian($PLASMID_SIZE, $start);
	my $rad2			=	_get_radian($PLASMID_SIZE, $end);
	
	#Calculate y coordinate
	my $no_enzyme 	= @enzymes;
	my $length		= 10 + (10 * $no_enzyme);
	$text_coord_y  = $SVGPlasmid::Y_COORD - ($length / 2);
	    
	my $text_attrib = "start";
	
	if ($rad1 > $PI){
		$text_attrib 	= "end";
		$INVERSE_FLAG	=  1;
		@enzymes = reverse @enzymes;
		$text_coord_x	=	125;
		
		($rad1, $rad2)	=	($rad2, $rad1);
	} else {
		$text_coord_x	=	700;
		
	} 
	
	my ($x0, $y0)		=	_get_cord($radius,$rad1);
	my ($x1, $y1)		=	_get_cord($end_radius,$rad1);
	my ($x2, $y2)		=	_get_cord($radius,$rad2);
	my ($x3, $y3)		=   _get_cord($end_radius,$rad2);
	#my ($text_x, $text_y) = _get_cord($text_radius, $rad);
		
	print qq(<path d="M $x0 $y0 L $x1 $y1 L $text_coord_x $text_coord_y" fill="none" stroke="silver" stroke-width="1" />\n);
	foreach my $enzyme(@enzymes){
		$text_coord_y += 10;
		print qq(<text x="$text_coord_x" y="$text_coord_y" text-anchor="$text_attrib" font-size="9px" font-weight="bold" font-family="Arial">$enzyme</text>\n);
		
	}
		
		print qq(<path d="M $x2 $y2 L $x3 $y3 L $text_coord_x $text_coord_y" fill="none" stroke="silver" stroke-width="1" />\n);
		#print qq(<text x="$text_x" y="$text_y" text-anchor="$text_attrib" font-size="10px" font-family="Arial">$name->[$i]</text>\n);
	return '';	
}

#--------------------------------------------------------------------------
#FUNCTION _end

sub _end {
	my $self = shift;
 	print qq(</svg>\n);
	return '';
}

1;
