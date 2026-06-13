<?php
/*************************************************************************************************#
# www.rf-cloning.org
#
# Copyright (C) Steve R. Bond <biologyguy@gmail.com>
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
include("../../classes/Plasmid.php");

$info_array = (! empty($_POST['user_id'])) ? mysqli_fetch_row(mysqli_query($conn, "SELECT session_check FROM users WHERE user_id = ".$_POST['user_id'].";")) : die("You must <a href='../../login.php'>log in</a> to save plasmids");

if($info_array[0] != $_POST['session_check'])
	{
	die("You must <a href='../../login.php'>log in</a> to save plasmids");	
	}

$plasmid_obj = new Plasmid($conn);

if (isset($_POST['plasmid_id']) && ($_POST['plasmid_id'] != "new"))
	{
	$plasmid_id = explode("|",$_POST['plasmid_id']);
	$plasmid_obj->set_database_plasmid($conn, $plasmid_id[0],"projects");
	$params_array = array("database" => "plasmids", "plasmid_id" => "new", "user_id" => $_POST['user_id'], "privacy" => $_POST['privacy']);
	$plasmid_obj->set_parameters($params_array);
	$plasmid_obj->save();
	}

else
	{
	$params_array = array("database" => "plasmids",
						  "plasmid_id" => "new",
						  "plasmid_seq" => $_POST['sequence'],
						  "plasmid_name" => $_POST['plasmid_name'],
						  "savvy_markers" => $_POST['markers'],
						  "savvy_enzymes" => $_POST['enzymes'],
						  "user_id" => $_POST['user_id'],
						  "privacy" => $_POST['privacy'],
						  "popularity" => 0);
	$plasmid_obj->set_parameters($params_array);
	$plasmid_obj->save();
	}

$error = $plasmid_obj->get_error();
if (strpos($error, 'Duplicate entry') !== false){
	echo "Error: You already have a plasmid by this name in your profile.";
}
else if (strpos($error, 'plasmid_seq') !== false){
	echo "Error: You must supply a sequence for your new plasmid.";
}
else if (strpos($error, 'plasmid_name') !== false){
	echo "Error: You must supply a name for your new plasmid.";
}

?>
