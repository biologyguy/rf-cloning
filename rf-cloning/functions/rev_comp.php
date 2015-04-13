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
function rev_comp($sequence)
	{
	$sequence = preg_replace("/[^A-Za-z]/i","",$sequence);
	$sequence = preg_replace("/[^ATUCGatucg]/i","N",$sequence);
	
	$sequence = str_replace("A","W",$sequence);	
	$sequence = str_replace("T","X",$sequence);
	$sequence = str_replace("U","X",$sequence);
	$sequence = str_replace("G","Y",$sequence);	
	$sequence = str_replace("C","Z",$sequence);	
	$sequence = str_replace("a","w",$sequence);	
	$sequence = str_replace("t","x",$sequence);
	$sequence = str_replace("u","x",$sequence);	
	$sequence = str_replace("g","y",$sequence);	
	$sequence = str_replace("c","z",$sequence);
	
	$sequence = str_replace("W","T",$sequence);	
	$sequence = str_replace("X","A",$sequence);
	$sequence = str_replace("Y","C",$sequence);	
	$sequence = str_replace("Z","G",$sequence);	
	$sequence = str_replace("w","t",$sequence);	
	$sequence = str_replace("x","a",$sequence);	
	$sequence = str_replace("y","c",$sequence);	
	$sequence = str_replace("z","g",$sequence);
	
	$sequence = strrev($sequence);
	
	return $sequence;
	}
?>