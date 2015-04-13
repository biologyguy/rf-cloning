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

$svg_file = file_get_contents("images/banner.svg");
$svg_file = preg_replace("/[\n\r]/","",$svg_file);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="icon" type="image/ico" href="favicon.ico" />
<meta name="keywords" content="rf-cloning, restriction free, restriction free cloning, restriction-free cloning, plasmid, overlap extension, overlap extension PCR" />
<meta name="description" content="A tool for designing restriction free cloning experiments." />
<title>Restriction Free Cloning</title>
<link rel="stylesheet" href="includes/styles.css" />
<script src="classes/ajaxObj.js" type="text/javascript"></script>
<script src="javascript/ajax.js" type="text/javascript"></script>
<script src="classes/raphael_uncompressed.js" language="javascript" type="text/javascript"></script>
<script src="javascript/javascripts.js" language="javascript" type="text/javascript"></script>
<script src="javascript/svg2raphael.js" language="javascript" type="text/javascript"></script>
<script src="javascript/home.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">
function banner()
	{
	svg_banner = new Raphael("banner", 800, 90); //GLOBAL		
	var $params = 'svg_text=<?php echo $svg_file; ?>&div_id=banner&width=800&height=450';
	$svg2raphael.update($params,"POST");
	}

function sample_data()
	{
	document.getElementById('insert_name').value = "Connexin43";
	$call_numbered_triplets_target.update('input=GACATGGGTGACTGGAGCGCCTTGGGGAAGCTGCTGGACAAGGTCCAAGCCTACTCCACGGCCGGAGGGAAGGTGTGGCTGTCGGTGCTCTTCATTTTCAGAATCCTGCTCCTGGGGACAGCGGTTGAGTCAGCTTGGGGTGATGAACAGTCTGCCTTTCGCTGTAACACTCAACAACCCGGTTGTGAAAATGTCTGCTATGACAAGTCCTTCCCCATCTCTCACGTGCGCTTCTGGGTCCTTCAGATCATATTCGTGTCTGTGCCCACACTCCTGTACTTGGCTCACGTGTTCTATGTGATGAGAAAGGAAGAGAAGCTGAACAAGAAAGAAGAGGAGCTCAAAGTGGCGCAGACCGACGGGGTCAACGTGGAGATGCACCTGAAGCAGATTGAAATCAAGAAGTTCAAGTATGGGATTGAAGAACACGGCAAGGTGAAGATGAGAGGTGGCCTGCTGAGAACCTACATCATCAGCATCCTCTTCAAGTCTGTCTTCGAGGTGGCCTTCCTGCTGATCCAGTGGTACATCTATGGGTTCAGCCTGAGTGCGGTCTACACCTGCAAGAGAGATCCCTGCCCCCACCAGGTGGACTGCTTCCTCTCACGTCCCACGGAGAAAACCATCTTCATCATCTTCATGCTGGTGGTGTCCTTGGTGTCTCTCGCTCTGAATATCATTGAGCTCTTCTATGTCTTCTTCAAGGGCGTTAAGGATCGCGTGAAGGGAAGAAGCGATCCTTACCACGCCACCACCGGCCCACTGAGCCCATCCAAAGACTGCGGATCTCCAAAATATGCTTACTTCAATGGCTGCTCCTCACCAACGGCCCCACTCTCACCTATGTCTCCTCCTGGGTACAAGCTGGTCACTGGTGACAGAAACAATTCCTCCTGCCGCAATTACAACAAGCAAGCCAGCGAGCAAAACTGGGCGAATTACAGCGCAGAGCAAAATCGAATGGGGCAGGCCGGAAGCACCATCTCCAACTCCCACGCCCAGCCGTTTGATTTCCCTGACGACAGCCAAAATGCCAAAAAAGTTGCTGCTGGACACGAACTCCAGCCCTTAGCTATCGTGGATCAGCGACCTTCCAGCAGAGCCAGCAGCCGCGCCAGCAGCAGACCTCGGCCTGATGACCTGGAGATT','POST');
	document.getElementById('insert_site_1').value = 590;
	document.getElementById('insert_site_2').value = 678;
	plasmid_focus('2|pEGFP-N1','plasmids','index');	
	document.getElementById('plasmid_list').value = "2|pEGFP-N1";	
	}

function clear_form()
	{
	document.getElementById('insert_name').value = "Insert";
	document.getElementById('insert_description').value = "Insert Description";
	document.getElementById('insert_site_1').value = " -------- ";
	document.getElementById('insert_site_2').value = " -------- ";
	document.getElementById('plasmid_list').value = "nothing";
	document.getElementById('backbone_name').value = "pPLASMID";
	plasmid_focus("nothing");
	}

var $email = new ajaxObject("functions/ajax/email.php",get_email)

function get_email(responseText, responseStatus)
	{
	if (responseStatus == 200)
		{			
		document.getElementById('email1').innerHTML = responseText + "send me an email</a>";
		}
	}
	
</script>
</head>

<?php
include("functions/rev_comp.php");
include("functions/numbered_triplets.php");
include("functions/get_projects_menu.php");
?>



<body onload="warn_ie(); banner(); $email.update('check=ok','POST')" onMouseMove="mousepos(event)">
<div id='help_overlay' onclick="clear_help()"></div>
<div id='help_text' onclick="clear_help()"></div>
<div id='close_help' onclick="clear_help()"></div>
<input type="hidden" id="page" name="page" value="index" />
<div style="width:670px;">
    <div class="tabs">
        <ul>
            <?php 	$login_status = isset($login_status) ? $login_status : "false";
					if($login_status == "true") echo "<li><a href='plasmid_management.php'><span>Manage plasmids</span></a></li>"; ?>
        	<li><a href='savvy.php'><span>Savvy</span></a></li>
            <li><a href='QandA.php' target="_blank"><span>Q & A</span></a></li>
            <li><a href='soap_server.php'><span>SOAP</span></a></li>
            <li><a href="login.php"><span><?php if($login_status == "true") echo "Log out"; else echo "Log in/Register";  ?></span></a></li>
        </ul>
    </div>
    <!--<img src="images/banner.png" title="Restriction Free Cloning"/> -->
    <div id="banner"></div>
</div>
 <br />

<form method="post" action="rf_cloning_project.php" name="rf_cloning">
<!--<h3>Site updated</h3> Mar 19th: Well, I submitted a manuscript to Nucleic Acids Research, and they didn't reject it! The reviewers asked for a few more things though, so I've been updating the site in a caffeine fueled flurry. I really hope I haven't introduced any new bugs (although I definitely did clean up a few), but if you notice something not working like you think it should, PLEASE PLEASE PLEASE <span id="email1">send me an email</span> to let me know! Seriously, I'd rather get 15 emails about the same issue then get 0 emails. Thanks to everyone that has been using the site, I hope it continues to be helpful.<br />
-Steve  
<br />-->
<br />
<table>
    <tr>
    	<td>Backbone name:</td>
        <td><input type="text" name="backbone_name" id="backbone_name" value="pPLASMID" onfocus="clearDefault(this)" size="59" /></td>  
    </tr>
    <tr>
    	<td>Insert name:</td>   
        <td><input type="text" name="insert_name" id="insert_name" value="Insert" onfocus="clearDefault(this)" size="59" /></td>
    </tr>
    <tr>
    	<td>Insert description:</td>   
        <td><input type="text" name="insert_description" id="insert_description" value="Insert Description" onfocus="clearDefault(this)" size="59" /></td>
    </tr>
	<tr>
    	<td>Orientation:</td> 
        <td>(+)<input type="radio" name="orientation" value="cw" checked="checked" /> (-) <input type="radio" name="orientation" value="ccw" /> <img class='help' src="images/help.png" onclick="help_file(0);" /></td>
   	</tr>
	<tr>
		<td>Arrow:</td> 
        <td>On <input type="radio" name="arrow" value="on" checked="checked" /> Off <input type="radio" name="arrow" value="off" /> <img class='help' src="images/help.png" onclick="help_file(1);" /></td>
    </tr>
	<tr>
    	<td colspan="4"><div id="advanced" onclick="advance_div();">Advanced settings <img src="images/arrowhead_fwd_simp.png" /></div>
        	<div id="advanced_form"></div></td>
    </tr>
</table>
<br />
<b><span style="font-size:20px;">Plasmid Sequence</span></b> 
	<?php
	if ($login_status == "true")
	{
	?>
    <select name="plasmid_list" id="plasmid_list" onChange="plasmid_focus(this.options[this.selectedIndex].value,'plasmids')">
    	<option value='nothing' > ---------- Your Plasmids ---------- </option>
		<?php 
		$plasmids_query = mysql_query("SELECT * FROM plasmids WHERE user_id = ".$_SESSION['user_id']." ORDER BY plasmid_name;");
		$plasmids_array = array();
		
		while ($row = mysql_fetch_assoc($plasmids_query))
			{
			array_push($plasmids_array,$row);	
			}
		$counter = 0;
		foreach($plasmids_array as $row)
			{
			echo "<option value='".$row['plasmid_id']."|".$row['plasmid_name']."'>".$row['plasmid_name']."</option>";
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
    <?php
	}
else
	{
	?>
	<select name="plasmid_list" id="plasmid_list" onChange="plasmid_focus(this.options[this.selectedIndex].value,'plasmids','index')">
    	<option value='nothing' > --------- Popular Plasmids --------- </option>
		<?php
		$plasmids_query = mysql_query("SELECT * FROM plasmids WHERE privacy = 1 ORDER BY popularity DESC;");

		$plasmids_array = array();
		
		$counter = 0;
		
		while (($row = mysql_fetch_assoc($plasmids_query)) && ($counter < 40))
			{
			array_push($plasmids_array,$row);
			$counter++;	
			}

		foreach($plasmids_array as $row)
			{
			echo "<option value='".$row['plasmid_id']."|".$row['plasmid_name']."'>".$row['plasmid_name']."</option>
			";
			}
   		?>
	</select>	
	<?php
    }
	?>
    <span class="insert_sites">Specify Insert Sites: 1 <input type="text" id="insert_site_1" name="insert_site_1" size="3" style="z-index:10;" value=" -------- " onfocus="clearDefault(this)" /> 2 <input type="text" id="insert_site_2" name="insert_site_2" size="3" style="z-index:10;" value=" -------- " onfocus="clearDefault(this)" /></span> <img class='help' src="images/help.png" onclick="help_file(4);" />
<br />Or, place 2 exclaimation points (!) directly in the sequence to denote where you want the insert to go.    
    <div style="position:absolute;left:750px;top:250px;">
    <div id="plasmid_map_display_box"></div>
    
    </div>  
    
<br />
<textarea id='plasmid_sequence' name='plasmid_sequence' rows=8 cols=90 wrap='no' style='font-size:9pt;'></textarea> <img class='help' src="images/help.png" onclick="help_file(3);" /><br />
<div style="width:673px;"> 
<input type="button" value="remove FASTA" style="margin-right:1px;"
	onClick="this.form.plasmid_sequence.value=this.form.plasmid_sequence.value.replace(/>.+[\r\n]/g,'')">
<input type="button" value="remove line breaks" style="margin-right:1px;"
	onClick="this.form.plasmid_sequence.value=this.form.plasmid_sequence.value.replace(/[\r\n]/g,'')">

<input type="button" value="remove numbers" style="margin-right:1px;"
	onClick="this.form.plasmid_sequence.value=this.form.plasmid_sequence.value.replace(/[0-9]/g,'')">
<input type="button" value="remove white space" style="margin-right:1px;"
	onClick="this.form.plasmid_sequence.value=this.form.plasmid_sequence.value.replace(/[\t ]/g,'')">
<br /><input type="button" value="show triplets"
	onClick="this.form.plasmid_sequence.value=this.form.plasmid_sequence.value.replace(/[\t ]/g,'').replace(/(...)/g,'$1 ')">
<input type="button" value="UPPER CASE"
	onClick="this.form.plasmid_sequence.value=this.form.plasmid_sequence.value.toUpperCase()">
<input type="button" value="lower case"
	onClick="this.form.plasmid_sequence.value=this.form.plasmid_sequence.value.toLowerCase()">
<input type="button" value="reverse"
	onClick="this.form.plasmid_sequence.value=reverse(this.form.plasmid_sequence.value)">
<input type="button" value="complement"
	onClick="this.form.plasmid_sequence.value=complement(this.form.plasmid_sequence.value)">
<input type="button" value="RNA"
	onClick="this.form.plasmid_sequence.value=toRNA(this.form.plasmid_sequence.value)"><br />
<input type="button" name="num_trip" value="tidy sequence" onclick= "var $plasmid_sequence = document.getElementById('plasmid_sequence').value.replace(/>.+[\n\r]/g,'');
																				$call_numbered_triplets_plasmid.update('input='+ $plasmid_sequence,'POST');" />
<span id='colored_sequence_button'></span>
<span style="float:right;">
<input type="button" class="shadow" style="background-color:#FF9; border:solid thin black;" name="draw_plasmid" value="Draw Plasmid" onclick="index_plas_draw();" />
<img class='help' src="images/help.png" onclick="help_file(8);" />
</span>
<br />
<div id="homepage_draw_tracking" style="float:right;"></div>
<input type="hidden" id="homepage_draw" value="" />
<input type="hidden" id="plas_sequence" value="" />
</div>
<br />
<b><span style="font-size:20px;">Insert Sequence</span></b><br />
<textarea name='target_sequence' id='target_sequence' rows=8 cols=90 wrap="no" style="font-size:9pt;"></textarea>  <img class='help' src="images/help.png" onclick="help_file(2);" /><br />
<input type="button" value="remove FASTA" style="margin-right:1px;"
	onClick="this.form.target_sequence.value=this.form.target_sequence.value.replace(/>.+[\n\r]/g,'')">
<input type="button" value="remove line breaks" style="margin-right:1px;"
	onClick="this.form.target_sequence.value=this.form.target_sequence.value.replace(/[\r\n]/g,'')">

<input type="button" value="remove numbers" style="margin-right:1px;"
	onClick="this.form.target_sequence.value=this.form.target_sequence.value.replace(/[0-9]/g,'')">
<input type="button" value="remove white space"
	onClick="this.form.target_sequence.value=this.form.target_sequence.value.replace(/[\t ]/g,'')">
<br /><input type="button" value="show triplets"
	onClick="this.form.target_sequence.value=this.form.target_sequence.value.replace(/[\t ]/g,'').replace(/(...)/g,'$1 ')">
<input type="button" value="UPPER CASE"
	onClick="this.form.target_sequence.value=this.form.target_sequence.value.toUpperCase()">
<input type="button" value="lower case"
	onClick="this.form.target_sequence.value=this.form.target_sequence.value.toLowerCase()">
<input type="button" value="reverse"
	onClick="this.form.target_sequence.value=reverse(this.form.target_sequence.value)">
<input type="button" value="complement"
	onClick="this.form.target_sequence.value=complement(this.form.target_sequence.value)">
<input type="button" value="RNA"
	onClick="this.form.target_sequence.value=toRNA(this.form.target_sequence.value)">
   <!--<div class="tool_button"><img src="images/cutom_button/left_default.png" /><span>Test text</span><img src="images/cutom_button/right_default.png" /></div> -->
    <br />
<input type="button" name="num_trip" value="tidy sequence" onclick="	var $input = document.getElementById('target_sequence').value.replace(/>.+[\n\r]/g,''); 
																				$call_numbered_triplets_target.update('input=' + $input,'POST')" /><br />

<br />
<br />
<input type="hidden" name="plasmid_target_Tm" id="plasmid_target_Tm" value="60" />
<input type="hidden" name="insert_target_Tm" id="insert_target_Tm" value="55" />
<input type="hidden" name="plasmid_min_length" id="plasmid_min_length" value="22" />
<input type="hidden" name="insert_min_length" id="insert_min_length" value="18" />
<input type="hidden" name="plasmid_max_length" id="plasmid_max_length" value="35" />
<input type="hidden" name="insert_max_length" id="insert_max_length" value="25" />
 
<input type="hidden" name="backbone_id" id="backbone_id" value="blank" />
<input type="hidden" name="database" id="database" value="" />
<input type="button" name="clear" value="Clear form" onclick="clear_form()" style="float:left; margin-right:6px;" />
<input type="submit" name="execute" value="  Run!  " style="float:left" />
</form>
<input type="button" style="margin-left:424px;" value="Sample Data" onclick="sample_data()" />
<div id="features_display" onclick="begindrag(event)" style="cursor:move;"></div>
<br /><br /><br /><br />

<form name='savvy_form' action='/cgi-bin/savvy.cgi' target='_blank' method='post'>
    <input type='hidden' name='plasmid_name' 		id='plasmid_name2' 		value='' />
    <input type='hidden' name='markers' 			id='markers' 			value='' />
	<input type='hidden' name='enzymes' 			id='enzymes' 			value='' />
    <input type='hidden' name='plasmid_size' 		id='plasmid_size' 		value='' />
	<input type='hidden' name='line_thickness'		id='line_thickness'		value='0.5' />
</form>
<?php include("includes/footer.php"); ?>

</body>
</html>
