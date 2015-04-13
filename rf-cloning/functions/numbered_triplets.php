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
function numbered_triplets($input)
	{
	$counter = 0;
	$input = preg_replace("/[^A-Za-z]/i","",$input);
	$input = preg_replace("/[^ATUGCatugc]/i","X",$input);
		
	$input = chunk_split($input,10,",");
	
	$input = explode(",",$input);
	$output = "";
	
	while ($current_triplet = array_shift($input))
		{
		if ($counter%7 == 0)
			{
			$output .= "\r".($counter*10+1)."\t";	
			}
		$output .= $current_triplet." ";
		$counter++;
		}		
	return $output;	
	}
?>