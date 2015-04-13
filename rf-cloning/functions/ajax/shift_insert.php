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
	require_once('../../../includes/rf-cloning/db_connect.php');
	include("../savvy_ouput.php");
	include("../primer_design.php");
	include("../primer_info.php");
	include("../melting_temp.php");
	include("../pcr_conditions.php");
	include("../rev_comp.php");
	
	$shift_insert_code = $_POST['shift_insert_code'];
	$target = $_POST['target'];
	$plasmid = $_POST['plasmid'];
	$insert_name = $_POST['insert_name'];
	$orientation = $_POST['orientation'];
	$arrow = $_POST['arrow'];
	$database = $_POST['database'];
	$backbone_id = $_POST['backbone_id'];
	$insert_sites = $_POST['insert_sites'];
	
	
	$insert_sites_array = explode("-",$insert_sites);
	$plasmid_length = strlen($plasmid);
	
	switch ($shift_insert_code)
		{
		case 1:
			if($insert_sites_array[0] > 0)
				$insert_sites = ($insert_sites_array[0] - 1)."-".$insert_sites_array[1];
		break;	
		
		case 2:
			if(($insert_sites_array[0] < $insert_sites_array[1]) && ($insert_sites_array[0] < $plasmid_length))
				$insert_sites = ($insert_sites_array[0] + 1)."-".$insert_sites_array[1];
			elseif(($insert_sites_array[0] == $insert_sites_array[1]) && ($insert_sites_array[0] < $plasmid_length))
				$insert_sites = ($insert_sites_array[0] + 1)."-".($insert_sites_array[1] + 1);
		break;
		
		case 3:
			if(($insert_sites_array[0] < $insert_sites_array[1]) && ($insert_sites_array[1] > 0))
				$insert_sites = $insert_sites_array[0]."-".($insert_sites_array[1] - 1);
			elseif(($insert_sites_array[0] == $insert_sites_array[1]) && ($insert_sites_array[1] > 0))
				$insert_sites = ($insert_sites_array[0] - 1)."-".($insert_sites_array[1] - 1);
		break;
		
		case 4:
			if($insert_sites_array[1] < $plasmid_length)
				$insert_sites = $insert_sites_array[0]."-".($insert_sites_array[1] + 1);
		break;	
		}	
	
	//Break the plasmid into an array based on insert sites
	$insert_sites_array = explode("-",$insert_sites);
	$plasmid_array = array();
	array_push($plasmid_array,substr($plasmid,0,$insert_sites_array[0]));
	array_push($plasmid_array,substr($plasmid,$insert_sites_array[0],($insert_sites_array[1]-$insert_sites_array[0])));
	array_push($plasmid_array,substr($plasmid,$insert_sites_array[1]));	
	
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
	if (strlen($plasmid_array[0]) < 25)
		{
		$plasmid_array[0] = $plasmid_array[2].$plasmid_array[0]; 		
		}
	
	
	//Create the primers. If the insert is larger than 50bps, a primary PCR will be required to amplify the insert from another source. If the insert is smaller however, then the whole thing can be synthesized with the primers.
	$fwd_plas_seq = substr(strrev($plasmid_array[0]),0,40);
	$rev_plas_seq = strrev(rev_comp(substr($plasmid_array[2],0,40)));
	$primers = primer_design($fwd_plas_seq,$rev_plas_seq,$target);
		
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
	$primer_info = primer_info($primers['fwd_primer_database'],$primers['rev_primer_database'],$target);	
	$pcr_conditions = pcr_conditions(strlen($simple_sequence),strlen($plasmid),$primer_info['target_pcr_size']);
	
	$output['backbone_id'] = $backbone_id; 
	$output['new_construct'] = $new_construct;
	$output['sequence'] = $simple_sequence;
	$output['plasmid_sequence'] = $plasmid;
	$output['insert_sequence'] = $target;
	$output['insert_sites'] = $insert_site_1."-".$insert_site_2;
	$output['old_insert_sites'] = $_POST['insert_sites'];
	$output['new_size'] = $end_plas_size;
	$output['savvy_markers'] = $savvy_output[0];
	$output['savvy_enzymes'] = $savvy_output[1];
	$output['fwd_primer_database'] = $primers['fwd_primer_database'];
	$output['rev_primer_database'] = $primers['rev_primer_database'];
	$output['fwd_primer'] = $primer_info['fwd_primer'];
	$output['rev_primer'] = $primer_info['rev_primer'];
	$output['fwd_plas_tm'] = $primer_info['fwd_plas_tm'];
	$output['rev_plas_tm'] = $primer_info['rev_plas_tm'];
	$output['fwd_ins_tm'] = $primer_info['fwd_ins_tm'];
	$output['rev_ins_tm'] = $primer_info['rev_ins_tm'];
	$output['fwd_primer_length'] = $primer_info['fwd_primer_length'];
	$output['rev_primer_length'] = $primer_info['rev_primer_length'];
	$output['target_pcr_size'] = $primer_info['target_pcr_size'];
	$output['extension_time'] = $pcr_conditions['extension_time_mins'];
	$output['ng_of_insert'] = $pcr_conditions['ng_of_insert'];
	$output['ng_of_plasmid'] = $pcr_conditions['ng_of_plasmid'];
	$output['no_first_pcr'] = $primers['no_first_pcr'];
	
	echo json_encode($output);	
	
	
?>