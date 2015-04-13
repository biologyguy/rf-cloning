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
/*POST variables accepted: id, database
if no id given, generate a blank form
*/
require_once('../../../includes/rf-cloning/db_connect.php');
require_once("../../classes/Plasmid.php");
include("../obj2array.php");
$login_status = "false";

if(isset($_COOKIE['user_id']))
	{
	$user_info = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE user_id='".$_COOKIE['user_id']."' ;"));
	if ($user_info['session_check'] == $_COOKIE['session_check'])
		{
		$login_status = "true";
		}	
	}

if ($login_status == "false")
	{
	exit;	
	}

$plasmid_obj = new Plasmid();

if(isset($_POST['id']))
	{
	$plasmid_obj->set_database_plasmid($_POST['id'],$_POST['database']);
	$clear = "";
	$save_disable = $plasmid_obj->get_parameters('user_id') != $_COOKIE['user_id'] ? "disabled='disabled' " : "";
	$delete = "<input type='button' onclick=\"delete_backbone(".$_POST['id'].", '".$_POST['database']."');\" value='Delete' ".$save_disable." />";
	$project_options = ($_POST['database'] == 'projects') ? " <input type='button' value='Get project' onclick='get_project()' /> <input type='button' value='Convert to Backbone' onclick='confirm_new_backbone()' />
	<input type='hidden' id='proj_hash' name='proj_hash' value='".$plasmid_obj->get_parameters('proj_hash')."' />" : "";
	}

else
	{	
	$params_array = array( "plasmid_id" => "new", "database" => "plasmids", "plasmid_name" => "Unnamed");
	$plasmid_obj->set_parameters($params_array);
	
	$clear = "onfocus='clearDefault(this);'";
	$save_disable = "";
	$delete = "";
	$project_options = "";
	}
	
	
?>

<form name='savvy_form' action='/cgi-bin/savvy.cgi' target='_blank' method='post'>
    Name:<br />
    <input type='text' name='plasmid_name' id='plasmid_name' size="50" value=<?php echo "'".$plasmid_obj->get_parameters('plasmid_name')."' ".$clear; ?> /><br />
    Markers:<br />
    <textarea name='markers' id='markers' rows=8 cols=90 style='font-size:9pt;' onchange='disable_save()'><?php echo $plasmid_obj->get_parameters("savvy_markers"); ?></textarea><br />
        
    
    <table border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table>
                    <tr>
                        <td colspan="2"><span><b>Name: </b></span>
                        <input type="text" name="add_marker_name" id="add_marker_name" size="10" /></td>
                        <td colspan="2"><span><b>   Start: </b></span>
                        <input type="text" name="add_marker_start" id="add_marker_start" size="10" /></td>
                        <td colspan="2"><span>  <b>Finish: </b></span>
                        <input type="text" name="add_marker_end" id="add_marker_end" size="10" /></td>
                        <td><input type="checkbox" checked name="add_marker_arrow" id="add_marker_arrow" value="arrow_on" /><span> <b>Arrow on</b></span></td>
                   		<td  style="padding-left:35px;"><input type="button" onclick="	document.getElementById('markers').value = 'Blasting Sequence...';
                                                                                        var $sequence = document.getElementById('plasmid_sequence').value;
                                                                                        var $params = 'sequence=' + $sequence;
                                                                                        $blast_features.update($params,'POST')" id="blast_features" name="blast_features" value="Auto Find" /></td>
                    </tr>
                    <tr>
                    	<td><span><b>Marker style:</b></span></td>
                        <td>
                            <select name="add_marker_style" id="add_marker_style" size="1">
                            <option value="Filled">Filled</option>
                            <option value="Open">Open</option>
                            </select>
                       	</td>
                        <td><span> <b>Fill-color:</b></span></td>
                        <td>
                            <select name="add_marker_fill" id="add_marker_fill" size="1">
                            <option value="Aqua">Aqua</option>
                            <option value="Black">Black</option>
                            <option value="Blue">Blue</option>
                            <option value="Brown">Brown</option>
                            <option value="Gray">Gray</option>
                            <option value="Green">Green</option>
                            <option value="Lime">Lime</option>
                            <option value="Maroon">Maroon</option>
                            <option value="Navy">Navy</option>
                            <option value="Olive">Olive</option>
                            <option value="Orange">Orange</option>
                            <option value="Pink">Pink</option>
                            <option value="Purple">Purple</option>
                            <option selected value="Red">Red</option>
                            <option value="Silver">Silver</option>
                            <option value="Tan">Tan</option>
                            <option value="Teal">Teal</option>
                            <option value="Yellow">Yellow</option>
                            </select>
                        </td>
                        <td><span><b>Thickness: </b></span></td>
                        <td><input type="text" name="add_marker_thickness" id="add_marker_thickness" size="5" value="12" /></td>
                        <td><input type="button" name="add_to_marker_list" id="add_to_marker_list" value="Add to list" onClick="add_to_marker()" /></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table><br />
	Enzymes:<br />
	<textarea name='enzymes' id='enzymes'rows=2 cols=90 style='font-size:9pt;' onchange='disable_save()'><?php echo $plasmid_obj->get_parameters('savvy_enzymes'); ?></textarea><br />
    <input type="button" onclick="	document.getElementById('enzymes').value = 'Digesting...';
    								var $sequence = document.getElementById('plasmid_sequence').value;
    								var $cut_num = document.getElementById('cut_num').value;
                                    var $params = 'sequence=' + $sequence + '&cut_num=' + $cut_num;
    								$restriction_digest.update($params,'POST')" value="digest"/>
    <select name='cut_num' id='cut_num'>
    	<option value="1">1 cutters</option>
        <option value="2">1 & 2 cutters</option>
        <option value="3"><= 3 cutters</option>
        <option value="4">All sites</option>
    </select><br /><br />
   <b>Name: </b><input type="text" name="add_enzyme_name" id="add_enzyme_name" size="10" /> 
    <b>Position: </b><input type="text" name="add_enzyme_position" id="add_enzyme_position" size="10" /> 
    <input type="button" name="add_to_enzyme_list" value="Add to list" onClick="add_enzyme()" /><br /><br />
	Sequence:<br />
	<textarea name='plasmid_sequence' id='plasmid_sequence' rows=8 cols=90 wrap='no' style='font-size:9pt;' onchange='disable_save()'></textarea><br />
	<input type='button' id='redraw' onclick='redrawing()' value='Redraw' />
	<input type='button' id='save_edits' onclick="saveEdits()" value='Save' <?php echo $save_disable; ?>/>
    <?php echo $delete.$project_options; ?>
    <input type="button" id='export_gbfile' onclick='gbfile()' value='Export' /><br />
    <span style="margin:5px 0px 0px 2px;"><input type="checkbox" id='privacy' name="privacy" value=1 style="vertical-align: middle; margin: 0px;" <?php echo ($plasmid_obj->get_parameters('privacy') == 0) ? "" : "checked='checked' "; ?>/><span style="font-size:10px; vertical-align:middle; margin: 0px;"> Make backbone publically viewable?</span></span><br />
    <input type='hidden' name='plasmid_size' id='plasmid_size' value='' />
    <input type='hidden' name='line_thickness' id='line_thickness' value='0.5' />
	<input type='hidden' name='shape' id='shape' value='circular' />
    <input type='hidden' name='database' id='database' value='<?php echo $plasmid_obj->get_parameters("database"); ?>' />
    <input type='hidden' name='project_id' id='project_id' value='<?php echo $plasmid_obj->get_parameters("plasmid_id"); ?>' />
	<div id='saved_alert'></div>
</form>

<form name='gbfile_form' action='functions/export_gbfile.php' target="_blank" method="post">
<input type="hidden" id="marker_export" name="marker_export" value="marker" />
<input type="hidden" id="sequence_export" name="sequence_export" value="sequence" />
<input type="hidden" id="length_export" name="length_export" value="length" />
<input type="hidden" id="name_export" name="name_export" value="name" />
</form>
<input type="hidden" id="plasmid_obj" value='<?php $json_obj = json_encode(obj2array($plasmid_obj)); echo $json_obj;?>' />