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
function primer_info($fwd_prim, $rev_prim, $insert_sequence)
	{
	//split the primers (from database) into their constituent parts (plasmid sequence and insert sequence)
	$fwd_primer_array = explode("|",$fwd_prim);
	$rev_primer_array = explode("|",$rev_prim);
	
	//calculate all the accoutrement associated with the primers, and put them into an array
	$primers = array();

	//If the primer spans the entire insert, we need to add a second <span> in the sequence output
	if (empty($fwd_primer_array[2]))
		{
		$primers['fwd_primer'] = "<span style='background-color:#09F;font-family:Courier New;'>".$fwd_primer_array[0]."</span><span style='background-color:#6F3;font-family:Courier New;'>".$fwd_primer_array[1]."</span>";
		}
	else
		{
		$primers['fwd_primer'] = "<span style='background-color:#09F;font-family:Courier New;'>".$fwd_primer_array[0]."</span><span style='background-color:#6F3;font-family:Courier New;'>".$fwd_primer_array[1]."</span><span style='background-color:#09F;font-family:Courier New;'>".$fwd_primer_array[2]."</span>";
		}	
		
	if (empty($rev_primer_array[2]))
		{
		$primers['rev_primer'] = "<span style='background-color:#09F;font-family:Courier New;'>".$rev_primer_array[0]."</span><span style='background-color:#6F3;font-family:Courier New;'>".$rev_primer_array[1]."</span>";
		}
	else
		{
		$primers['rev_primer'] = "<span style='background-color:#09F;font-family:Courier New;'>".$rev_primer_array[0]."</span><span style='background-color:#6F3;font-family:Courier New;'>".$rev_primer_array[1]."</span><span style='background-color:#09F;font-family:Courier New;'>".$rev_primer_array[2]."</span>";
		}	
	
	$primers['fwd_plas_seq'] = $fwd_primer_array[0];
	$primers['fwd_ins_seq'] = $fwd_primer_array[1];
	$primers['fwd_ins_prim_cont_seq'] = (!empty($fwd_primer_array[2])) ? $fwd_primer_array[2] : "";
	
	$primers['rev_plas_seq'] = $rev_primer_array[0];
	$primers['rev_ins_seq'] = $rev_primer_array[1];
	$primers['rev_ins_prim_cont_seq'] = (!empty($rev_primer_array[2])) ? $rev_primer_array[2] : "";
						
	//calculate Tm for the primers
	$primers['fwd_plas_tm'] = melting_temp($fwd_primer_array[0]);
	$primers['fwd_ins_tm'] = melting_temp($fwd_primer_array[1]);
	
	$primers['fwd_plas_length'] = strlen($fwd_primer_array[0]);
	$primers['fwd_primer_length'] = isset($fwd_primer_array[2]) ? strlen($fwd_primer_array[0].$fwd_primer_array[1].$fwd_primer_array[2]) : strlen($fwd_primer_array[0].$fwd_primer_array[1]);
	
	$primers['rev_plas_tm'] = melting_temp($rev_primer_array[0]);
	$primers['rev_ins_tm'] = melting_temp($rev_primer_array[1]);
	
	$primers['rev_plas_length'] = strlen($rev_primer_array[0]);
	$primers['rev_primer_length'] = isset($rev_primer_array[2]) ? strlen($rev_primer_array[0].$rev_primer_array[1].$rev_primer_array[2]) : strlen($rev_primer_array[0].$rev_primer_array[1]);
	
	//get the pcr product size: I need to take into account the possibility that the insert is small, and the continuing plasmid sequence on the 3' end has been extended to make it longer then the plasmid sequence on the 5' end 
	if(strlen($primers['fwd_ins_prim_cont_seq']) > $primers['rev_plas_length'])
		{
		$target_pcr_size = strlen($insert_sequence) + $primers['fwd_plas_length'] + strlen($primers['fwd_ins_prim_cont_seq']);	
		}
	
	elseif(strlen($primers['rev_ins_prim_cont_seq']) > $primers['fwd_plas_length'])
		{
		$target_pcr_size = strlen($insert_sequence) + strlen($primers['rev_ins_prim_cont_seq']) + $primers['rev_plas_length'];	
		}
		
	elseif((strlen($primers['fwd_ins_prim_cont_seq']) > $primers['rev_plas_length']) && (strlen($primers['rev_ins_prim_cont_seq']) > $primers['fwd_plas_length']))
		{
		$target_pcr_size = strlen($insert_sequence) + strlen($primers['rev_ins_prim_cont_seq']) + strlen($primers['fwd_ins_prim_cont_seq']);	
		}
	
	else
		{
		$target_pcr_size = strlen($insert_sequence) + $primers['fwd_plas_length'] + $primers['rev_plas_length'];
		}
		
	$primers['target_pcr_size'] = $target_pcr_size;
	
	//Deal with "No first PCR"	
	if(!empty($fwd_primer_array[2]) || !empty($rev_primer_array[2]))
		{
		$fwd_primer_array[2] = empty($fwd_primer_array[2]) ? "" : $fwd_primer_array[2];
		$rev_primer_array[2] = empty($rev_primer_array[2]) ? "" : $rev_primer_array[2];
		$primers['no_first_pcr'] = "The insert is fully synthesized by the primers. Use a 5 cycle PCR reaction (w/o any plasmid) to anneal and extend 5ng of each primer.";
		//note: the melting temp of the primery PCR primers are equal when the entire insert is enclosed within the primer.
		$primers['fwd_ins_tm'] = melting_temp($fwd_primer_array[1].$fwd_primer_array[2].$rev_primer_array[2]);
		$primers['rev_ins_tm'] = $primers['fwd_ins_tm'];
		}
	
	elseif((strlen($primers['rev_ins_seq'].$primers['fwd_ins_seq']) - strlen($insert_sequence)) >= 15)
		{
		$primers['no_first_pcr'] = "The insert is fully synthesized by the primers. Use a 5 cycle PCR reaction (w/o any plasmid) to anneal and extend 5ng of each primer.";
		$overlap = substr($rev_primer_array[1],0,(strlen($primers['rev_ins_seq'].$primers['fwd_ins_seq']) - strlen($insert_sequence)));
		$primers['rev_ins_tm'] = melting_temp($overlap);
		$primers['fwd_ins_tm'] = $primers['rev_ins_tm'];
		}
		
	else
		{
		$primers['no_first_pcr'] = "";
		}
		
		return $primers;
	}
?>