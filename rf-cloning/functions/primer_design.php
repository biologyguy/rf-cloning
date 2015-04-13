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
function primer_design($fwd_plasmid_seq,$rev_plasmid_seq,$insert_seq, $PLAS_TARGET_TM = 60, $INS_TARGET_TM = 55, $PLAS_MIN_SIZE = 20, $INS_MIN_SIZE = 15, $PLAS_MAX_SIZE = 35, $INS_MAX_SIZE = 25)
{
//Make sure there aren't any weird values sneaking through that could get the program stuck in a loop
$PLAS_TARGET_TM = $PLAS_TARGET_TM > 83 ? 83 : $PLAS_TARGET_TM;
$INS_TARGET_TM = $INS_TARGET_TM > 83 ? 83 : $INS_TARGET_TM;
$PLAS_MAX_SIZE = $PLAS_MAX_SIZE > 100 ? 100 : $PLAS_MAX_SIZE;
$INS_MAX_SIZE = $INS_MAX_SIZE > 100 ? 100: $INS_MAX_SIZE;
$PLAS_MIN_SIZE = $PLAS_MIN_SIZE > $PLAS_MAX_SIZE ? $PLAS_MAX_SIZE : $PLAS_MIN_SIZE;
$INS_MIN_SIZE = $INS_MIN_SIZE > $INS_MAX_SIZE ? $INS_MAX_SIZE : $INS_MIN_SIZE;

$MAX_SIZE = $PLAS_MAX_SIZE + $INS_MAX_SIZE;

//initialize some variables
$no_first_pcr = "";

//plasmids first
//Forward
for ($a = $PLAS_MIN_SIZE; $a <= $PLAS_MAX_SIZE; $a++)
	{
	$fwd_plas_prim = substr($fwd_plasmid_seq,0,$a);
	$fwd_plas_tm = melting_temp($fwd_plas_prim);
	if ($fwd_plas_tm > $PLAS_TARGET_TM)
		{
		break;
		}
	}
$fwd_plas_length = strlen($fwd_plas_prim);

//Reverse	
for ($b = $PLAS_MIN_SIZE; $b <= $PLAS_MAX_SIZE; $b++)
	{
	$rev_plas_prim = substr($rev_plasmid_seq,0,$b);
	$rev_plas_tm = melting_temp($rev_plas_prim);
	
	if ($rev_plas_tm > $PLAS_TARGET_TM)
		{
		break;
		}
	}
$rev_plas_length = strlen($rev_plas_prim);

//Deal with the insert second
//Forward. This algorithm will handle inserts which are big enough to require primary amplification off of an exogenous template, as well as small inserts which can be synthesized using primers alone.
for ($c = $INS_MIN_SIZE; $c <= $INS_MAX_SIZE; $c++)
	{
	$fwd_ins_prim = substr($insert_seq,0,$c);
	$fwd_ins_tm = melting_temp($fwd_ins_prim);
	
	if ($fwd_ins_tm > $INS_TARGET_TM)
		{
		$no_first_pcr = "";
		break;
		}
	}
$fwd_ins_length = strlen($fwd_ins_prim);

//Reverse
$insert_seq = rev_comp($insert_seq);
for ($d = $INS_MIN_SIZE; $d <= $INS_MAX_SIZE; $d++)
	{
	$rev_ins_prim = substr($insert_seq,0,$d);
	$rev_ins_tm = melting_temp($rev_ins_prim);
	
	if ($rev_ins_tm > $INS_TARGET_TM)
		{
		break;
		}
	}
$rev_ins_length = strlen($rev_ins_prim);

//See if the insert for this project can be completely synthesized.
$insert_length = strlen($insert_seq);
$available_insert_primer = (($MAX_SIZE*2) - $fwd_plas_length - $rev_plas_length);
$fwd_ins_prim_cont = "";
$rev_ins_prim_cont = "";

if ($insert_length <= $available_insert_primer)
	{
	//handle really short inserts by adding extra base pairs from the flanking sides of the plasmid
	if ($fwd_ins_length == $insert_length && $rev_ins_length == $insert_length)
		{
		$no_first_pcr = "The insert is fully synthesized by the primers. Use a 5 cycle PCR reaction (w/o any plasmid) to anneal and extend 5ng of each primer.";
		$counter = 1;
		$full_tm = 0;
		while (($full_tm <= $INS_TARGET_TM && ($fwd_plas_length + $fwd_ins_length) <= $MAX_SIZE) || $counter + $fwd_ins_length < 16)
			{
			$fwd_ins_prim_cont = substr(strrev(rev_comp($rev_plasmid_seq)),0,$counter);
			$rev_ins_prim_cont = substr(strrev(rev_comp($fwd_plasmid_seq)),0,$counter);
			
			$fwd_ins_length++;
			$rev_ins_length++;
			
			$full_tm = melting_temp($rev_ins_prim_cont.$fwd_ins_prim.$fwd_ins_prim_cont);
			$fwd_ins_tm = $full_tm;
			$rev_ins_tm = $full_tm;
			
			$counter++;
			}
		
		//reduce the reverse primer by one basepair if the Ins_TM will remain above 50 after doing so
		$check_shorter_rev_ins_prim = strrev($fwd_ins_prim_cont).$rev_ins_prim.substr(strrev(rev_comp($fwd_plasmid_seq)),0,$counter-2);
		$rev_tm_check = melting_temp($check_shorter_rev_ins_prim);
		if ($rev_tm_check >= $INS_TARGET_TM)
			{
			$rev_ins_prim_cont = substr(strrev(rev_comp($fwd_plasmid_seq)),0,$counter-2);
			$rev_ins_length--;
			$fwd_ins_tm = $rev_tm_check;
			$rev_ins_tm = $rev_tm_check;
			}
		}
	
	//handle longer inserts that are not sysnthesized completely on both primer, but are still short enough to be fully synthesized
	elseif($insert_length <= (($MAX_SIZE*2) - ($fwd_plas_length + $rev_plas_length + 15)))
		{
		$no_first_pcr = "The insert is fully synthesized by the primers. Use a 5 cycle PCR reaction (w/o any plasmid) to anneal and extend 5ng of each primer.";
		//Checks first to make sure the insert qualifies (there must be at least a 15bps overlap with a Tm of 50+) before making any changes to the primers. 
		$available_bps = ($MAX_SIZE * 2) - ($fwd_plas_length + $rev_plas_length);
		$counter = 15;
		$available_fwd_bps = $MAX_SIZE - $fwd_plas_length;
		$available_rev_bps = $MAX_SIZE - $rev_plas_length;
		
		//save the fwd and rev primers made earlier, just incase a good Tm can't be had by fully synthesizing.
		$fwd_ins_prim_save = $fwd_ins_prim;
		$fwd_ins_tm_save = $fwd_ins_tm;
		$rev_ins_prim_save = $rev_ins_prim;
		$rev_ins_tm_save = $rev_ins_tm;
		$fwd_ins_length_save = $fwd_ins_length;
		$rev_ins_length_save = $rev_ins_length;
		
		//create the appropraite initial overlapping primers, depending on the number of available bps on each primer 
		////echo $available_fwd_bps." ".(($insert_length-15)/2)." ";
		if (($available_fwd_bps-15) < (($insert_length-15)/2))
			{
			//use all available	fwd
			////echo "first";
			$fwd_ins_prim = substr(rev_comp($insert_seq),0,$available_fwd_bps);
			$rev_ins_prim = substr($insert_seq,0,($insert_length - $available_fwd_bps));
			$fwd_ins_length = strlen($fwd_ins_prim);
			$rev_ins_length = strlen($rev_ins_prim);
			$prim_overlap = substr(rev_comp($insert_seq),(strlen($fwd_ins_prim)-$counter),$counter);
			$overlap_tm = melting_temp($prim_overlap);
			$fwd_ins_tm = $overlap_tm;
			$rev_ins_tm = $overlap_tm;
			}
		
		elseif (($available_rev_bps-15) < (($insert_length-15)/2))
			{
			//use all available	rev
			////echo "second";
			$rev_ins_prim = substr($insert_seq,0,$available_rev_bps);
			$fwd_ins_prim = substr(rev_comp($insert_seq),0,($insert_length - $available_rev_bps));
			$fwd_ins_length = strlen($fwd_ins_prim);
			$rev_ins_length = strlen($rev_ins_prim);
			$prim_overlap = substr($insert_seq,(strlen($rev_ins_prim)-15),$counter);
			$overlap_tm = melting_temp($prim_overlap);
			$fwd_ins_tm = $overlap_tm;
			$rev_ins_tm = $overlap_tm;
			}
			
		else
			{
			$fwd_ins_prim = substr(rev_comp($insert_seq),0,(floor(($insert_length-15)/2) + $counter));
			$rev_ins_prim = substr($insert_seq,0,(ceil($insert_length-15)/2)+$counter);
			$fwd_ins_length = strlen($fwd_ins_prim);
			$rev_ins_length = strlen($rev_ins_prim);
			$prim_overlap = substr(rev_comp($insert_seq),(strlen($fwd_ins_prim)-$counter),$counter);
			$overlap_tm = melting_temp($prim_overlap);
			$fwd_ins_tm = $overlap_tm;
			$rev_ins_tm = $overlap_tm;
			//echo round($overlap_tm)." ".$prim_overlap." ";
			}
		//continue to add bps to the primers until a Tm of 50 is reached, or we run out of available bps
		while ($overlap_tm < $INS_TARGET_TM)
			{
			//echo " ".$counter;
			$counter++;
			if (($counter + 15) >= $available_bps)
				{
				//if a good Tm can't be had, return to the saved primers designed earlier
				$no_first_pcr = "";
				$fwd_ins_prim = $fwd_ins_prim_save;
				$fwd_ins_tm = $fwd_ins_tm_save;
				$rev_ins_prim = $rev_ins_prim_save;
				$rev_ins_tm = $rev_ins_tm_save;
				$fwd_ins_length = $fwd_ins_length_save;
				$rev_ins_length = $rev_ins_length_save;
				break;
				}
			
			if ($counter%2 == 0)
				{
				if (strlen($fwd_ins_prim) < $available_fwd_bps)
					{
					$fwd_ins_prim = substr(rev_comp($insert_seq),0,((($insert_length - $counter)/2) + $counter+1));
					$fwd_ins_length = strlen($fwd_ins_prim);
					$prim_overlap = substr(rev_comp($insert_seq),(strlen($fwd_ins_prim)-$counter),$counter);
					$overlap_tm = melting_temp($prim_overlap);
					$fwd_ins_tm = $overlap_tm;
					$rev_ins_tm = $overlap_tm;
					//echo "fwd: ".round($overlap_tm)." ".$prim_overlap." - ";
					}
				else
					{
					continue;
					}
				}
			
			else
				{
				if (strlen($rev_ins_prim) < $available_rev_bps)
					{
					$rev_ins_prim = substr($insert_seq,0,(floor(($insert_length - $counter)/2) + $counter));
					$rev_ins_length = strlen($rev_ins_prim);
					$prim_overlap = substr($insert_seq,(strlen($rev_ins_prim)-$counter),$counter);
					$overlap_tm = melting_temp($prim_overlap);
					$fwd_ins_tm = $overlap_tm;
					$rev_ins_tm = $overlap_tm;
					//echo "rev: ".round($overlap_tm)." ".rev_comp($prim_overlap)." - ";
					}
				else
					{
					continue;
					}	
				}
			}					
		}
	}

	
$output['fwd_primer'] = "<span style='background-color:#09F;font-family:Courier New;'>".strrev($fwd_plas_prim)."</span><span style='background-color:#6F3;font-family:Courier New;'>".$fwd_ins_prim."</span><span style='background-color:#09F;font-family:Courier New;'>".$fwd_ins_prim_cont."</span>";

$output['rev_primer'] = "<span style='background-color:#09F;font-family:Courier New;'>".strrev($rev_plas_prim)."</span><span style='background-color:#6F3;font-family:Courier New;'>".$rev_ins_prim."</span><span style='background-color:#09F;font-family:Courier New;'>".$rev_ins_prim_cont."</span>";				

$output['fwd_primer_database'] = ($fwd_ins_prim_cont != "") ? strrev($fwd_plas_prim)."|".$fwd_ins_prim."|".$fwd_ins_prim_cont : strrev($fwd_plas_prim)."|".$fwd_ins_prim;
$output['rev_primer_database'] = ($rev_ins_prim_cont != "") ? strrev($rev_plas_prim)."|".$rev_ins_prim."|".$rev_ins_prim_cont : strrev($rev_plas_prim)."|".$rev_ins_prim;

$output['fwd_plas_tm'] = $fwd_plas_tm;
$output['fwd_ins_tm'] = $fwd_ins_tm;
$output['fwd_plas_length'] = $fwd_plas_length;
$output['fwd_primer_length'] = ($fwd_plas_length+$fwd_ins_length);
$output['fwd_plas_seq'] = strrev($fwd_plas_prim);
$output['fwd_ins_seq'] = $fwd_ins_prim;
$output['fwd_ins_prim_cont_seq'] = (!empty($fwd_ins_prim_cont)) ? $fwd_ins_prim_cont : false;

$output['rev_plas_tm'] = $rev_plas_tm;
$output['rev_ins_tm'] = $rev_ins_tm;
$output['rev_plas_length'] = $rev_plas_length;
$output['rev_primer_length'] = ($rev_plas_length+$rev_ins_length);
$output['rev_plas_seq'] = strrev($rev_plas_prim);
$output['rev_ins_seq'] = $rev_ins_prim;
$output['rev_ins_prim_cont_seq'] = (!empty($rev_ins_prim_cont)) ? $rev_ins_prim_cont : false;

$output['no_first_pcr'] = $no_first_pcr;

return $output;
}
?>
