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
function retrieve_saved($project_id)
	{	
	include_once("savvy_ouput.php");
	
	$construct_info_array = ($project_id == "nothing") ? false : mysql_fetch_assoc(mysql_query("SELECT * FROM projects WHERE plasmid_id = ".$project_id.";"));
	
	
	$backbone_name = mysql_fetch_row(mysql_query("SELECT plasmid_name FROM ".$construct_info_array['backbone_database']." WHERE plasmid_id = ".$construct_info_array['backbone_id'].";"));
	
	$target = $construct_info_array['insert_sequence'];

//get all the sequences sorted out so we can re-build the final plasmid	
	$insert_sites = explode("-",$construct_info_array['insert_sites']);
	$insert_site_1 = $insert_sites[0];
	$insert_site_2 = $insert_sites[1];
	$cut_sequence = substr($construct_info_array['plasmid_sequence'],$insert_site_1,($insert_site_2 - $insert_site_1));
	
	$plasmid_array = array();
	$plasmid_array[0] = substr($construct_info_array['plasmid_sequence'],0,$insert_site_1);
	$plasmid_array[1] = $construct_info_array['insert_sequence'];
	$plasmid_array[2] = substr($construct_info_array['plasmid_sequence'],$insert_site_2);
	
	//grab the full sequence of the new construct before the values in $plasmid_array are modified, break it into chunks so it displays nice, and add some colour.
	$left_seq_remainder = (strlen($plasmid_array[0])%10);
	$left_space = ($left_seq_remainder == 0) ? " " : "";
		
	$target_seq_remainder = ((strlen($target)-(10-$left_seq_remainder))%10);
	$target_space = ($target_seq_remainder == 0) ? " " : "";
	
	if ($target_seq_remainder < 0)
		{
		$target_sequence = $target;	
		$right_sequence = substr($plasmid_array[2],0,(10-strlen($target)-$left_seq_remainder))." ".chunk_split(substr($plasmid_array[2],(10-strlen($target)-$left_seq_remainder)),10," ");
		}
	else
		{
		$target_sequence = substr($target,0,(10-$left_seq_remainder))." ".rtrim(chunk_split(substr($target,(10-$left_seq_remainder)),10," ")).$target_space;	
		$right_sequence = substr($plasmid_array[2],0,(10-$target_seq_remainder))." ".chunk_split(substr($plasmid_array[2],(10-$target_seq_remainder)),10," ");
		}
	
	
	$left_sequence = rtrim(chunk_split($plasmid_array[0],10," ")).$left_space;
	
	
	$new_construct = "<span style='background-color:#09F;font-size:5pt;font-family:Courier New;'>".$left_sequence."</span><span style='background-color:#6F3;font-size:5pt;font-family:Courier New;'>".$target_sequence."</span><span style='background-color:#09F;font-size:5pt;font-family:Courier New;'>".$right_sequence."</span>";
	

	$complete_disabled = ($construct_info_array['complete'] == 1) ? "disabled='disabled'" : "";
	$incomplete_disabled = ($construct_info_array['complete'] == 0) ? "disabled='disabled'" : "";
	
	
	
	$output['backbone_id'] = $construct_info_array['backbone_id'];
	$output['backbone_database'] = $construct_info_array['backbone_database'];
	$output['proj_id'] = $project_id;
	$output['sequence'] = $construct_info_array['sequence'];
	$output['plasmid_sequence'] = $construct_info_array['plasmid_sequence'];
	$output['insert_sequence'] = $construct_info_array['insert_sequence'];
	$output['insert_sites'] = $construct_info_array['insert_sites'];	
	$output['new_size'] = $construct_info_array['new_size'];
	$output['plasmid_name'] = $construct_info_array['plasmid_name'];
	$output['insert_name'] = $construct_info_array['insert_name'];
	$output['backbone_name'] = $backbone_name[0];
	$output['notes'] = $construct_info_array['notes'];
	$output['savvy_markers'] = $construct_info_array['savvy_markers'];
	$output['savvy_enzymes'] = $construct_info_array['savvy_enzymes'];
	$output['savvy_meta'] = $construct_info_array['savvy_meta'];
	$output['complete'] = $construct_info_array['complete'];
	$output['complete_disabled'] = ($construct_info_array['complete'] == 1) ? "disabled='disabled'" : "";
	$output['incomplete_disabled'] = ($construct_info_array['complete'] == 0) ? "disabled='disabled'" : "";
	$output['new_construct'] = $new_construct;	
	$output['fwd_primer_database'] = $construct_info_array['fwd_primer'];
	$output['rev_primer_database'] = $construct_info_array['rev_primer'];
	
	
	return $output;	
	}
?>