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
function pcr_conditions($end_plas_size,$plasmid_size,$target_pcr_size)
	{
	
	$extension_time = floor(($end_plas_size/1000)*(0.33));
	if ((($end_plas_size/1000)*(0.33)*60%60) < 10 )
		{
		$extension_time_remainder = "0".(($end_plas_size/1000)*(0.33)*60%60);
		}
	else
		{
		$extension_time_remainder = (($end_plas_size/1000)*(0.33)*60%60);
		}

	$ug_of_plasmid = ($plasmid_size/5000) * 0.06 ;
	$pmol_of_plasmid = ($ug_of_plasmid * 1515)/$plasmid_size;
	$pmol_of_insert = $pmol_of_plasmid * 20;
	$ug_of_insert = ($pmol_of_insert * $target_pcr_size)/1515 ;
	
	$output['ug_of_plasmid'] = $ug_of_plasmid;
	$output['ng_of_plasmid'] = $ug_of_plasmid*1000;
	$output['pmol_of_plasmid'] = $pmol_of_plasmid;
	$output['ug_of_insert'] = $ug_of_insert;
	$output['ng_of_insert'] = $ug_of_insert*1000;
	$output['pmol_of_insert'] = $pmol_of_insert;
	$output['extension_time_mins'] = $extension_time.":".$extension_time_remainder;
	$output['extension_time_secs'] = round(($end_plas_size/1000)*(0.33)*60);
	
	return $output;	
	}
?>