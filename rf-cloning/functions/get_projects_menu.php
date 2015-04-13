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
function get_projects_menu($page)
	{
    $output['drop_down_menu'] = "<form method='post' action='rf_cloning_project.php'>
    								<select name='projects_list' id='projects_list' onChange=\"plasmid_focus(this.options[this.selectedIndex].value,'projects','".$page."');\">
            							<option value='nothing' > --------Projects in progress-------- </option>";
	
	$plasmids_query = mysql_query("SELECT * FROM projects WHERE user_id = ".$_COOKIE['user_id']." AND complete = 0;");
    
	$plasmids_array = array();
	
	while ($row = mysql_fetch_assoc($plasmids_query))
		{
		array_push($plasmids_array,$row);	
		}
	
	foreach($plasmids_array as $row)
                {
                $output['drop_down_menu'] .= "<option value='".$row['plasmid_id']."|".$row['plasmid_name']."'>".$row['plasmid_name']."</option>
                ";
                }
   
	$output['drop_down_menu'] .= "<option value='nothing' > ---------Completed projects--------- </option>";

	$plasmids_query = mysql_query("SELECT * FROM projects WHERE user_id = ".$_COOKIE['user_id']." AND complete = 1;");

	$plasmids_array = array();
	
	while ($row = mysql_fetch_assoc($plasmids_query))
		{
		array_push($plasmids_array,$row);	
		}

	foreach($plasmids_array as $row)
		{
		$output['drop_down_menu'] .= "<option value='".$row['plasmid_id']."|".$row['plasmid_name']."'>".$row['plasmid_name']."</option>
		";
		}
    $output['drop_down_menu'] .=  "</select><br />";
	
    $output['drop_down_no_submit'] = $output['drop_down_menu']."</form>";
    
	$output['drop_down_menu'] .= "<input type='submit' id='project_list_submit' name='submit' value='Get project' style='float:left;'/></form>";
	
	return($output);
    }	
    
    