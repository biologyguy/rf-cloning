<?php
/*************************************************************************************************#
# www.rf-cloning.org
#
# Copyright (C) 2009-2014 Steve R. Bond <biologyguy@gmail.com>
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License version 3 as published by
# the Free Software Foundation <http://www.gnu.org/licenses/>
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#*************************************************************************************************/

/********************************************************************************************
There is a bug in this code somewhere which is causing the positioning of colors to be a 
little bit off. It isn't severe, but it's annoying... I've added a comment at the top of
the outputted page to let the user know about the bug, but try to fix it in the future.

I may want to just re-write this from scratch at some point...
*******************************************************************************************/

require_once('../../../includes/rf-cloning/db_connect.php');

$get_sequence = mysql_fetch_row(mysql_query("	SELECT sequence, savvy_markers 
												FROM ".$_POST['database']." 
												WHERE plasmid_id ='".$_POST['id']."' ;"));
$input = $get_sequence[0];

$counter = 0;
$input = preg_replace("/[^A-Za-z]/i","",$input);
$input = preg_replace("/[^ATUGCatugc]/i","X",$input);

$input = chunk_split($input,3,",");

$input = explode(",",$input);
$output = "1\t";
$features = "	<h4>Features</h4>
				<table><tr><td>&nbsp;</td><td>Start</td><td>End</td></tr>";

while ($current_triplet = array_shift($input))
	{
	if ($counter != 0 && $counter%20 == 0)
		{
		$output .= "\r".($counter*3+1)."\t";	
		}
	$output .= $current_triplet." ";
	$counter++;
	}		


$get_sequence[1] = preg_replace("/[\n]/","\r",$get_sequence[1]);
$full_markers_array = explode("\r",$get_sequence[1]);
$markers_multi_array = array();

foreach($full_markers_array as $row)
	{
	$marker_points_array = explode(" ",$row);
	if (! isset($marker_points_array[1]))
		{
		continue;
		}
	$max_point = max($marker_points_array[1],$marker_points_array[2]);
	$min_point = min($marker_points_array[1],$marker_points_array[2]);
	$point_array = array($min_point,$max_point,$marker_points_array[5],$marker_points_array[0]);
	array_push($markers_multi_array,$point_array);
	}

function multi2dSortAsc(&$arr, $key)
	{ 
	$sort_col = array(); 
	foreach ($arr as $sub) $sort_col[] = $sub[$key]; 
	array_multisort($sort_col, $arr); 
	}

function extra_char_count($position)
	{
	$N = floor($position/60);
	$R = $position%60;
	$number_chars = 1;
	$line_cardinal = (0.15);
	$count = 2;
	//this part is just getting the number of characters contained in the far left column of numbers
	while ($N > 1)
		{
		if ($N <= floor($line_cardinal))
			{
			$number_chars += $N * $count;
			$N = 0;
			}
		
		else
			{
			$number_chars += (floor($line_cardinal)*$count);
			$N -= floor($line_cardinal);
			$line_cardinal *= 10;
			$count++;
			}
		}
			
	//Now add up the remaining characters, including \s, \t, and \r
	$number_chars += (floor($position/60)*21);
	
	$number_chars += (floor($R/3)+1);
	
	return $number_chars;
	}

$features_array = array();

for($c = 0; $c <= (count($markers_multi_array) * 2); $c++)
	{
			
	// do the array sorting 
	multi2dSortAsc($markers_multi_array, 0);
	$markers_multi_array_0 = array_reverse($markers_multi_array);

	multi2dSortAsc($markers_multi_array, 1);
	$markers_multi_array_1 = array_reverse($markers_multi_array);
	
	if($markers_multi_array_0[0][0] == (-1) && $markers_multi_array_1[0][1] == (-1))
		{
		break;	
		}
	
	if($markers_multi_array_0[0][0] >= $markers_multi_array_1[0][1])
		{
		$markers_multi_array = $markers_multi_array_0;
		
		$extra_characters = extra_char_count($markers_multi_array_0[0][0]);
		$backend_of_output = substr($output,($markers_multi_array[0][0]-2)+$extra_characters);
		if ($markers_multi_array[0][2] == "Black" || $markers_multi_array[0][2] == "black")
			{
			$markers_multi_array[0][2] = "grey; border:thin black solid;";
			} 
		
		$output = substr($output,0,($markers_multi_array[0][0]-2)+$extra_characters)."<span style='background-color:".$markers_multi_array[0][2].";'>".$backend_of_output;
		
		$end_pos = ($markers_multi_array[0][1] == (-1)) ? $hold_end_pos : $markers_multi_array[0][1];
		
		array_push($features_array,"<tr><td style='background-color:".$markers_multi_array[0][2].";'>".$markers_multi_array[0][3]."</td><td>".$markers_multi_array[0][0]."</td><td>".$end_pos."</td></tr>");
		$markers_multi_array[0][0] = (-1);
		}
	
	else
		{
		$markers_multi_array = $markers_multi_array_1;
		
		$extra_characters = extra_char_count($markers_multi_array_0[0][1]);
		$backend_of_output = substr($output,($markers_multi_array[0][1])+$extra_characters-1);
		$output = substr($output,0,($markers_multi_array[0][1])+$extra_characters-1)."</span>".$backend_of_output;
		$hold_end_pos = $markers_multi_array[0][1];
		$markers_multi_array[0][1] = (-1);	
		}	
	}

$features_array = array_reverse($features_array);

foreach($features_array as $row)
	{
	$features .= $row;	
	}

$features .= "</table>";
echo "This map is only a guide. The exact position of the colors may not perfectly align with the position of the markers, so please check your sequence carefully.<br />";
echo "<pre>".$output."</pre>";
echo "<div style='position:absolute;left:750px;top:100px;'>".$features."</div>";

$new_file = fopen("check_file.txt","w+");
fwrite($new_file,"<pre>".$output."</pre>");


?>
