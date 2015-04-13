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
function rf_cloning_output($target, $plasmid, $backbone_name, $insert_name, $orientation, $arrow, $insert_description, $database, $backbone_id, $insert_sites, $plas_target_tm, $ins_target_tm, $plas_min_size, $ins_min_size, $plas_max_size, $ins_max_size)
	{
	include_once("savvy_ouput.php");
	include_once("primer_design.php");
	include_once("melting_temp.php");

	//break if the user didn't fill in one of the sequences
	if (($target == "blank") || ($plasmid == "blank"))
		{
		$output[0] = "You need to include a target sequence, as well as the plasmid sequence, for this to work.<br /><a href='index.php'>Return to main page</a>";
		return $output;
		}
	
	//clear FASTA
	$target = preg_replace("/>.+[\r\n]/i","",$target);
	$plasmid = preg_replace("/>.+[\r\n]/i","",$plasmid);
		
	//get rid of anything that isn't a letter
	$target = preg_replace("/[^A-Za-z]/i","",$target);
	$plasmid = preg_replace("/[^A-Za-z!]/i","",$plasmid);
	
	//convert any non-standard nucleotides into 'X'
	$target = preg_replace("/[^ATUGCatugc]/i","X",$target);
	$plasmid = preg_replace("/[^ATUGCatugc!]/i","X",$plasmid);
	
	//convert to upper case
	$target = strtoupper($target);
	$plasmid = strtoupper($plasmid);
	
	//replace spaces in backbone name and insert name with underscores
	$backbone_name = preg_replace("/[ ]/","_",$backbone_name);
	$insert_name = preg_replace("/[ ]/","_",$insert_name);
		
	//Break the plasmid into an array based on insert sites
	if($insert_sites)
		{
		$insert_sites_array = explode("-",$insert_sites);
		$plasmid_array = array();
		array_push($plasmid_array,substr($plasmid,0,$insert_sites_array[0]));
		array_push($plasmid_array,substr($plasmid,$insert_sites_array[0],($insert_sites_array[1]-$insert_sites_array[0])));
		array_push($plasmid_array,substr($plasmid,$insert_sites_array[1]));	
		}
	else
		{
		$plasmid_array = explode("!",$plasmid);
		}
		
	//break if the user didn't specify where to put the insert into the plasmid
	if (count($plasmid_array) != 3)
		{
		$output['error'] = "You need to specify where you want your insert to sit for this to work.<br /><a href='index.php'>Return to main page</a>";	
		return $output;		
		}
	
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
	
	
	$simple_sequence = $plasmid_array[0].$target.$plasmid_array[2];
	
	$insert_site_1 = (strlen($plasmid_array[0]));
	$insert_site_2 = (strlen($plasmid_array[0]) + strlen($plasmid_array[1]));
	
	//if the user is inserting close to the "front" of the plasmid sequence, attach the sequence from the "back" of the plasmid so the primers can be designed
	if (strlen($plasmid_array[0]) < 100)
		{
		$plasmid_array[0] = $plasmid_array[2].$plasmid_array[0]; 		
		}
	
	//break if there isn't enough plasmid sequence (after adding the back sequence)
	if (strlen($plasmid_array[0]) < 200)
		{
		$output['error'] = "The plasmid sequence provided is to short. The minimum length required is 100bps on either side of your insert site, but include the entire sequence if you have it.<br /><a href='index.php'>Return to main page</a>";
		return $output;
		}
	
	//Create the primers. If the insert is larger than 50bps, a primary PCR will be required to amplify the insert from another source. If the insert is smaller however, then the whole thing can be synthesized with the primers.
	$fwd_plas_seq = substr(strrev($plasmid_array[0]),0,100);
	$rev_plas_seq = strrev(rev_comp(substr($plasmid_array[2],0,100)));
	$primers = primer_design($fwd_plas_seq, $rev_plas_seq, $target, $plas_target_tm, $ins_target_tm, $plas_min_size, $ins_min_size, $plas_max_size, $ins_max_size);
		
	$target_pcr_size = strlen($target) + $primers['fwd_plas_length'] + $primers['rev_plas_length'];
	$end_plas_size = strlen($plasmid) - strlen($plasmid_array[1]) + strlen($target) - 2;
	
	if ((($end_plas_size/1000)*(0.33)*60%60) < 10 )
		{
		$extension_time_remainder = "0".(($end_plas_size/1000)*(0.33)*60%60);
		}
	else
		{
		$extension_time_remainder = (($end_plas_size/1000)*(0.33)*60%60);
		}
	
	
		$savvy_output = savvy_output($backbone_id, $insert_name, $target, $insert_site_1, $insert_site_2, $orientation, $arrow, $database);
		
		
	$plasmid = preg_replace("/[!]/","",$plasmid);
	
	$output['backbone_id'] = $backbone_id; 
	$output['backbone_database'] = $database;
	$output['proj_id'] = "new";
	$output['sequence'] = $simple_sequence;
	$output['plasmid_sequence'] = $plasmid;
	$output['insert_sequence'] = $target;
	$output['insert_sites'] = $insert_site_1."-".$insert_site_2;	
	$output['new_size'] = $end_plas_size;
	$output['plasmid_name'] = $backbone_name."-".$insert_name;
	$output['backbone_name'] = $backbone_name;
	$output['insert_name'] = $insert_name;
	$output['notes'] = $insert_description;
	$output['savvy_markers'] = $savvy_output[0];
	$output['savvy_enzymes'] = $savvy_output[1];
	$output['savvy_meta'] = $orientation."-".$arrow;
	$output['complete'] = 0;
	$output['complete_disabled'] = "";
	$output['incomplete_disabled'] = "disabled='disabled'";
	$output['new_construct'] = $new_construct;	
	$output['fwd_primer_database'] = $primers['fwd_primer_database'];
	$output['rev_primer_database'] = $primers['rev_primer_database'];
	
	return $output;	
	}
	
?>