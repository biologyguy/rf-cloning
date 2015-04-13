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
include("../functions/primer_design.php");
include("../functions/rev_comp.php");
include("../functions/pcr_conditions.php");
include("../functions/primer_info.php");
include("../functions/melting_temp.php");

function getPrimers($plasmid_seq, $insert_seq, $insert_sites, $PLAS_TARGET_TM = 60, $INS_TARGET_TM = 55, $PLAS_MIN_SIZE = 20, $INS_MIN_SIZE = 15, $PLAS_MAX_SIZE = 35, $INS_MAX_SIZE = 25)
	{
	//clear FASTA
	$target = preg_replace("/>.+/i","",$insert_seq);
	$plasmid = preg_replace("/>.+/i","",$plasmid_seq);
		
	//get rid of anything that isn't a letter
	$target = preg_replace("/[^A-Za-z]/i","",$target);
	$plasmid = preg_replace("/[^A-Za-z!]/i","",$plasmid);
	
	//convert any non-standard nucleotides into 'X'
	$target = preg_replace("/[^ATGCatgc]/i","X",$target);
	$plasmid = preg_replace("/[^ATGCatgc!]/i","X",$plasmid);
	
	//convert to upper case
	$target = strtoupper($target);
	$plasmid = strtoupper($plasmid);
		
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
	
	$new_construct = $plasmid_array[0]."|".$target."|".$plasmid_array[2];
	$remaining_plas_seq = $plasmid_array[0].$plasmid_array[2];
	
	$insert_site_1 = (strlen($plasmid_array[0]));
	$insert_site_2 = (strlen($plasmid_array[0]) + strlen($plasmid_array[1]));
	
	//if the user is inserting close to the "front" of the plasmid sequence, attach the sequence from the "back" of the plasmid so the primers can be designed
	if (strlen($plasmid_array[0]) < 25)
		{
		$plasmid_array[0] = $plasmid_array[2].$plasmid_array[0]; 		
		}
	
	//break if there isn't enough plasmid sequence (after adding the back sequence)
	if (strlen($plasmid_array[0]) < 25)
		{
		$output['error'] = "The plasmid sequence provided is to short. The minimum length required is 25bps on either side of your insert site, but include the entire sequence if you have it.";
		$output = json_encode($output);
		return $output;
		}
	
	//Create the primers. If the insert is larger than 50bps, a primary PCR will be required to amplify the insert from another source. If the insert is smaller however, then the whole thing can be synthesized with the primers.
	$fwd_plas_seq = substr(strrev($plasmid_array[0]),0,40);
	$rev_plas_seq = strrev(rev_comp(substr($plasmid_array[2],0,40)));
	$primers = primer_design($fwd_plas_seq,$rev_plas_seq,$target,$PLAS_TARGET_TM, $INS_TARGET_TM, $PLAS_MIN_SIZE, $INS_MIN_SIZE, $PLAS_MAX_SIZE, $INS_MAX_SIZE);
	$primer_info = primer_info($primers['fwd_primer_database'],$primers['rev_primer_database'],$insert_seq);
	
	if($primers['fwd_ins_tm'] == "")
		{
		$output['error'] = "You've set max primer length to low relative to max plasmid Tm, so the insert segment of the primer could not be calculated. Please increase primer length, or decrease max Tm.";
		$output = json_encode($output);
		return $output;	
		}
		
	$target_pcr_size = strlen($target) + $primers['fwd_plas_length'] + $primers['rev_plas_length'];
	
	$pcr_conditions = pcr_conditions(strlen($remaining_plas_seq),strlen($remaining_plas_seq),$target_pcr_size);
	
	
	if ((($end_plas_size/1000)*(0.33)*60%60) < 10 )
		{
		$extension_time_remainder = "0".(($end_plas_size/1000)*(0.33)*60%60);
		}
	else
		{
		$extension_time_remainder = (($end_plas_size/1000)*(0.33)*60%60);
		}
		
	$plasmid = preg_replace("/[!]/","",$plasmid);
	
	$output['new_construct'] = $new_construct;
	$output['fwd_primer_database'] = $primers['fwd_primer_database'];
	$output['rev_primer_database'] = $primers['rev_primer_database'];
	
	$output['fwd_plas_tm'] = $primers['fwd_plas_tm'];
	$output['fwd_ins_tm'] = $primers['fwd_ins_tm'];
	
	$output['rev_plas_tm'] = $primers['rev_plas_tm'];
	$output['rev_ins_tm'] = $primers['rev_ins_tm'];
	
	$output['ng_of_plasmid'] = $pcr_conditions['ng_of_plasmid'];
	$output['pmol_of_plasmid'] = $pcr_conditions['pmol_of_plasmid'];
	$output['ng_of_insert'] = $pcr_conditions['ng_of_insert'];
	$output['pmol_of_insert'] = $pcr_conditions['pmol_of_insert'];
	$output['extension_time_mins'] = $pcr_conditions['extension_time_mins'];
	$output['extension_time_secs'] = $pcr_conditions['extension_time_secs'];
	
	$output['target_pcr_size'] = $primer_info['target_pcr_size'];
	
	$output = json_encode($output);
	return $output;
	}

// turn off the wsdl cache
ini_set("soap.wsdl_cache_enabled", "0");

$server = new SoapServer("rf_cloning.wsdl");

$server->addFunction("getPrimers");

$server->handle();
?>