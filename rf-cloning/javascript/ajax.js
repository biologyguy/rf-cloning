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
	

/*********************************************************************************/
var $get_colored_plasmid_sequence = new ajaxObject("functions/ajax/get_colored_plasmid_sequence.php", get_colored_plasmid_sequence);
function get_colored_plasmid_sequence(responseText, responseStatus)
	{
	if(responseStatus == 200)
		{		
		document.getElementById("plasmid_sequence").innerHTML = "<div width='300pt'>" + responseText + "</div>";
		}
	else
		{		
		document.getElementById("plasmid_sequence").innerHTML = "<div width='300pt'>There was a problem retrieving the colored plasmid from the database</div>";
		}		
	}

/*********************************************************************************/
var $call_numbered_triplets_target = new ajaxObject("functions/ajax/numbered_triplets.php",get_target_sequence);

function get_target_sequence(responseText, responseStatus)
	{
	if (responseStatus == 200)
		{
		document.getElementById("target_sequence").value = responseText;
		}
	}

/*********************************************************************************/
var $get_plasmid_sequence = new ajaxObject("functions/ajax/get_plasmid_sequence.php",get_plasmid_sequence);
var $call_numbered_triplets_plasmid = new ajaxObject("functions/ajax/numbered_triplets.php",get_plasmid_sequence);

function get_plasmid_sequence(responseText, responseStatus)
	{
	if (responseStatus == 200)
		{
		document.getElementById("plasmid_sequence").value = responseText;
		if(document.getElementById('database').value == "projects")
			{
			document.getElementById("plasmid_sequence").readOnly = true;
			document.getElementById("plasmid_sequence").style.color='#999';	
			}
		}
	else
		{
		document.getElementById("plasmid_sequence").value = responseStatus;	
		}
	}
		

/*********************************************************************************/
var $save_project = new ajaxObject("functions/ajax/save.php",save_project);
function save_project(responseText, responseStatus)
	{
	if (responseStatus == 200)
		{
		clear_save_alert();
		var $response_array = responseText.split("~|~");
		if (isInt($response_array[1]))
			{
			document.getElementById("project_id").value = $response_array[1];	
			document.getElementById("saved_alert").innerHTML = $response_array[0];
			
			if (document.getElementById('page').value == "project")
				{
				document.getElementById('save').disabled = true;	
				}
				
			if (document.getElementById('page').value == "project" && document.getElementById('autosave').value == "true")
				{
				document.getElementById('project_complete').disabled = false;
				document.getElementById('autosave').value = "false";
				document.getElementById('user_id').value = $response_array[2];
				}
			}

		else
			{
			document.getElementById("saved_alert").innerHTML = responseText;	
			}
		}
	}


/*********************************************************************************/
var $complete_click = new ajaxObject("functions/ajax/complete_click.php",complete_click);
function complete_click(responseText, responseStatus)
	{
	if (responseStatus == 200)
		{
		if(responseText == 1)
			{
			document.getElementById("project_complete").disabled=true;
			document.getElementById("project_incomplete").disabled=false;
			}
		if(responseText == 0)
			{
			document.getElementById("project_complete").disabled=false;
			document.getElementById("project_incomplete").disabled=true;
			}
		clear_save_alert();
		document.getElementById("complete").value= responseText;
		}
	}

/***********************************************************************/
var $savvy_info_ajax = new ajaxObject("functions/ajax/savvy_info.php",get_savvy_info);

function get_savvy_info(responseText, responseStatus)
	{
	if(responseStatus == 200)
		{
		var $response = JSON.parse(responseText);
		var $markers = unescape($response['markers'].replace(/&/g,""));
		
		$post_parameters = 'markers='+$markers+'&enzymes='+$response['enzymes']+'&plasmid_name='+$response['plasmid_name']+'&line_thickness=0.5&shape=circular&plasmid_size='+ $response['plasmid_size'];
		
		if(document.getElementById('page').value == "index")
			{
			document.getElementById('markers').value = $markers;
			document.getElementById('enzymes').value = $response['enzymes'];
			document.getElementById('plasmid_name2').value = $response['plasmid_name'];
			}
			
		setTimeout(function(){draw_plas_dis_box($response['plasmid_id'],$response['database'])},0);			
		setTimeout(function(){$draw_plasmid.update($post_parameters, 'POST')},200);			
		
		clean_up($response['plasmid_id'], $response['plasmid_name'], $markers);
		}		
	}

/***********************************************************************/
var $svg2raphael = new ajaxObject("functions/ajax/svg2raphael.php",svg2raphael);

function svg2raphael(responseText, responseStatus)
	{
	if(responseStatus == 200)
		{
		eval(responseText);	
		}
	}

/*************************************************************************/
var $restriction_digest = new ajaxObject("/cgi-bin/restriction_digest.cgi",restriction_digest);
function restriction_digest(responseText, responseStatus)
	{
	if(responseStatus == 200)
		{
		document.getElementById('enzymes').value = responseText;
		if(document.getElementById('page').value == "index")
			{
			if(document.getElementById('homepage_draw').value == "drawing")
				{
				setTimeout(function(){document.getElementById('homepage_draw_tracking').innerHTML = "Drawing..."},0);
				$post_parameters = 'markers='+document.getElementById('markers').value+'&enzymes='+document.getElementById('enzymes').value+'&plasmid_name='+document.getElementById('backbone_name').value+'&line_thickness=0.5&shape=circular&plasmid_size='+document.getElementById('plasmid_size').value;
				$draw_plasmid.update($post_parameters, 'POST');
				}
			}
		}
	else
		{
		document.getElementById('enzymes').value = "There was an error connecting with the server, please try again.";
		}
	}
	
/****************************************************************************/
var $blast_features = new ajaxObject("/cgi-bin/plasmid_features.cgi",plasmid_features);
function plasmid_features(responseText, responseStatus)
	{
	if(responseStatus == 200)
		{
		document.getElementById('markers').value = responseText;
		}
	else
		{
		document.getElementById('markers').value = "There was an error connecting with the server, please try again.";
		}
	}