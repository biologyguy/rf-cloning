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
var $draw_plasmid = new ajaxObject("/cgi-bin/savvy.cgi",draw_plasmid);
	
function draw_plasmid(responseText, responseStatus, responseXML)
	{
	if (responseStatus == 200)
		{			
		document.getElementById('plasmid_map_display_box').innerHTML = "";
		svg_plasmid_map_display_box = Raphael("plasmid_map_display_box", 530, 500);	//GLOBAL
		svg_plasmid_map_display_box.rect(0, 0, 530, 500).attr({"fill":"white", "stroke":"black", "stroke-width":"5"});

		var $params = "svg_text=" + responseText + "&div_id=plasmid_map_display_box&width=530&height=500";
		$svg2raphael.update($params,"POST");
		
//Output 'close' and 'print' buttons
		//Close button
		svg_plasmid_map_display_box.rect(396.868, 446.396, 32.158, 24).attr({"fill":"#F6EC43", "stroke":"#000000", "stroke-width":"2", "stroke-miterlimit":"10"});
		svg_plasmid_map_display_box.path("M 402.775 468.567 L 423.118 448.226").attr({"stroke":"#C12A2E", "stroke-width":"4", "stroke-miterlimit":"10"});
		var $x_outline1 = svg_plasmid_map_display_box.rect(398.563, 456.46, 28.769, 3.873).attr({"fill":"none", "stroke":"#000000", "stroke-width":"0.5", "stroke-miterlimit":"10"});
		$x_outline1.rotate(-45);
		svg_plasmid_map_display_box.path("M 402.775 448.226 L 423.118 468.567").attr({"stroke":"#C12A2E", "stroke-width":"4", "stroke-miterlimit":"10"});
		var $x_outline2 = svg_plasmid_map_display_box.rect(398.471, 456.552, 28.769, 3.873).attr({"fill":"none", "stroke":"#000000", "stroke-width":"0.5", "stroke-miterlimit":"10"});
		$x_outline2.rotate(45);
		var $close = svg_plasmid_map_display_box.rect(396.868, 446.396, 32.158, 24).attr({"fill":"#000000", "fill-opacity":"0.01", "stroke":"#000000", "stroke-width":"2", "stroke-miterlimit":"10"});
		$close.click(function () { 	document.getElementById('plasmid_map_display_box').innerHTML = ""; });
		
		//Print button
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
		$print.click(function() {setTimeout(function(){drag=0;},1000); document.savvy_form.submit();});
		setTimeout(function(){	document.getElementById('draw_plasmid_button').disabled = false;},100);
		}
	}

/*********************************************************************************/
var $add_basepair = new ajaxObject("functions/ajax/add_basepair.php",change_basepair);
var $sub_basepair = new ajaxObject("functions/ajax/sub_basepair.php",change_basepair);

function change_basepair(responseText,responseStatus)
	{			
	if(responseStatus == 200)
		{
		var $response = JSON.parse(responseText);		
			
		if(($response['primer_dir_code'] == 1) || ($response['primer_dir_code'] == 2))
			{
			document.getElementById("fwd_primer").innerHTML= $response['fwd_primer'];
			document.getElementById("fwd_ins_tm").innerHTML= Math.round($response['fwd_ins_tm']) + "&deg;C";	
			document.getElementById("fwd_plas_tm").innerHTML= Math.round($response['fwd_plas_tm']) + "&deg;C";
			document.getElementById("fwd_prim_len").innerHTML= $response['fwd_prim_len'];
			document.getElementById("pcr_prod_size").innerHTML= $response['pcr_prod_size'];
			document.getElementById("extension_time").innerHTML= $response['extension_time'];
			document.getElementById("ng_insert").innerHTML= Math.round($response['ng_insert']);
			document.getElementById("ng_plasmid").innerHTML= Math.round($response['ng_plasmid']);
			document.getElementById("fwd_primer_database").value= $response['fwd_primer_database'];
			document.getElementById("no_first_pcr").innerHTML = $response['no_first_pcr'];
			}
		else if(($response['primer_dir_code'] == 3) || ($response['primer_dir_code'] == 4))
			{
			document.getElementById("rev_primer").innerHTML= $response['rev_primer'];
			document.getElementById("rev_ins_tm").innerHTML= Math.round($response['rev_ins_tm']) + "&deg;C";	
			document.getElementById("rev_plas_tm").innerHTML= Math.round($response['rev_plas_tm']) + "&deg;C";
			document.getElementById("rev_prim_len").innerHTML= $response['rev_prim_len'];
			document.getElementById("pcr_prod_size").innerHTML= $response['pcr_prod_size'];
			document.getElementById("extension_time").innerHTML= $response['extension_time'];
			document.getElementById("ng_insert").innerHTML= Math.round($response['ng_insert']);
			document.getElementById("ng_plasmid").innerHTML= Math.round($response['ng_plasmid']);
			document.getElementById("rev_primer_database").value= $response['rev_primer_database'];
			document.getElementById("no_first_pcr").innerHTML = $response['no_first_pcr'];
			}
		clear_save_alert();	
		activate_save();				
		}	
	}
				
/*********************************************************************************/
var $shift_insert = new ajaxObject("functions/ajax/shift_insert.php",shift_insert);
function shift_insert(responseText, responseStatus)
	{
	if(responseStatus == 200)
		{
		clear_save_alert();
		var $response = JSON.parse(responseText);			
		document.getElementById("backbone_id").value= $response['backbone_id'];
		document.getElementById("new_construct").innerHTML= $response['new_construct'];
		document.getElementById("sequence").value= $response['sequence'];
		document.getElementById("plasmid_sequence").value= $response['plasmid_sequence'];
		document.getElementById("insert_sequence").value= $response['insert_sequence'];
		document.getElementById("insert_sites_span").innerHTML= $response['insert_sites'];
		document.getElementById("insert_sites").value= $response['insert_sites'];
		document.getElementById("new_size_span").innerHTML= $response['new_size'];
		document.getElementById("new_size").value= $response['new_size'];
		document.getElementById("plasmid_size").value= $response['new_size'];
		document.getElementById("markers").value= $response['savvy_markers'];
		document.getElementById("enzymes").value= $response['savvy_enzymes'];
		
		document.getElementById("fwd_primer_database").value= $response['fwd_primer_database'];				
		document.getElementById("fwd_primer").innerHTML= $response['fwd_primer'];
		document.getElementById("fwd_ins_tm").innerHTML= Math.round($response['fwd_ins_tm']) + "&deg;C";	
		document.getElementById("fwd_plas_tm").innerHTML= Math.round($response['fwd_plas_tm']) + "&deg;C";
		document.getElementById("fwd_prim_len").innerHTML= $response['fwd_primer_length'];
		
		document.getElementById("rev_primer_database").value= $response['rev_primer_database'];				
		document.getElementById("rev_primer").innerHTML= $response['rev_primer'];
		document.getElementById("rev_ins_tm").innerHTML= Math.round($response['rev_ins_tm']) + "&deg;C";	
		document.getElementById("rev_plas_tm").innerHTML= Math.round($response['rev_plas_tm']) + "&deg;C";
		document.getElementById("rev_prim_len").innerHTML= $response['rev_primer_length'];
					
		document.getElementById("pcr_prod_size").innerHTML= $response['target_pcr_size'];
		
		
		document.getElementById("extension_time").innerHTML= $response['extension_time'];
		document.getElementById("ng_insert").innerHTML= Math.round($response['ng_of_insert']);
		document.getElementById("ng_plasmid").innerHTML= Math.round($response['ng_of_plasmid']);
		
		document.getElementById("no_first_pcr").innerHTML = $response['no_first_pcr'];
			
		document.getElementById("insert_shift_alert").innerHTML = "Insert sites shifted from " + $response['old_insert_sites'] + " to " + $response['insert_sites'];					
		activate_save();
		}
	}
/**************************************************************************/
function draw_plas_dis_box()
	{
	document.getElementById('plasmid_map_display_box').innerHTML = "<div style='width:523px;height:493px;background-color:white; border:solid 3px black;'><img src='images/loading.gif' style='position:absolute;top:150px;left:165px;'/></div>";
	document.getElementById('draw_plasmid_button').disabled = true;
	
	var $markers = document.getElementById('markers').value;
	var $enzymes = document.getElementById('enzymes').value;
	var $plasmid_name = document.getElementById('plasmid_name').value; 
	var $line_thickness = document.getElementById('line_thickness').value;
	var $shape = document.getElementById('shape').value;
	var $plasmid_size = document.getElementById('plasmid_size').value;
	$post_parameters = 'markers='+$markers+'&enzymes='+$enzymes+'&plasmid_name='+$plasmid_name+'&line_thickness='+$line_thickness+'&shape='+$shape+'&plasmid_size='+$plasmid_size;
	
	setTimeout(function(){$draw_plasmid.update($post_parameters, 'POST')},200);	
	}
	
/*********************************************************************************/
function save_project()
	{
	var $js_plasmid = JSON.parse(document.getElementById('plasmid_obj').value); 

	$js_plasmid['plasmid_seq'] = document.getElementById('sequence').value;
	$js_plasmid['orig_plasmid_seq'] = document.getElementById('plasmid_sequence').value;
	$js_plasmid['insert_seq'] = document.getElementById('insert_sequence').value;
	$js_plasmid['insert_sites'] = document.getElementById('insert_sites').value;
	$js_plasmid['notes'] = document.getElementById('notes').value.replace(/["'&<>\\;]/g,"");	
	//$js_plasmid['savvy_markers'] = trim(document.getElementById('markers').value.replace(/["'&<>(){};\t\\]/g,""));
	$js_plasmid['savvy_markers'] = trim(document.getElementById('markers').value.replace(/\n+/g,"\\n"));
	$js_plasmid['savvy_enzymes'] = trim(document.getElementById('enzymes').value.replace(/["'&<>(){};\r\t\n\\]/g,""));
	$js_plasmid['savvy_meta'] = document.getElementById('savvy_meta').value;
	$js_plasmid['plasmid_id'] = document.getElementById('project_id').value;
	$js_plasmid['complete'] = document.getElementById('complete').value;
	$js_plasmid['fwd_primer'] = document.getElementById('fwd_primer_database').value;
	$js_plasmid['rev_primer']= document.getElementById('rev_primer_database').value;
	$js_plasmid['user_id'] = document.getElementById('user_id').value;
	
	$post_parameters = 'plasmid_obj='+JSON.stringify($js_plasmid);
		
	$save_project.update($post_parameters,'POST');	
	}

/*********************************************************************************/
function activate_save()
	{
	document.getElementById('save').disabled = false;	
	}

/**************************************** Draggable DIV *****************************************/

var drag=0;
var xdif=0;
var ydif=0;
var initx="0px";
var inity="0px";

function begindrag(event)
	{
	if(drag==0 && (document.getElementById('plasmid_map_display_box').innerHTML != ""))
		{
		floatingd = document.getElementById("plasmid_map_display_box");
		if(floatingd.style.left=="")
			{
			floatingd.style.left=initx;
			}
		if(floatingd.style.top=="")
			{
			floatingd.style.top=inity;
			}
		prex=floatingd.style.left.replace(/px/,"");
		prey=floatingd.style.top.replace(/px/,"");
		drag=1;
		xdif=event.clientX-prex;
		ydif=event.clientY-prey;
		}
	else
		{
		drag=0;
		}
	}

function mousepos(event)
	{
	floatingd = document.getElementById("plasmid_map_display_box");
	if(drag==1)
		{
		floatingd.style.left = event.clientX-xdif+"px";
		floatingd.style.top = event.clientY-ydif+"px";
		}
	}

/************************************************************************************************/
var $align_result = new ajaxObject("functions/ajax/align_result.php",send_alignment);

function prep_alignment()
	{
	$new_window = window.open("about:blank","Alignment","menubar=yes,toolbar=no,location=no,scrollbars=yes,directories=no,copyhistory=no,resizable=yes");
	var $plasmid_seq = document.getElementById('sequence').value;
	var $proj_name = document.getElementById('plasmid_name').value;
	var $sequencing_seq = ">Insert sequence from " + document.getElementById('plasmid_name').value + "\\n" + document.getElementById('insert_sequence').value;
	$align_result.update("plasmid_seq=" + $plasmid_seq + "&proj_name=" + $proj_name + "&sequencing_seq=" + $sequencing_seq,"POST");
	}

function send_alignment(responseText, responseStatus)
	{
	if(responseStatus == 200)
		{		
		$new_window.document.body.innerHTML = responseText;	
		}
	}

/*************************************************************************/
function gbfile()
	{
	var $plas_sequence = document.getElementById('sequence').value.replace(/>.+/g,"");
	$plas_sequence = $plas_sequence.toUpperCase();
	$plas_sequence = $plas_sequence.replace(/[^ATUCG]/g,"");
	document.getElementById('sequence_export').value = $plas_sequence; 
	document.getElementById('length_export').value = $plas_sequence.length;
	document.getElementById('marker_export').value = document.getElementById('markers').value;
	document.getElementById('name_export').value = document.getElementById('plasmid_name').value;
	document.gbfile_form.submit()	
	}