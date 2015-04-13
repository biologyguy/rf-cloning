// JavaScript Document
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

var $draw_plasmid = new ajaxObject("/cgi-bin/savvy.cgi",draw_plasmid);
	
function draw_plasmid(responseText, responseStatus, responseXML)
	{
	if (responseStatus == 200)
		{			
		document.getElementById('plasmid_map_display_box').innerHTML = "";		
		
		//plasmid_map_display_box.rect(0, 0, 600, 600).attr({"fill":"white", "stroke":"none"});
		svg_plasmid_map_display_box = new Raphael("plasmid_map_display_box", 600, 600); //GLOBAL
		
		var $params = "svg_text=" + responseText + "&div_id=plasmid_map_display_box&width=600&height=600";
		$svg2raphael.update($params,"POST");
		
		//print button
		svg_plasmid_map_display_box.rect(354.643, 446.396, 32.158, 24).attr({"fill":"#76CFE4", "stroke":"#000000", "stroke-width":"2", "stroke-miterlimit":"10"});
		svg_plasmid_map_display_box.path("M 384.17 467.204 L 379.858 467.204 L 379.357 464.871 L 362.774 464.871 L 362.107 467.204 L 357.857 467.204 L 357.857 457.017 L 384.17 457.017Z").attr({"fill":"#8D9596", "stroke":"#000000", "stroke-miterlimit":"10"});
		svg_plasmid_map_display_box.path("M 377.982 456.642 L 365.363 456.642 L 365.363 448.579 L 374.536 448.579 L 377.982 451.501Z").attr({"fill":"#F5FBFC", "stroke":"#000000", "stroke-miterlimit":"10"});
		svg_plasmid_map_display_box.path("M 377.4 451.954 L 373.955 451.954 L 373.955 449 Z");
		svg_plasmid_map_display_box.path("M 367.795 451.954 L 372.92 451.954").attr({"fill":"none", "stroke":"#231F20", "stroke-width":"0.5", "stroke-miterlimit":"10"});
		svg_plasmid_map_display_box.path("M 367.795 453.517 L 372.92 453.517").attr({"fill":"none", "stroke":"#231F20", "stroke-width":"0.5", "stroke-miterlimit":"10"});
		svg_plasmid_map_display_box.path("M 367.795 455.017 L 372.92 455.017").attr({"fill":"none", "stroke":"#231F20", "stroke-width":"0.5", "stroke-miterlimit":"10"});
		svg_plasmid_map_display_box.ellipse(378.061, 459.579, 1.047, 1.031).attr({"fill":"#8D9596", "stroke":"#000000", "stroke-width":"0.5", "stroke-miterlimit":"10"});
		svg_plasmid_map_display_box.ellipse(381.467, 459.923, 1.391, 1.375).attr({"fill":"#8D9596", "stroke":"#000000", "stroke-width":"0.5", "stroke-miterlimit":"10"});
		var $print = svg_plasmid_map_display_box.rect(354.643, 446.396, 32.158, 24).attr({"fill":"#000000", "fill-opacity":"0.01", "stroke":"#000000", "stroke-width":"2", "stroke-miterlimit":"10"});
		$print.click(function() 
						{
						var $plas_sequence = document.getElementById('plasmid_sequence').value.replace(/>.+/g,"");
						$plas_sequence = $plas_sequence.replace(/[^ATUCGatucg]/g,"");
						document.getElementById('plasmid_size').value = $plas_sequence.length; 	
						document.savvy_form.submit()
						});
		}
	}	
/**************************************************************************/
var $info_display = new ajaxObject("functions/ajax/info_display.php",info_display);
function info_display(responseText, responseStatus)
	{	
	if(responseStatus == 200)
		{		
		document.getElementById('plasmid_edit_div').innerHTML = responseText; 
		if (document.getElementById('project_id').value != "new")
			{
			var $id = document.getElementById('project_id').value;
			var $database = document.getElementById('database').value;
			$get_plasmid_sequence.update('id=' + $id + '&database=' + $database,'POST');
			}
		}
	}

/************************************************************************************************/
function saveEdits()
	{
	var $js_plasmid = JSON.parse(document.getElementById('plasmid_obj').value); 
	
	var $plas_sequence = document.getElementById('plasmid_sequence').value.replace(/[^ATUCGatucg]/g,"");
	$plas_sequence = $plas_sequence.toUpperCase();
	
	$js_plasmid['plasmid_seq'] = $plas_sequence;
	if((md5($plas_sequence) != $js_plasmid['checksum']) && (document.getElementById('project_id').value != "new"))
		{
		var $confirm = 	confirm('You are about to modify the plasmid sequence. Plasmid feature positions will not be automatically updated, so you may want to manually make the changes or use the auto-find functions before saving. Do you wish to continue?');
		}
	else
		{
		var $confirm = true;
		}
	
	if ($confirm == true)
		{			
		$js_plasmid['plasmid_name'] = trim(document.getElementById('plasmid_name').value.replace(/["'&;\t\\]/g,""));
		$js_plasmid['savvy_markers'] = trim(document.getElementById('markers').value.replace(/["'&;\t\\]/g,""));
		$js_plasmid['savvy_markers'] = $js_plasmid['savvy_markers'].replace(/\n+/g,"\\n");
		$js_plasmid['savvy_enzymes'] = trim(document.getElementById('enzymes').value.replace(/["'&<>(){};\r\t\n\\]/g,""));
		$js_plasmid['privacy'] = (document.getElementById('privacy').checked) ? 1 : 0;
			
		$post_parameters = 'plasmid_obj='+JSON.stringify($js_plasmid);
		$save_project.update($post_parameters,'POST');	
		}
	}

/**************************************************************************/
function draw_plas_dis_box($id, $database)
	{
	document.getElementById('plasmid_map_display_box').innerHTML = "<div style='height:600px; width:490px; text-align:center; margin-top:150px;'><img src='images/loading.gif' /></div>";	
	var $edit_div_content = "";	
	
	//return either the project or backbone dropdown list to null, depending on which one just selected	
	if ($database == 'plasmids')
		{ 
		document.getElementById('projects_list').value = 'nothing';		
		$info_display.update("id="+$id+"&database=plasmids","POST")
		}
		
	if ($database == 'projects')
		{
		document.getElementById('plasmid_list').value = 'nothing';		
		$info_display.update("id="+$id+"&database=projects","POST")
		}	
	}
/**************************************************************************/
var $delete_backbone = new ajaxObject("functions/ajax/delete_backbone.php",delete_backbone_ajax);
function delete_backbone($plasmid_id, $database)
	{
	var $confirm = ($database == "plasmids") ? confirm("Are you sure you would like to delete this backbone? \r The action cannot be undone.") : confirm("Are you sure you would like to delete this project from your profile? \r It will still be accessible at www.rf-cloning.org/rf_cloning_project.php?proj_id=" + document.getElementById('proj_hash').value); 
	if ($confirm == true)
		{
		var $user_id = getCookie("user_id");
		var $session_check = getCookie("session_check");
		var plasmid_id = document.getElementById('plasmid_list').value;
		var $paramaters = 'user_id=' + $user_id + '&session_check=' + $session_check + '&plasmid_id=' + $plasmid_id + '&database=' + $database;
		$delete_backbone.update($paramaters,'POST'); 
		}
	}

function delete_backbone_ajax(responseText, responseStatus)
	{
	if(responseStatus == 200)
		{
		if (responseText != "")
			{
			document.getElementById('saved_alert').innerHTML = responseText; 	
			}
		else
			{
			location.reload(true); 
			}
		}	
	}
/**************************************************************************/
function get_project()
	{
	var $proj_hash = document.getElementById('proj_hash').value;
	document.getElementById('projects_form').action = "http://localhost/rf-cloning/rf_cloning_project.php?proj_id=" + $proj_hash; 
	//document.getElementById('projects_form').action = "http://www.rf-cloning.org/rf_cloning_project.php?proj_id=" + $proj_hash; 
	document.projects_form.submit();
	}

/******************************** Adapted from Savvy ******************************************/	
function add_to_marker()
	{
	var name = document.getElementById('add_marker_name').value;
	var start = document.getElementById('add_marker_start').value;
	var end = document.getElementById('add_marker_end').value;
	var arrow = "";
	
	if(document.getElementById('add_marker_arrow').checked)
		{
		arrow = document.getElementById('add_marker_arrow').value;
		}
	else
		{
		//alert("Inside arrow off");
		arrow = "arrow_off";
		}
	
	var style = document.getElementById('add_marker_style').value;
	var fill = document.getElementById('add_marker_fill').value;
	var thickness = document.getElementById('add_marker_thickness').value;
		
	var s = document.getElementById('markers').value.replace(/^\s+|\s+$/g,"");  
		s += "\n" + name + " " + start + " " + end + " " + arrow + " " + style + " " + fill + " " + thickness;
	document.getElementById('markers').value = s;		
	}


function add_enzyme() {
	var p = document.getElementById('add_enzyme_position').value;
	var s 	 = 	document.getElementById('enzymes').value.replace(/^\s+|\s+$/g,"");
		s	+=	document.getElementById('add_enzyme_name').value + " ";
		s	+=	p + ":" ;
		
	document.getElementById('enzymes').value = s;
	document.getElementById('add_enzyme_position').value = "";
	document.getElementById('add_enzyme_name').value     = "";		
	}	
/**************************************************************************/
function new_backbone()
	{
	var $sequence = document.getElementById('plasmid_sequence').value.replace(/>.+/g,'');
	$sequence = $sequence.toUpperCase();
	$sequence = $sequence.replace(/[^ATUGC]/g,'');
	var $markers = document.getElementById('markers').value.replace(/["'&<>(){};\t\\]/g,'');
	$markers = $markers.replace(/\n+/g,"\\n");
	var $enzymes = document.getElementById('enzymes').value.replace(/["'&<>(){};\t\n\r\\]/g,'');
	var $plasmid_name = document.getElementById('plasmid_name').value.replace(/["'&<>(){};\s\\]/g,'');
	if ($plasmid_name == "")
		{ 
		alert('Please give your new plasmid a name!');
		return;
		}
	var $privacy = document.getElementById('privacy').checked ? 1 : 0;
	var $user_id = getCookie("user_id");
	var $session_check = getCookie("session_check");
	var $post_parameters = 'markers='+$markers+'&enzymes='+$enzymes+'&plasmid_name='+$plasmid_name+'&sequence='+$sequence+'&session_check='+$session_check+'&user_id='+$user_id + '&privacy=' + $privacy;
	$add_backbone.update($post_parameters,'POST');
	}

/**************************************************************************/
function confirm_new_backbone()
	{
	var $confirm = confirm('Are you sure you would like to add this backbone to your profile?'); 
	if ($confirm == true)
		{
		var $user_id = getCookie("user_id");
		var $session_check = getCookie("session_check");
		var $plasmid_id = document.getElementById('projects_list').value;
		var $privacy = document.getElementById('privacy').checked ? 1 : 0;
		var $paramaters = 'user_id=' + $user_id + '&session_check=' + $session_check + '&plasmid_id=' + $plasmid_id + '&privacy=' + $privacy;
		$add_backbone.update($paramaters,'POST');
		}
	}
	
/**************************************************************************/
var $add_backbone = new ajaxObject("functions/ajax/add_backbone.php",add_backbone);
function add_backbone(responseText, responseStatus)
	{
	if(responseStatus == 200)
		{
		if (responseText != "")
			{
			document.getElementById('saved_alert').innerHTML = responseText; 	
			}
		else
			{
			location.reload(true); 
			} 
		}
	}

/**************************************************************************/
function redrawing()
	{	
	setTimeout(function(){document.getElementById('plasmid_map_display_box').innerHTML = "<div style='height:600px; width:490px; text-align:center; margin-top:150px;'><img src='images/loading.gif' /></div>";},1);
	var $sequence = document.getElementById('plasmid_sequence').value.replace(/>.+/g,'');
	$sequence = $sequence.replace(/[^ATUGCatugc]/g,'');
	var $markers = document.getElementById('markers').value;
	var $enzymes = document.getElementById('enzymes').value;
	var $plasmid_name = document.getElementById('plasmid_name').value; 
	var $line_thickness = "0.5";
	var $shape = "circular";
	var $plasmid_size = $sequence.length;
	$post_parameters = 'markers='+$markers+'&enzymes='+$enzymes+'&plasmid_name='+$plasmid_name+'&line_thickness='+$line_thickness+'&shape='+$shape+'&plasmid_size='+$plasmid_size;
	
	setTimeout(function(){$draw_plasmid.update($post_parameters, 'POST')},200);
	
	}

/*************************************************************************/
function gbfile()
	{
	var $plas_sequence = document.getElementById('plasmid_sequence').value.replace(/>.+/g,"");
	$plas_sequence = $plas_sequence.toUpperCase();
	$plas_sequence = $plas_sequence.replace(/[^ATUCG]/g,"");
	document.getElementById('sequence_export').value = $plas_sequence; 
	document.getElementById('length_export').value = $plas_sequence.length;
	document.getElementById('marker_export').value = document.getElementById('markers').value;
	document.getElementById('name_export').value = document.getElementById('plasmid_name').value;
	document.gbfile_form.submit()	
	}
	
/************************************************************************/
function clean_up($id, $backbone_name)
	{

	}
	
/************************************************************************/
function disable_save()
	{
	document.getElementById('saved_alert').innerHTML = "";	
	}
/************************************************************************/
var $savvy_info = new ajaxObject("functions/ajax/savvy_info.php",savvy_info);
	
function savvy_info(responseText, responseStatus)
	{
	if(responseStatus == 200)
		{
		var $response = JSON.parse(responseText);
		document.getElementById('markers').value = $response['markers'];
		document.getElementById('enzymes').value = $response['enzymes'];
		var $plasmid_info_array = document.getElementById('plasmid_list').value.split("|");
		document.getElementById('plasmid_name').value = $plasmid_info_array[1];
		$get_plasmid_sequence.update('id=' + $response['plasmid_id'] + '&database=plasmids','POST');	
		}
	}

/*************************************************************************/
function save_backbone_edit($database)
	{
	var $confirm = confirm('Are you sure you want to make these changes?'); 
	if ($confirm == true)
		{	
		var $save_backbone_changes = new ajaxObject("functions/ajax/save_backbone_changes.php",function(responseText,responseStatus){if(responseStatus == 200){document.getElementById('saved_alert').innerHTML = responseText;}});
		
		if($database == 'projects')
			{
			var $plasmid_info = document.getElementById('projects_list').value.split("|");	
			}
		else
			{
			var $plasmid_info = document.getElementById('plasmid_list').value.split("|");	
			}
		var $plasmid_id = $plasmid_info[0];
		var $plasmid_name = document.getElementById('plasmid_name').value;
		var $markers = trim(document.getElementById('markers').value.replace(/["'&<>(){};\t\\]/g,""));
		$markers = $markers.replace(/\n+/g,"\\n");
		var $enzymes = trim(document.getElementById('enzymes').value.replace(/["'&<>(){};\r\t\n\\]/g,""));
		var $sequence = document.getElementById('plasmid_sequence').value.replace(/>.+/g,'');
		$sequence = $sequence.replace(/[^ATUGCatugc]/g,'');
		var $user_id = getCookie("user_id");
		var $session_check = getCookie("session_check");
		var $paramaters = 'markers=' + $markers + '&enzymes=' + $enzymes + '&sequence=' + $sequence + '&user_id=' + $user_id + '&session_check=' + $session_check + '&plasmid_id=' + $plasmid_id + '&plasmid_name=' + $plasmid_name + '&database=' + $database;
		$save_backbone_changes.update($paramaters,"POST");
		}
	//I need to rebuild the plasmid dropdown menu after a save, since the name of the plasmid may have changed
	}

