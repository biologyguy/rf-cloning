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
$info_array = (! empty($_POST['user_id'])) ? mysql_fetch_row(mysql_query("SELECT session_check FROM users WHERE user_id = ".$_POST['user_id'].";")) : die("You must <a href='../../login.php'>log in</a> to save plasmids");

if($info_array[0] != $_POST['session_check'])
	{
	die("You must <a href='../../login.php'>log in</a> to save plasmids");	
	}
$checksum = md5($_POST['sequence']);
mysql_query("UPDATE ".$_POST['database']." SET plasmid_name = '".$_POST['plasmid_name']."', sequence = '".$_POST['sequence']."', savvy_markers = '".$_POST['markers']."', savvy_enzymes='".$_POST['enzymes']."' checksum = '".$checksum."' WHERE plasmid_id = ".$_POST['plasmid_id'].";") or die("There was a database error.<br />".mysql_error());

echo $_POST['plasmid_name']." Updated";

?>