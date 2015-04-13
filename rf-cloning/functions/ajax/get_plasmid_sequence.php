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

$get_sequence = mysql_fetch_row(mysql_query("SELECT sequence FROM ".$_POST['database']." WHERE plasmid_id ='".$_POST['id']."' ;"));
$input = $get_sequence[0];
	
$counter = 0;
$input = preg_replace("/[^A-Za-z]/i","",$input);
$input = preg_replace("/[^ATGCatgc]/i","X",$input);
	
$input = chunk_split($input,10,",");

$input = explode(",",$input);
$output = "1\t";

while ($current_triplet = array_shift($input))
	{
	if ($counter != 0 && $counter%7 == 0)
		{
		$output .= "\r".($counter*10+1)."\t";	
		}
	$output .= $current_triplet." ";
	$counter++;
	}		
echo $output;


?>