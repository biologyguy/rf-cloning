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
		if(document.getElementById('homepage_draw').value = "drawing")
			{
			document.getElementById('homepage_draw').value = "";
			document.getElementById('homepage_draw_tracking').innerHTML = "";
			}
		svg_plasmid_map_display_box = Raphael("plasmid_map_display_box", 800, 600);	//GLOBAL
		//svg_plasmid_map_display_box.rect(0, 0, 800, 600).attr({"fill":"none", "stroke":"none"});
		var $params = "svg_text=" + responseText + "&div_id=plasmid_map_display_box&width=800&height=600";
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
function draw_plas_dis_box()
	{
	document.getElementById('plasmid_map_display_box').innerHTML = "<div style='height:600px; width:490px; text-align:center; margin-top:150px;'><img src='images/loading.gif' /></div>";	
	}

/************************************************************************/
function clean_up($id, $backbone_name, $markers)
	{
	$get_plasmid_sequence.update('id=' + $id + '&database=plasmids','POST');
	document.getElementById('colored_sequence_button').innerHTML = "<input type='button' value='Show Color Map' onclick=\"window.open('colored_sequence_map.php?id=" + $id + "&database=plasmids','Colored_Sequence_Map','resizable=yes,scrollbars=yes')\" />";	
	
	document.getElementById('backbone_name').value = $backbone_name;
	document.getElementById('backbone_id').value = $id;
	document.getElementById('database').value = "plasmids";
	var $features = clean_features($markers)
	document.getElementById('features_display').innerHTML = $features;
	}

/************************************************************************/
function warn_ie()
	{
	$user_agent = navigator.userAgent; 
	var $re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})"); 
	if ($re.exec($user_agent) != null)
		{
		var $rv = parseFloat( RegExp.$1 );
		}
	var $check_cookie = getCookie("ie_check");	
	if (($rv > -1) && ($rv < 9) && ($check_cookie != "true"))
		{
			
		var $exp_date=new Date();
		$exp_date.setDate($exp_date.getDate()+1);
		document.cookie = "ie_check=true; expires=" + $exp_date.toUTCString();
		
		alert("I see you're using an older version of Internet Explorer! Unfortunately, Microsoft had previously chosen not to adopt some of the W3 standards in the creation of their rather ubiquitous web browser, which is a real pain for us web designers... The good news is that I have managed to accommodate most of the IE idiosyncrasies, and the major features of this site will work for you; the bad news is that you won't be able to download or print any of the custom plasmid maps you may make. All other major browsers and IE9+ should work just fine.");	
		}	
	}

/************************************************************************/
var $home_blast_features = new ajaxObject("/cgi-bin/plasmid_features.cgi",features_list);
function features_list(responseText, responseStatus)
	{
	if(responseStatus == 200)
		{
		var $features = clean_features(responseText)
		document.getElementById('features_display').innerHTML = $features;
		
		if(document.getElementById('homepage_draw').value = "drawing")
			{
			document.getElementById('markers').value = responseText;
			document.getElementById('homepage_draw_tracking').innerHTML = "Digesting...";
			$restriction_digest.update('sequence=' + document.getElementById('plas_sequence').value + '&cut_num=1','POST');
			}
		}
	else
		{
		document.getElementById('features_display').innerHTML = "There was an error with $blast_features.update: " + responseStatus;
		}
	}
/************************************************************************/
function index_plas_draw()
	{
	setTimeout(function(){document.getElementById('homepage_draw_tracking').innerHTML = 'BLASTing features...'; draw_plas_dis_box();},1);
	document.getElementById('homepage_draw').value = "drawing";
	var $plas_sequence = document.getElementById('plasmid_sequence').value.replace(/>.+/g,"").toUpperCase();
	$plas_sequence = $plas_sequence.replace(/[^ATUCG]/g,"");	
	document.getElementById('plas_sequence').value = $plas_sequence;
	document.getElementById('plasmid_size').value = $plas_sequence.length;
	$home_blast_features.update('sequence=' + $plas_sequence,'POST');
	}


/************************************************************************/		
function clean_features($savvy_markers)
	{
	var $features_array = $savvy_markers.split("\n");
	var $length = $features_array.length;
	$features_array = $features_array.slice(0,$length - 1);
	var $features = "<table>";
	for (var $i in $features_array)
		{
		var $line = $features_array[$i].split(" ");
		$features += "<tr><td>" + $line[0] + "</td><td>" + $line[1] + "</td><td>" + $line[2] + "</td></tr>";
		}	
	$features += "</table>";
	return $features;	
	}

/************************************************************************/	
var $on_off = 0;
function advance_div()
	{
	if($on_off == 0)
		{
		document.getElementById('advanced').innerHTML = "Advanced settings <img src='images/arrowhead_down_simp.png' />";	
		document.getElementById('advanced_form').innerHTML = "<table><tr><td>Plasmid side anneal &deg;C:</td><td><input type='text' size='4' id='plasmid_target_Tm_js' onKeyUp='update_advanced(\"plasmid_target_Tm\");' value='"+document.getElementById('plasmid_target_Tm').value+"' /></td><td style='padding-left:30px'>Insert side anneal &deg;C:</td><td><input type='text' size='4' id='insert_target_Tm_js' onKeyUp='update_advanced(\"insert_target_Tm\");' value='"+document.getElementById('insert_target_Tm').value+"' /></td></tr><tr><td>Plasmid side min length:</td><td><input type='text' size='4' id='plasmid_min_length_js' onKeyUp='update_advanced(\"plasmid_min_length\");' value='"+document.getElementById('plasmid_min_length').value+"' /></td><td style='padding-left:30px'>Insert side min length:</td><td><input type='text' size='4' id='insert_min_length_js' onKeyUp='update_advanced(\"insert_min_length\");' value='"+document.getElementById('insert_min_length').value+"' /></td><td style='padding-left:25px;'><img class='help' src='images/help.png' z-index='1' onclick='help_file(9);' /></td></tr><tr><td>Plasmid side max length:</td><td><input type='text' size='4' id='plasmid_max_length_js' onKeyUp='update_advanced(\"plasmid_max_length\");' value='"+document.getElementById('plasmid_max_length').value+"' /></td><td style='padding-left:30px'>Insert side max length:</td><td><input type='text' size='4' id='insert_max_length_js' onKeyUp='update_advanced(\"insert_max_length\");' value='"+document.getElementById('insert_max_length').value+"' /></td></tr></table>";
		$on_off = 1;
		}
	
	else
		{
		document.getElementById('advanced').innerHTML = "Advanced settings <img src='images/arrowhead_fwd_simp.png' />";	
		document.getElementById('advanced_form').innerHTML = "";
		$on_off = 0;
		}
	}

/************************************************************************/	

function update_advanced($field)
	{
	var $value = document.getElementById($field+"_js").value;
	if ($value == "")
		{
		document.getElementById($field).value = 0;	
		}
	else if (($field == "plasmid_max_length" || $field == "insert_max_length") && (parseInt($value) > 100))
		{
		document.getElementById($field).value = 100;
		document.getElementById($field+"_js").value = 100;	
		}
	else if (($field == "plasmid_target_Tm" || $field == "insert_target_Tm") && (parseInt($value) > 83))
		{
		document.getElementById($field).value = 83;
		document.getElementById($field+"_js").value = 83;	
		}
	else if ($field == "plasmid_min_length" && parseInt($value) > document.getElementById('plasmid_max_length').value)
		{
		document.getElementById($field).value = document.getElementById('plasmid_max_length').value;
		document.getElementById($field+"_js").value = document.getElementById('plasmid_max_length').value;	
		}
	else if ($field == "insert_min_length" && parseInt($value) > document.getElementById('insert_max_length').value)
		{
		document.getElementById($field).value = document.getElementById('insert_max_length').value;
		document.getElementById($field+"_js").value = document.getElementById('insert_max_length').value;	
		}	
	else if(parseInt($value)==$value)
		{
		document.getElementById($field).value = $value;	
		}
	else
		{
		document.getElementById($field).value = 0;
		document.getElementById($field+"_js").value = 0;	
		}
	}

/**************************************** Draggable DIV *****************************************/

var drag=0;
var xdif=0;
var ydif=0;
var initx="730px";
var inity="700px";

function begindrag(event)
	{
	if(drag==0 && (document.getElementById('features_display').innerHTML != ""))
		{
		floatingd = document.getElementById("features_display");
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
	floatingd = document.getElementById("features_display");
	if(drag==1)
		{
		floatingd.style.left = event.clientX-xdif+"px";
		floatingd.style.top = event.clientY-ydif+"px";
		}
	}
