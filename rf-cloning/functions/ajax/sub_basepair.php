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
//passed variables = $primer_dir_code, $fwd_primer_seq, $rev_primer_seq, $plasmid_seq, $insert_seq, $insert_sites
include("../melting_temp.php");
include("../rev_comp.php");
include("../primer_info.php");
include("../pcr_conditions.php");

//prepair the plasmid sequence by breaking it up based on insert sites
$insert_sites = explode("-",$_POST['insert_sites']);
$insert_site_1 = $insert_sites[0];
$insert_site_2 = $insert_sites[1];
$cut_sequence = substr($_POST['plasmid_seq'],$insert_site_1,($insert_site_2 - $insert_site_1));

$plasmid_array = array();
$plasmid_array[0] = substr($_POST['plasmid_seq'],0,$insert_site_1);
$plasmid_array[1] = $_POST['insert_seq'];
$plasmid_array[2] = substr($_POST['plasmid_seq'],$insert_site_2);

//split the current primers
$split_fwd_primer_array = explode("|",$_POST['fwd_primer_seq']);
$split_rev_primer_array = explode("|",$_POST['rev_primer_seq']);

switch($_POST['primer_dir_code'])
	{
	case 1: //fwd-fwd
		if (count($split_fwd_primer_array) == 3 )
			{			
			$final_sequence = substr($split_fwd_primer_array[2],0,strlen($split_fwd_primer_array[2])-1);		
			$fwd_primer_database = (strlen($split_fwd_primer_array[2]) == 1) ? $split_fwd_primer_array[0]."|".$split_fwd_primer_array[1] : $split_fwd_primer_array[0]."|".$split_fwd_primer_array[1]."|".$final_sequence;		
			}
		
		else
			{
			$final_sequence = (strlen($split_fwd_primer_array[1]) > 1) ? substr($split_fwd_primer_array[1],0,strlen($split_fwd_primer_array[1])-1) : $split_fwd_primer_array[1];	
			$fwd_primer_database = $split_fwd_primer_array[0]."|".$final_sequence;			
			}
		
		$rev_primer_database = $_POST['rev_primer_seq'];
		$primer_info = primer_info($fwd_primer_database,$rev_primer_database,$_POST['insert_seq']);
		
		break;
			
	case 2: //fwd-rev
		
		$final_sequence = (strlen($split_fwd_primer_array[0]) > 1) ? substr($split_fwd_primer_array[0],1) : $split_fwd_primer_array[0];
		
		$fwd_primer_database = (count($split_fwd_primer_array) == 2) ? $final_sequence."|".$split_fwd_primer_array[1] : $final_sequence."|".$split_fwd_primer_array[1]."|".$split_fwd_primer_array[2];
		
		$rev_primer_database = $_POST['rev_primer_seq'];
		
		$primer_info = primer_info($fwd_primer_database,$rev_primer_database,$_POST['insert_seq']);
								
		break;	
	
	
	case 3: //rev-fwd
		if (count($split_rev_primer_array) == 3 )
			{			
			$final_sequence = substr($split_rev_primer_array[2],0,strlen($split_rev_primer_array[2])-1);		
			$rev_primer_database = (strlen($split_rev_primer_array[2]) == 1) ? $split_rev_primer_array[0]."|".$split_rev_primer_array[1] : $split_rev_primer_array[0]."|".$split_rev_primer_array[1]."|".$final_sequence;		
			}
		
		else
			{
			$final_sequence = (strlen($split_rev_primer_array[1]) > 1) ? substr($split_rev_primer_array[1],0,strlen($split_rev_primer_array[1])-1) : $split_rev_primer_array[1];	
			$rev_primer_database = $split_rev_primer_array[0]."|".$final_sequence;			
			}
		
		$fwd_primer_database = $_POST['fwd_primer_seq'];
		$primer_info = primer_info($fwd_primer_database,$rev_primer_database,$_POST['insert_seq']);
		
		break;

	
	case 4: //rev-rev
		
		$final_sequence = (strlen($split_rev_primer_array[0]) > 1) ? substr($split_rev_primer_array[0],1) : $split_rev_primer_array[0];
		
		$rev_primer_database = (count($split_rev_primer_array) == 2) ? $final_sequence."|".$split_rev_primer_array[1] : $final_sequence."|".$split_rev_primer_array[1]."|".$split_rev_primer_array[2];
		
		$fwd_primer_database = $_POST['fwd_primer_seq'];
		
		$primer_info = primer_info($fwd_primer_database,$rev_primer_database,$_POST['insert_seq']);
		
		break;
	
	}


	$pcr_conditions = pcr_conditions(strlen($plasmid_array[0].$_POST['insert_seq'].$plasmid_array[2]),strlen($_POST['plasmid_seq']),$primer_info['target_pcr_size']);

	$output['fwd_primer'] = $primer_info['fwd_primer'];
	$output['fwd_ins_tm'] = $primer_info['fwd_ins_tm'];	
	$output['fwd_plas_tm'] = $primer_info['fwd_plas_tm'];
	$output['fwd_prim_len'] = $primer_info['fwd_primer_length'];
	$output['fwd_primer_database'] = $fwd_primer_database;
	$output['rev_primer'] = $primer_info['rev_primer'];
	$output['rev_ins_tm'] = $primer_info['rev_ins_tm'];	
	$output['rev_plas_tm'] = $primer_info['rev_plas_tm'];
	$output['rev_prim_len'] = $primer_info['rev_primer_length'];
	$output['rev_primer_database'] = $rev_primer_database;
	$output['pcr_prod_size'] = $primer_info['target_pcr_size'];
	$output['extension_time'] = $pcr_conditions['extension_time_mins'];
	$output['ng_insert'] = $pcr_conditions['ng_of_insert'];
	$output['ng_plasmid'] = $pcr_conditions['ng_of_plasmid'];
	$output['primer_dir_code'] = $_POST['primer_dir_code'];
	$output['no_first_pcr'] = $primer_info['no_first_pcr'];

echo json_encode($output);

?>