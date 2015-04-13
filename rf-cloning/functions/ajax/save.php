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
session_start();
require_once('../../../includes/rf-cloning/db_connect.php');
require_once("../../classes/Plasmid.php");

$php_plasmid = json_decode(stripslashes($_POST['plasmid_obj']));
unset($php_plasmid->checksum);
unset($php_plasmid->error);

$new_save = $php_plasmid->user_id == 1 ? true : false; 

$php_plasmid->user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

$plasmid = new Plasmid();
$plasmid->set_parameters(get_object_vars($php_plasmid));


$save_result = $plasmid->save();
if (is_int($save_result))
	{
	echo $plasmid->get_parameters("plasmid_name")." saved.~|~".$save_result."~|~".$php_plasmid->user_id;	
	}

elseif($new_save && $php_plasmid->user_id == 1)
	{
	echo "Changes to project id: ".$plasmid->get_parameters("proj_hash")." (".$plasmid->get_parameters("plasmid_name").") saved to the database.~|~".$save_result."~|~".$php_plasmid->user_id;	
	}

elseif($new_save)
	{
	echo $plasmid->get_parameters("plasmid_name")." saved.~|~".$plasmid->get_parameters("plasmid_id")."~|~".$php_plasmid->user_id;	
	}
	
elseif($save_result)
	{
	echo $plasmid->get_parameters("plasmid_name")." updated.~|~".$plasmid->get_parameters("plasmid_id")."~|~".$php_plasmid->user_id;	
	}		
		
?>