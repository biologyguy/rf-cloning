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


$plasmid_query_array = mysql_fetch_row(mysql_query("SELECT savvy_MCS, sequence FROM ".$_POST['database']." WHERE plasmid_id='".$_POST['id']."';"));

$mcs_array = explode(",",$plasmid_query_array[0]);

$mcs_start = array_shift($mcs_array);
$mcs_end = array_pop($mcs_array);
$mcs_length = $mcs_end - $mcs_start;

$mcs_sequence = substr($plasmid_query_array[1],$mcs_start,$mcs_length);

$mcs_sequence = chunk_split($mcs_sequence,3," ");

echo "<b>MCS sequence</b><br />".$mcs_sequence;


?>
