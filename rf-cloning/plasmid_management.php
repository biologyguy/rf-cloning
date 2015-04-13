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

require_once('includes/db_connect.php');
include("functions/set_session.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="icon" type="image/ico" href="favicon.ico" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>RF-Cloning Plasmid Management</title>
<script src="classes/ajaxObj.js" language="javascript" type="text/javascript"></script>
<script src="classes/raphael_uncompressed.js" language="javascript" type="text/javascript"></script>
<script src="javascript/ajax.js" language="javascript" type="text/javascript"></script>
<script src="javascript/javascripts.js" language="javascript" type="text/javascript"></script>
<script src="javascript/management.js" language="javascript" type="text/javascript"></script>
<link rel="stylesheet" href="includes/styles.css" />

</head>

<body>
<input type="hidden" id="page" name="page" value="management" />
<?php
if ($login_status == "false")
	{
    die("You must be logged in to access this content. Please <a href='login.php'>do so</a>, or <a href='register.php'>register</a> if you don't have an account.");	
	}
?>

<div style="width:670px;">
    <div class="tabs">
        <ul>
            <li><a href='index.php'><span>Home</span></a></li>
            <li><a href='savvy.php'><span>Savvy</span></a></li>
            <li><a href='QandA.php' target="_blank"><span>Q & A</span></a></li>
            <li><a href='soap_server.php'><span>SOAP</span></a></li>
            <li><a href="login.php"><span>Log out</span></a></li>
        </ul>
    </div>
  <h1>Plasmid Management</h1>
</div>
<?php
//$project_menu = get_projects_menu("management");
//echo $project_menu['drop_down_no_submit'];
?>
<table class="plasmid_management_table" >
	<tr>
    	<th>Plasmid Backbones</th>
   		<th>Projects</th>
    	<th rowspan="2"><input type="button" value="Add New Backbone" onclick="document.getElementById('plasmid_list').value = 'nothing'; document.getElementById('projects_list').value = 'nothing'; document.getElementById('plasmid_map_display_box').innerHTML = ''; $info_display.update();" /></th>
    </tr>
    <tr>
<td>
            <select name="plasmid_list" id="plasmid_list" onChange="plasmid_focus(this.options[this.selectedIndex].value,'plasmids')">
            <option value='nothing' > -------------- Plasmids -------------- </option>
            <?php 
            $plasmids_query = mysql_query("SELECT * FROM plasmids WHERE user_id = ".$_COOKIE['user_id']." ORDER BY plasmid_name;");
            $plasmids_array = array();
            
            while ($row = mysql_fetch_assoc($plasmids_query))
                {
                array_push($plasmids_array,$row);	
                }
            $counter = 0;
            foreach($plasmids_array as $row)
                {
                echo "<option value='".$row['plasmid_id']."|".$row['plasmid_name']."'>".$row['plasmid_name']."</option>
				";
                $counter++;
                }
			
			echo "<option value='nothing' > --------- Popular Plasmids --------- </option>";
		
			$plasmids_query = mysql_query("SELECT * FROM plasmids WHERE privacy = 1 AND user_id != ".$_SESSION['user_id']." ORDER BY popularity DESC;");
			$plasmids_array = array();
			
			while ($row = mysql_fetch_assoc($plasmids_query))
				{
				array_push($plasmids_array,$row);	
				}
			
			$row_counter = 0;
			while ($counter <= 40 && isset($plasmids_array[$row_counter]))
				{
				$row = $plasmids_array[$row_counter];
				echo "<option value='".$row['plasmid_id']."|".$row['plasmid_name']."'>".$row['plasmid_name']."</option>";
				$counter++;	
				$row_counter++;
				}
            ?>
            </select>
        </td>    	
        <td>
        <form method='post' name='projects_form' id='projects_form' action='rf_cloning_project.php'>
          <select name='projects_list' id='projects_list' onChange="plasmid_focus(this.options[this.selectedIndex].value,'projects');">
            <option value='nothing' > --------Projects in progress-------- </option>
    
				<?php
                $plasmids_query = mysql_query("SELECT * FROM projects WHERE user_id = ".$_COOKIE['user_id']." AND complete = 0 ORDER BY plasmid_name;");
                $plasmids_array = array();	
                
                while ($row = mysql_fetch_assoc($plasmids_query))
                    {
                    array_push($plasmids_array,$row);	
                    }
                
                foreach($plasmids_array as $row)
                            {
                            echo "<option value='".$row['plasmid_id']."|".$row['plasmid_name']."'>".$row['plasmid_name']."</option>
                            ";
                            }
               
                echo "<option value='nothing' > ---------Completed projects--------- </option>
                ";
            
                $plasmids_query = mysql_query("SELECT * FROM projects WHERE user_id = ".$_COOKIE['user_id']." AND complete = 1 ORDER BY plasmid_name;");
                $plasmids_array = array();
                
                while ($row = mysql_fetch_assoc($plasmids_query))
                    {
                    array_push($plasmids_array,$row);	
                    }
            
                foreach($plasmids_array as $row)
                    {
                    echo "<option value='".$row['plasmid_id']."|".$row['plasmid_name']."'>".$row['plasmid_name']."</option>
                    ";
                    }
                ?>
          </select><input type='hidden' id='saved_submit' name='saved_submit' value='Get project' /></form>
   	  </td>
   	</tr>
</table>


<?php
/*************************************************************************************************
					At some point, I would like to integrate rf-cloning with the 
					AddGene database as presented through LabLife
					I might want to use SOAP if they support it
					
$lablife_file = file_get_contents("http://www.lablife.org/p?a=vdb&query=".$plasmid_search."&t_vdb_order=score");
*************************************************************************************************/
?>

<div id="plasmid_map_display_box" style="position:absolute; left:20px; top:200px;"></div>
<div id="plasmid_edit_div" style="position:absolute; left:625px; top:200px;"></div>
<div style="position:absolute; left:500px; top:900px;"><?php include("includes/footer.php"); ?></div>
</body>
</html>