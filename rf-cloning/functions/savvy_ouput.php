<?php
/*************************************************************************************************#
# www.rf-cloning.org
#
# Copyright (C) Steve R. Bond <biologyguy@gmail.com>
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
function savvy_output($conn, $backbone_id, $insert_name, $insert_sequence, $first_insert, $second_insert, $orientation, $arrow, $database)
{

$id_field_name = "plasmid_id" ;

/*******************************************************************************************************************************************
There is a problem with the next line of code. If the user executes a project based on a stored plasmid, and then uses the back button and
then re-executes the project without changing the plasmid sequence, then the backbone id and database info is not passed along. This 
affects the savvy output, and probably some of the info saved if the user wants to keep the new plasmid in the database.
*******************************************************************************************************************************************/
if ($backbone_id == "blank" || $backbone_id == 0) 
	{
	$plasmid_data_array = array();
	$plasmid_data_array['savvy_markers'] = "";
	$plasmid_data_array['savvy_enzymes'] = "";
	}

else
	{
	$plasmid_data_array =	mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM ".$database." WHERE ".$id_field_name." = '".$backbone_id."' ;"));
	}
//*******************************************************************************************************************************************//
//This page shows the results when the insert is added																						 //	
//*******************************************************************************************************************************************//
	
	$insert_size = strlen($insert_sequence);
	
	$bps_shifted = ($first_insert - $second_insert + $insert_size);
				
//Start off by parsing the markers data, and inserting the new sequence in
	//make sure that all \n characters and \r\r are changed to \r before exploding
	$marker_data = str_replace("\n","\r",$plasmid_data_array['savvy_markers']);
	$marker_data = str_replace("\r\r","\r",$marker_data);
	$marker_data = trim($marker_data);

	$marker_row_array = explode("\r",$marker_data);
	
	$new_markers_output = "";
	
//Add in all the saved markers
	while ($row = array_shift($marker_row_array))
		{
		$markers_array = explode(" ", $row);

		if ((max($markers_array[1],$markers_array[2])) < (min($second_insert,$first_insert)))
			{
			foreach($markers_array as $a)
				{
				$new_markers_output .= $a." ";
				}
			$new_markers_output .= "\r";
			continue;
			}
			
		elseif ((min($markers_array[1],$markers_array[2])) < (min($second_insert,$first_insert)) && (max($markers_array[1],$markers_array[2])) > (max($second_insert,$first_insert)))
			{	
			if ($markers_array[2] > $markers_array[1])
				{
				$markers_array[2] += $bps_shifted;
				}
			
			else
				{
				$markers_array[1] += $bps_shifted;	
				}
			
			foreach($markers_array as $a)
				{
				$new_markers_output .= $a." ";
				}
			$new_markers_output .= "\r";
			continue;	
			}
		elseif ((min($markers_array[1],$markers_array[2])) > (min($second_insert,$first_insert)) && (max($markers_array[1],$markers_array[2])) < (max($second_insert,$first_insert)))
			{
			continue;
			}
			
		elseif ((min($markers_array[1],$markers_array[2])) > (max($second_insert,$first_insert)))
			{
			$new_markers_output .= $markers_array[0]." ".($markers_array[1] + $bps_shifted)." ".($markers_array[2] + $bps_shifted);
			$new_markers_output .= " ".implode(" ", array_slice($markers_array, 3))."\r";
			continue;
			}
		}
		
	//Put in the insert 	
	if ($orientation == "ccw")
		{
		$pos1 = $first_insert + $insert_size;	
		$pos2 = $first_insert;
		}
	else
		{
		$pos1 = $first_insert;	
		$pos2 = $first_insert + $insert_size;
		}
	$new_markers_output .= $insert_name." ".$pos1." ".$pos2." arrow_".$arrow." Filled Red 12";

	//sort all of the markers
	$marker_row_array = explode("\r",$new_markers_output);
	$multiD_markers = array();
	
	foreach($marker_row_array as $next)
		{
		array_push($multiD_markers,explode(" ",$next));
		}	
	
	$new_markers_output = "";
	
	function aasort (&$array, $key1, $key2) 
		{
	    $sorter=array();
	    $ret=array();
	    reset($array);
	    foreach ($array as $ii => $va) 
	    	{
	        $sorter[$ii]=min($va[$key1],$va[$key2]);
	    	}
	    asort($sorter);
	    foreach ($sorter as $ii => $va) 
	    	{
	        $ret[$ii]=$array[$ii];
	    	}
	    $array=$ret;
		}
	
	aasort($multiD_markers,1,2);
	
	foreach($multiD_markers as $row)
		{
		foreach($row as $next)
			{	
			$new_markers_output .= $next." ";	
			}
		$new_markers_output .= "\r";
		}

//Next, parse the enzymes data, and shift them around as needed
$enzymes_array = explode(":",$plasmid_data_array['savvy_enzymes']);
$new_enzyme_output = "";

foreach ($enzymes_array as $enzyme)
	{
	$position = explode(" ",$enzyme);
	
	if (empty($position[1]))
		{
		continue;
		}
		
	if ($position[1] < $first_insert)
		{
		$new_enzyme_output .= $enzyme.":";	
		continue;
		}
	
	elseif ($position[1] > $first_insert && $position[1] < $second_insert)
		{
		continue;	
		}
	
	elseif ($position[1] > $second_insert)
		{
		$new_enzyme_output .= $position[0]." ".($position[1]+$bps_shifted).":";	
		}	
	}

/*finally, parse the MCS
$MCS_array = explode(",",$plasmid_data_array['savvy_MCS']);
$MCS_start = array_shift($MCS_array);
$MCS_end = array_pop($MCS_array);

//If the new insert is within the MCS, delete the MCS from the Savvy map. If I get hold of the savvy cgi script, I'd like to keep the positions of all MCS sites, and use that indo when this happens to keep those restriction sites on the map...
if (($MCS_start < $first_insert && $MCS_end >= $second_insert) || empty($MCS_start))
	{
	$new_MCS = "";
	}

else
	{
	if ($MCS_start > $first_insert)
		{
		$MCS_start += $bps_shifted;
		}
	
	if ($MCS_end > $second_insert)
		{
		$MCS_end += $bps_shifted;
		}
	
	$new_MCS = $MCS_start.",";
	
	foreach ($MCS_array as $b)
		{
		$new_MCS .= $b.",";	
		}
	
	$new_MCS .= $MCS_end;
	}
*/

	
$output[0] = $new_markers_output;
$output[1] = $new_enzyme_output;

return $output;
}
?>
