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
//Post variables needed = backbone_id, sequence, plasmid_sequence, insert_sequence, insert_sites, new_size, construct_name, notes, savvy_markers, savvy_enzymes, savvy_MCS, fwd_primer_database, rev_primer_database

require_once('../../../includes/rf-cloning/db_connect.php');
$login_status = "false";

if(isset($_COOKIE['user_id']))
	{
	$user_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE user_id='".$_COOKIE['user_id']."' ;"));
	if ($user_info['session_check'] == $_COOKIE['session_check'])
		{
		$login_status = "true";
		}	
	}

if ($login_status == "true")
	{
	
	if ($_POST['project_id'] > 0)
		{
		$output = $_POST['construct_name']." has been updated.";
				
		mysqli_query($conn, "UPDATE projects SET plasmid_name='".$_POST['construct_name']."', insert_name=\"".$_POST['insert_name']."\", fwd_primer = '".$_POST['fwd_primer_database']."',rev_primer = '".$_POST['rev_primer_database']."', insert_sites='".$_POST['insert_sites']."', new_size='".$_POST['new_size']."', notes='".$_POST['notes']."', savvy_markers=\"".$_POST['savvy_markers']."\", savvy_enzymes=\"".$_POST['savvy_enzymes']."\" WHERE user_id=".$_COOKIE['user_id']." AND plasmid_id=".$_POST['project_id'].";") or ($output = "ERROR: ".mysqli_error($conn));  	
		}
	
	elseif ($_POST['project_id'] == "new")
		{
		$output = $_POST['construct_name']." saved to the database.";
		
		mysqli_query($conn, "INSERT INTO projects (user_id, backbone_id, sequence, plasmid_sequence, insert_sequence, insert_sites, fwd_primer, rev_primer, new_size, plasmid_name, notes, savvy_markers, savvy_enzymes, savvy_meta, insert_name, backbone_database, checksum) VALUES (".$_COOKIE['user_id'].",".$_POST['backbone_id'].",'".$_POST['sequence']."','".$_POST['plasmid_sequence']."','".$_POST['insert_sequence']."','".$_POST['insert_sites']."','".$_POST['fwd_primer_database']."','".$_POST['rev_primer_database']."','".$_POST['new_size']."','".$_POST['construct_name']."','".$_POST['notes']."',\"".$_POST['savvy_markers']."\",\"".$_POST['savvy_enzymes']."\",\"".$_POST['savvy_meta']."\",\"".$_POST['insert_name']."\",\"".$_POST['backbone_database']."\",\"".md5($_POST['plasmid_sequence'])."\");") or ($output = "ERROR: ".mysqli_error($conn));
		
		$new_id = mysqli_insert_id($conn);
		$output .= "~|~".$new_id;
		
		$popularity = mysqli_fetch_assoc(mysqli_query($conn, "SELECT popularity FROM plasmids WHERE plasmid_id = ".$_POST['backbone_id'].";"));
		$popularity['popularity']++;
		mysqli_query($conn, "UPDATE plasmids SET popularity = ".$popularity['popularity']." WHERE plasmid_id = ".$_POST['backbone_id'].";");
		}
		
	else
		{
		$output = "There was an error with the project ID ";	
		}
	echo $output;
	}

else
	{
	echo "You need to log in to save a project";	
	}
?>
