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
include("functions/rev_comp.php");
include("functions/obj2array.php");
include("functions/numbered_triplets.php");
include("functions/rf_cloning_output.php");
include("functions/retrieve_saved.php");
include("functions/primer_info.php");
include("functions/pcr_conditions.php");
include("functions/get_projects_menu.php");
include("classes/Plasmid.php");
include("functions/melting_temp.php");


//instantiate a new plasmid object
$plasmid_obj = new Plasmid();
$error = "";

if(isset($_GET['proj_id']))
	{
	$query_string = "SELECT plasmid_id FROM projects WHERE proj_hash='".$_GET['proj_id']."';";
	try
		{
		$saved_project_query = mysql_query($query_string); 
		if (mysql_error())
			{
			throw new Exception(mysql_error());
			}
		}
	
	catch (Exception $e)
		{
		$error .= "Sorry, the project you are requesting doesn't seem to be in the database.<br />".$e."<br />";	
		}
	
	$saved_project_array = mysql_fetch_assoc($saved_project_query); 
	if(isset($saved_project_array['plasmid_id']))
		{
		$plasmid_obj->set_database_plasmid($saved_project_array['plasmid_id'],"projects");	
		$project_id = $plasmid_obj->get_parameters('plasmid_id');
		}
	
	else
		{
		$error .= "Sorry, project #".$_GET['proj_id']." has not been saved to the database. Please check the hash carefully and try again. If you feel like your projects are going missing, please get in touch with me (you can find my email address in the Q & A).<br />"; 	
		}
	}

//If the user is coming from the main page, starting a new project
elseif (isset($_POST['execute']))
	{
	$project_id = "new";
	$database = (empty($_POST['database'])) ? "blank" : $_POST['database'] ;
	$backbone_id = (empty($_POST['backbone_id'])) ? "blank" : $_POST['backbone_id'] ;
	$target_sequence = (empty($_POST['target_sequence'])) ? "blank" : $_POST['target_sequence'] ;
	$plasmid_sequence = (empty($_POST['plasmid_sequence'])) ? "blank" : $_POST['plasmid_sequence'] ;
	$backbone_name = (empty($_POST['backbone_name'])) ? "NotSet" : $_POST['backbone_name'] ;
	$insert_name = (empty($_POST['insert_name'])) ? "NotSet" : $_POST['insert_name'] ;
	$orientation = (empty($_POST['orientation'])) ? "cw" : $_POST['orientation'] ;
	$arrow = (empty($_POST['arrow'])) ? "on" : $_POST['arrow'] ;
	$insert_description = (empty($_POST['insert_description'])) ? "No Description" : $_POST['insert_description'] ;	
	$insert_sites = (is_numeric($_POST['insert_site_1']) && is_numeric($_POST['insert_site_2'])) ? round($_POST['insert_site_1'])."-".round($_POST['insert_site_2']) : false;
	$proj_hash = md5(rand(1,10000000)*rand(1,10000000));
	$plas_target_tm = $_POST['plasmid_target_Tm'];
	$ins_target_tm = $_POST['insert_target_Tm'];
	$plas_min_size = $_POST['plasmid_min_length'];
	$ins_min_size = $_POST['insert_min_length'];
	$plas_max_size = $_POST['plasmid_max_length'];
	$ins_max_size = $_POST['insert_max_length'];
	
	if ($target_sequence == "blank" || $plasmid_sequence == "blank")
		{
		$error .= "You need to include an insert sequence and a plasmid sequence for this to work.<br /><a href='index.php'>Return to main page</a><br />";
		
		}
		
	if ($orientation == "ccw")
		{
		$target_sequence = rev_comp($target_sequence);	
		}
		
	if ($backbone_id != "blank")
		{
		$incoming_plas_md5 = trim(preg_replace("/>.+[\r\n]/i","",$plasmid_sequence));
		$incoming_plas_md5 = md5(preg_replace("/[^ATUGCatugc]/i","",$incoming_plas_md5));	
		$backbone_md5 = mysql_fetch_row(mysql_query("SELECT checksum FROM plasmids WHERE plasmid_id = ".$backbone_id.";"));
		
		if($incoming_plas_md5 != $backbone_md5[0])
			{
			$backbone_id = "blank";
			}
		}
	
		$output_array = rf_cloning_output($target_sequence, $plasmid_sequence, $backbone_name, $insert_name, $orientation, $arrow, $insert_description, $database, $backbone_id, $insert_sites, $plas_target_tm, $ins_target_tm, $plas_min_size, $ins_min_size, $plas_max_size, $ins_max_size);
		
	if(isset($output_array['error']))
		{
		$error .=  $output_array['error']."<br />";
		}
		
	if($error == "")
		{
		$params_array = array( "user_id" => 1,"plasmid_id" => $project_id, "plasmid_name" => $output_array['plasmid_name'], "plasmid_seq" => $output_array["sequence"], "savvy_markers" => $output_array['savvy_markers'], "savvy_enzymes" => $output_array["savvy_enzymes"], "database" => "projects", "insert_name" => $output_array['insert_name'], "backbone_database" => $output_array['backbone_database'], "backbone_id" => $output_array['backbone_id'], "orig_plasmid_seq" => $output_array['plasmid_sequence'], "insert_seq" => $output_array['insert_sequence'], "insert_sites" => $output_array['insert_sites'], "fwd_primer" => $output_array['fwd_primer_database'], "rev_primer" => $output_array['rev_primer_database'], "notes" => $output_array['notes'], "savvy_meta" => $output_array['savvy_meta'], "complete" => $output_array['complete'], "proj_hash" => $proj_hash);	
		
		$plasmid_obj->set_parameters($params_array);
		}
	}
	
else
	{
	$error .=  "Hmmm... You should probably try getting here from the <a href='index.php'>main page</a>.<br />";	
	}
	
/**********************************Get all the other stuff sorted out********************************************/
if ($error == "")
	{
	$primer_info = primer_info($plasmid_obj->get_parameters("fwd_primer"),$plasmid_obj->get_parameters("rev_primer"),$plasmid_obj->get_parameters("insert_seq"));	
	$end_plas_size = strlen($plasmid_obj->get_parameters("plasmid_seq"));
	
	$pcr_conditions = pcr_conditions($end_plas_size,strlen($plasmid_obj->get_parameters("orig_plasmid_seq")),$primer_info['target_pcr_size']);
	
	$savvy_meta_array = explode("-",$plasmid_obj->get_parameters("savvy_meta"));
	}
/****************************************************************************************************************/

//Save the temp plasmid if coming from main page, and redirect to project using unique hash identifier
if ($error == "" && isset($_POST['execute']))
	{
	$plasmid_obj->save();
	//header('Location: http://www.rf-cloning.org/rf_cloning_project.php?proj_id='.$plasmid_obj->get_parameters('proj_hash'));
	header('Location: http://localhost/rf-cloning/rf_cloning_project.php?proj_id='.$plasmid_obj->get_parameters('proj_hash'));	
	exit();	
	}




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="icon" type="image/ico" href="favicon.ico" />
<meta charset="utf-8" />
<title>RF Cloning Project</title>
<script src="classes/ajaxObj.js" language="javascript" type="text/javascript"></script>
<script src="classes/raphael_uncompressed.js" language="javascript" type="text/javascript"></script>
<script src="javascript/ajax.js" language="javascript" type="text/javascript"></script>
<script src="javascript/project.js" language="javascript" type="text/javascript"></script>
<script src="javascript/javascripts.js" language="javascript" type="text/javascript"></script>
<script language="javascript" type="text/javascript">
function complete()
	{
	$complete_click.update("project_id=" + document.getElementById('project_id').value + "&complete_check=1","POST");
	}

function incomplete()
	{
	$complete_click.update("project_id=" + document.getElementById('project_id').value + "&complete_check=0","POST");		
	}
</script>       
<link rel="stylesheet" href="includes/styles.css" />


</head>
<body onMouseMove="mousepos(event)">
<div id='help_overlay' onclick="clear_help()"></div>
<div id='help_text' onclick="clear_help()"></div>
<div id='close_help' onclick="clear_help()"></div>
<input type="hidden" id="page" name="page" value="project" />
<input type="hidden" id="autosave" name="autosave" <?php print (($plasmid_obj->get_parameters('user_id') == 1) ? "value='true'" : "value='false'"); ?>/>
<?php echo "<input type='hidden' id='user_id' value=".$plasmid_obj->get_parameters('user_id')." />"; ?>

<!-- the following DIV is an absolute, set at 0,0. The svg generated on a 'Draw Plasmid' click will create a white box over the left of the page, and draw the construct on that part of the canvas -->
<div id="plasmid_map_display_box" onclick="begindrag(event)" style="cursor:move;"></div>
<h1>Restriction Free Cloning</h1>
<?php
if($error != "")
	{
	echo $error."</body></html>";
	exit();	
	}

?>
<div  id='left_body_column'>
    <u>Construct Name</u><br />
    <?php echo $plasmid_obj->get_parameters("plasmid_name"); ?><br /><br />
    <div id="primer_constraint"><u>Forward Primer</u><br />
    Plasmid annealing = <span id='fwd_plas_tm' class="numbers"><?php echo round($primer_info['fwd_plas_tm']); ?>&deg;C</span> &nbsp;&nbsp;&nbsp; Target annealing = <span id='fwd_ins_tm' class="numbers"><?php echo round($primer_info['fwd_ins_tm']); ?>&deg;C</span> &nbsp;&nbsp;&nbsp;Length = <span id='fwd_prim_len' class="numbers"><?php echo $primer_info['fwd_primer_length']; ?></span>
    <div>
        <div class='bp_control_buttons_left'> 
          <img class='add_base_rev' onclick="add_basepair(2);"/>
          <img class='sub_base_rev' onclick="sub_basepair(2);"/>
      	</div>    
        <span class='primer' id='fwd_primer'><?php echo $primer_info['fwd_primer']; ?></span>
        <div class='bp_control_buttons_right'>
          <img class='sub_base_fwd' onclick="sub_basepair(1);"/>
          <img class='add_base_fwd' onclick="add_basepair(1);"/>
        </div>
    </div>
    <br />
    <br />
    <u>Reverse Primer</u><br />
    Plasmid annealing = <span id='rev_plas_tm' class="numbers"><?php echo round($primer_info['rev_plas_tm']); ?>&deg;C</span> &nbsp;&nbsp;&nbsp; Target annealing = <span id='rev_ins_tm' class="numbers"><?php echo round($primer_info['rev_ins_tm']); ?>&deg;C</span> &nbsp;&nbsp;&nbsp;Length =  <span id='rev_prim_len' class="numbers"><?php echo $primer_info['rev_primer_length']; ?></span>
    
    <div id="rev_primer_div">
        <div class='bp_control_buttons_left'>
          <img class='add_base_rev' onclick="add_basepair(4);"/>
          <img class='sub_base_rev' onclick="sub_basepair(4);"/>
        </div>
         <span class='primer' id='rev_primer'><?php echo $primer_info['rev_primer']; ?></span>
        
        <div class='bp_control_buttons_right'>
          <img class='sub_base_fwd' onclick="sub_basepair(3);"/>
          <img class='add_base_fwd' onclick="add_basepair(3);"/>
        </div>                   
    </div>
    <br />
    
    <div id="no_first_pcr" style="width:483px;"><?php echo $primer_info['no_first_pcr']; ?></div>
    <table cellpadding=4 style="float:left;">
      <tr>
            <td width="98"><u>1&deg; PCR  Size</u></td>
            <td width="115"><u>New Plasmid Size</u></td>
            <td width="136" valign="top" align="center"><u>Insert Sites</u></td>
            <td width="98" style="padding-left:15px;"><u>Insert Size</u></td>
      </tr>
        <tr>
            <td class="numbers"><span id='pcr_prod_size'><?php echo $primer_info['target_pcr_size']; ?></span>bps</td>
            <td class="numbers"><span id="new_size_span"><?php echo $end_plas_size; ?></span>bps</td>
            <td align="center"  class="numbers" style="margin:0px; padding:0px;">
                <div class='bp_control_buttons_left' style="width:28px;">
                    <img class='add_base_rev' onclick="shift_insert(1); "/>
                    <img class='sub_base_rev' onclick="shift_insert(2); "/>
                </div>
                <span id='insert_sites_span'><?php echo $plasmid_obj->get_parameters("insert_sites"); ?></span>
                <div class='bp_control_buttons_right' style="width:28px;"> 
                    <img class='add_base_rev' onclick="shift_insert(3); "/>
                    <img class='sub_base_rev' onclick="shift_insert(4); "/>
                </div></td>
            <td style="padding-left:15px;" class="numbers"><?php echo strlen($plasmid_obj->get_parameters("insert_seq")); ?>bps</td>
        </tr>
    </table>				
    <span id="insert_shift_alert"></span>
    <br />
    <table>
        <tr>
            <td colspan='2'><b>2&deg; PCR conditions</b></td>
        </tr>
        <tr>
            <td><u>Extension Time</u></td>
            <td><u>ng of insert</u></td>
            <td><u>ng of plasmid</u></td>
        </tr>
        <tr>
            <td><span class="numbers" id="extension_time"><?php echo $pcr_conditions['extension_time_mins'] ?></span> mins</td>
            <td><span class="numbers" id="ng_insert"><?php echo round($pcr_conditions['ng_of_insert'],1); ?></span></td>
            <td><span class="numbers" id="ng_plasmid"><?php echo round($pcr_conditions['ng_of_plasmid'],1); ?></span></td>
        </tr>
    </table>
    <br />
    <u>New construct</u> 
    <br />
    <span style='background-color:#09F;border:solid black thin;font-size:9pt;color:#09F;'>ATGC</span> = Original plasmid sequence. <span style='background-color:#6F3;border:solid black thin;font-size:9pt;color:#6F3;'>ATGC</span> = Inserted sequence.
    <br /><br />
    <div id="new_construct">
    <?php echo $plasmid_obj->build_construct(); ?></div>
</div>
    
    <br />

<table>
<tr>
    <td>
<form method="post" action="index.php">
<input type="submit" value="Return" />
</form>
    </td>
    <td>
<input type="hidden" id="project_id" value="<?php echo $project_id; ?>" />
<input type="hidden" id="fwd_primer_database" value="<?php echo $plasmid_obj->get_parameters("fwd_primer"); ?>" />
<input type="hidden" id="rev_primer_database" value="<?php echo $plasmid_obj->get_parameters("rev_primer"); ?>" />
<input type="hidden" id="insert_sites" value="<?php echo $plasmid_obj->get_parameters("insert_sites"); ?>" />
<input type="hidden" id="backbone_id" value="<?php echo $plasmid_obj->get_parameters("backbone_id"); ?>" />
<input type="hidden" id="backbone_name" value="<?php echo $plasmid_obj->get_parameters("backbone_name"); ?>" />
<input type="hidden" id="backbone_database" value="<?php echo $plasmid_obj->get_parameters("backbone_database"); ?>" />
<input type="hidden" id="sequence" value="<?php echo $plasmid_obj->get_parameters("plasmid_seq"); ?>" />
<input type="hidden" id="plasmid_sequence" value="<?php echo $plasmid_obj->get_parameters("orig_plasmid_seq"); ?>" />
<input type="hidden" id="insert_sequence" value="<?php echo $plasmid_obj->get_parameters("insert_seq"); ?>" />
<input type="hidden" id="new_size" value="<?php echo strlen($plasmid_obj->get_parameters("plasmid_seq")); ?>" />
<input type="hidden" id="complete" value="<?php echo $plasmid_obj->get_parameters("complete"); ?>" />
<input type="hidden" id="savvy_meta" value="<?php echo $plasmid_obj->get_parameters("savvy_meta"); ?>" />
<input type="hidden" id="arrow" value="<?php echo $savvy_meta_array[1]; ?>" />
<input type="hidden" id="orientation" value="<?php echo $savvy_meta_array[0]; ?>" />


<input type="button" name='save' id='save' value="Save" onclick="save_project();" <?php echo ($login_status == "false") ? "disabled='disabled'" : ""; ?>/>
    </td>
        <td>
        <div id="saved_alert"></div>
        </td>
    </tr>	
</table>
<div id='right_column'>
        <u><?php echo date("M d Y"); ?></u><br /><br />
    <div class="tabs" style="margin-right:100px;">
        <ul>
            <li><a href="index.php"><span>Home</span></a></li>
            <?php if($login_status == "true") echo "<li><a href='plasmid_management.php'><span>Manage plasmids</span></a></li>"; ?>
            <li><a href='savvy.php'><span>Savvy</span></a></li>
            <li><a href='QandA.php' target="_blank"><span>Q & A</span></a></li>
            <li><a href='soap_server.php'><span>SOAP</span></a></li>
            <li><a href="login.php"><span><?php if($login_status == "true") echo "Log out"; else echo "Log in/Register";  ?></span></a></li>
        </ul>
    </div>
    <br /><br />
        <h3>Plasmid markers and restriction enzymes:</h3>			
        <form name="savvy_form" action="/cgi-bin/savvy.cgi" target="_blank" method="post">					
            <textarea name='markers' id='markers' rows='10' cols='50' onfocus='clear_save_alert()' onkeypress="activate_save();"><?php echo $plasmid_obj->get_parameters("savvy_markers"); ?></textarea> <img class='help' src="images/help.png" onclick="help_file(5);" /><br />
            <input type="button" onclick="	document.getElementById('markers').value = 'Blasting Sequence...';
            								activate_save();
                                            var $sequence = document.getElementById('sequence').value;
                                            var $params = 'sequence=' + $sequence;
                                            $blast_features.update($params,'POST')" id="blast_features" name="blast_features" value="Auto Find Markers" />
            <br /><br />
            <textarea name='enzymes' id='enzymes'rows='2' cols='50' onfocus='clear_save_alert()' onkeypress="activate_save();"><?php echo $plasmid_obj->get_parameters("savvy_enzymes"); ?></textarea> <img class='help' src="images/help.png" onclick="help_file(6);" /><br />
            <input type='hidden' name='plasmid_name' id='plasmid_name' value='<?php echo $plasmid_obj->get_parameters("plasmid_name"); ?>' />
            <input type='hidden' name="insert_name" id="insert_name" value='<?php echo $plasmid_obj->get_parameters("insert_name"); ?>' />
            <input type='hidden' name='line_thickness' id='line_thickness' value='0.5' />
            <input type='hidden' name='shape' id='shape' value='circular' />
            <input type='hidden' name='plasmid_size' id='plasmid_size' value=<?php echo $end_plas_size; ?> />
            <input type="button" onclick="	document.getElementById('enzymes').value = 'Digesting...';
            								activate_save();
                                            var $sequence = document.getElementById('sequence').value;
                                            var $cut_num = document.getElementById('cut_num').value;
                                            var $params = 'sequence=' + $sequence + '&cut_num=' + $cut_num;
                                            $restriction_digest.update($params,'POST')" value="digest"/>
            <select name='cut_num' id='cut_num'>
                <option value="1">1 cutters</option>
                <option value="2">1 & 2 cutters</option>
                <option value="3"><= 3 cutters</option>
                <option value="4">All sites</option>
            </select><br />
            <input type="button" id='draw_plasmid_button' onclick="draw_plas_dis_box();" value="Draw Plasmid" style="margin-top:4px;" />
            <input type="button" id='export_gbfile' onclick='gbfile()' value='Export' style="margin-top:4px;" />
            <input type="button" id="align_result" onclick="prep_alignment()" value="Align Sequencing Results" style="float:right;margin-top:4px;" />
        </form>
    
    <br />
    <div>Add notes here<br />
<textarea name='notes' id='notes' cols='50' rows='10' value='<?php echo $plasmid_obj->get_parameters("notes"); ?>' onfocus='clear_save_alert()' onkeypress="activate_save();"><?php echo $plasmid_obj->get_parameters("notes"); ?></textarea>
</div><br />
<div style='position:absolute; left:450px; top:150px;'>											
        <?php if ($login_status == "true"){ ?>
        <input type='button' id='project_complete' value='Complete' <?php print (($plasmid_obj->get_parameters("complete") == 0) && ($plasmid_obj->get_parameters('user_id') != 1) ? "" : "disabled=disabled"); ?> onclick="complete();" />
        <input type='button' id='project_incomplete' value='Incomplete' <?php  print (($plasmid_obj->get_parameters("complete") == 1) && ($plasmid_obj->get_parameters('user_id') != 1) ? "" : "disabled=disabled"); ?> onclick="incomplete();"> <img class='help' src="images/help.png" onclick="help_file(7);" style="float:right" />
        <?php } ?>
    </div>
    </div>
<input type="hidden" id="svg_file_ref" value="not_set" />
<input type="hidden" id="plasmid_obj" value='<?php $json_obj = json_encode(obj2array($plasmid_obj)); echo $json_obj;?>' />

<form name='gbfile_form' action='functions/export_gbfile.php' target="_blank" method="post">
    <input type="hidden" id="marker_export" name="marker_export" value="marker" />
    <input type="hidden" id="sequence_export" name="sequence_export" value="sequence" />
    <input type="hidden" id="length_export" name="length_export" value="length" />
    <input type="hidden" id="name_export" name="name_export" value="name" />
</form>
<?php include("includes/footer.php"); ?>
</body></html>	
   