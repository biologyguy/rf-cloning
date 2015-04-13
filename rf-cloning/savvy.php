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

if(isset($_COOKIE['user_id']))
	{
	include("functions/set_session.php");	
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="keywords" content="plasmid map, custom plasmid map, restriction map, plasmid vector map, savvy" />
<meta name="description" content="Re-tooled version of the Savvy SVG plasmid map generating program." />
<link rel="icon" type="image/ico" href="favicon.ico" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Draw a Custom Plasmid Map</title>

<script src="classes/ajaxObj.js" language="javascript" type="text/javascript"></script>
<script src="classes/raphael_uncompressed.js" language="javascript" type="text/javascript"></script>
<script src="javascript/ajax.js" language="javascript" type="text/javascript"></script>
<script src="javascript/javascripts.js" language="javascript" type="text/javascript"></script>
<script src="javascript/management.js" language="javascript" type="text/javascript"></script>
<script language="javascript" type="text/javascript">
function sample_data()
	{
	document.getElementById('plasmid_name').value = "pEGFP-N1";
	
	document.getElementById('plasmid_sequence').value = "TAGTTATTAATAGTAATCAATTACGGGGTCATTAGTTCATAGCCCATATATGGAGTTCCGCGTTACATAACTTACGGTAAATGGCCCGCCTGGCTGACCGCCCAACGACCCCCGCCCATTGACGTCAATAATGACGTATGTTCCCATAGTAACGCCAATAGGGACTTTCCATTGACGTCAATGGGTGGAGTATTTACGGTAAACTGCCCACTTGGCAGTACATCAAGTGTATCATATGCCAAGTACGCCCCCTATTGACGTCAATGACGGTAAATGGCCCGCCTGGCATTATGCCCAGTACATGACCTTATGGGACTTTCCTACTTGGCAGTACATCTACGTATTAGTCATCGCTATTACCATGGTGATGCGGTTTTGGCAGTACATCAATGGGCGTGGATAGCGGTTTGACTCACGGGGATTTCCAAGTCTCCACCCCATTGACGTCAATGGGAGTTTGTTTTGGCACCAAAATCAACGGGACTTTCCAAAATGTCGTAACAACTCCGCCCCATTGACGCAAATGGGCGGTAGGCGTGTACGGTGGGAGGTCTATATAAGCAGAGCTGGTTTAGTGAACCGTCAGATCCGCTAGCGCTACCGGACTCAGATCTCGAGCTCAAGCTTCGAATTCTGCAGTCGACGGTACCGCGGGCCCGGGATCCACCGGTCGCCACCATGGTGAGCAAGGGCGAGGAGCTGTTCACCGGGGTGGTGCCCATCCTGGTCGAGCTGGACGGCGACGTAAACGGCCACAAGTTCAGCGTGTCCGGCGAGGGCGAGGGCGATGCCACCTACGGCAAGCTGACCCTGAAGTTCATCTGCACCACCGGCAAGCTGCCCGTGCCCTGGCCCACCCTCGTGACCACCCTGACCTACGGCGTGCAGTGCTTCAGCCGCTACCCCGACCACATGAAGCAGCACGACTTCTTCAAGTCCGCCATGCCCGAAGGCTACGTCCAGGAGCGCACCATCTTCTTCAAGGACGACGGCAACTACAAGACCCGCGCCGAGGTGAAGTTCGAGGGCGACACCCTGGTGAACCGCATCGAGCTGAAGGGCATCGACTTCAAGGAGGACGGCAACATCCTGGGGCACAAGCTGGAGTACAACTACAACAGCCACAACGTCTATATCATGGCCGACAAGCAGAAGAACGGCATCAAGGTGAACTTCAAGATCCGCCACAACATCGAGGACGGCAGCGTGCAGCTCGCCGACCACTACCAGCAGAACACCCCCATCGGCGACGGCCCCGTGCTGCTGCCCGACAACCACTACCTGAGCACCCAGTCCGCCCTGAGCAAAGACCCCAACGAGAAGCGCGATCACATGGTCCTGCTGGAGTTCGTGACCGCCGCCGGGATCACTCTCGGCATGGACGAGCTGTACAAGTAAAGCGGCCGCGACTCTAGATCATAATCAGCCATACCACATTTGTAGAGGTTTTACTTGCTTTAAAAAACCTCCCACACCTCCCCCTGAACCTGAAACATAAAATGAATGCAATTGTTGTTGTTAACTTGTTTATTGCAGCTTATAATGGTTACAAATAAAGCAATAGCATCACAAATTTCACAAATAAAGCATTTTTTTCACTGCATTCTAGTTGTGGTTTGTCCAAACTCATCAATGTATCTTAAGGCGTAAATTGTAAGCGTTAATATTTTGTTAAAATTCGCGTTAAATTTTTGTTAAATCAGCTCATTTTTTAACCAATAGGCCGAAATCGGCAAAATCCCTTATAAATCAAAAGAATAGACCGAGATAGGGTTGAGTGTTGTTCCAGTTTGGAACAAGAGTCCACTATTAAAGAACGTGGACTCCAACGTCAAAGGGCGAAAAACCGTCTATCAGGGCGATGGCCCACTACGTGAACCATCACCCTAATCAAGTTTTTTGGGGTCGAGGTGCCGTAAAGCACTAAATCGGAACCCTAAAGGGAGCCCCCGATTTAGAGCTTGACGGGGAAAGCCGGCGAACGTGGCGAGAAAGGAAGGGAAGAAAGCGAAAGGAGCGGGCGCTAGGGCGCTGGCAAGTGTAGCGGTCACGCTGCGCGTAACCACCACACCCGCCGCGCTTAATGCGCCGCTACAGGGCGCGTCAGGTGGCACTTTTCGGGGAAATGTGCGCGGAACCCCTATTTGTTTATTTTTCTAAATACATTCAAATATGTATCCGCTCATGAGACAATAACCCTGATAAATGCTTCAATAATATTGAAAAAGGAAGAGTCCTGAGGCGGAAAGAACCAGCTGTGGAATGTGTGTCAGTTAGGGTGTGGAAAGTCCCCAGGCTCCCCAGCAGGCAGAAGTATGCAAAGCATGCATCTCAATTAGTCAGCAACCAGGTGTGGAAAGTCCCCAGGCTCCCCAGCAGGCAGAAGTATGCAAAGCATGCATCTCAATTAGTCAGCAACCATAGTCCCGCCCCTAACTCCGCCCATCCCGCCCCTAACTCCGCCCAGTTCCGCCCATTCTCCGCCCCATGGCTGACTAATTTTTTTTATTTATGCAGAGGCCGAGGCCGCCTCGGCCTCTGAGCTATTCCAGAAGTAGTGAGGAGGCTTTTTTGGAGGCCTAGGCTTTTGCAAAGATCGATCAAGAGACAGGATGAGGATCGTTTCGCATGATTGAACAAGATGGATTGCACGCAGGTTCTCCGGCCGCTTGGGTGGAGAGGCTATTCGGCTATGACTGGGCACAACAGACAATCGGCTGCTCTGATGCCGCCGTGTTCCGGCTGTCAGCGCAGGGGCGCCCGGTTCTTTTTGTCAAGACCGACCTGTCCGGTGCCCTGAATGAACTGCAAGACGAGGCAGCGCGGCTATCGTGGCTGGCCACGACGGGCGTTCCTTGCGCAGCTGTGCTCGACGTTGTCACTGAAGCGGGAAGGGACTGGCTGCTATTGGGCGAAGTGCCGGGGCAGGATCTCCTGTCATCTCACCTTGCTCCTGCCGAGAAAGTATCCATCATGGCTGATGCAATGCGGCGGCTGCATACGCTTGATCCGGCTACCTGCCCATTCGACCACCAAGCGAAACATCGCATCGAGCGAGCACGTACTCGGATGGAAGCCGGTCTTGTCGATCAGGATGATCTGGACGAAGAGCATCAGGGGCTCGCGCCAGCCGAACTGTTCGCCAGGCTCAAGGCGAGCATGCCCGACGGCGAGGATCTCGTCGTGACCCATGGCGATGCCTGCTTGCCGAATATCATGGTGGAAAATGGCCGCTTTTCTGGATTCATCGACTGTGGCCGGCTGGGTGTGGCGGACCGCTATCAGGACATAGCGTTGGCTACCCGTGATATTGCTGAAGAGCTTGGCGGCGAATGGGCTGACCGCTTCCTCGTGCTTTACGGTATCGCCGCTCCCGATTCGCAGCGCATCGCCTTCTATCGCCTTCTTGACGAGTTCTTCTGAGCGGGACTCTGGGGTTCGAAATGACCGACCAAGCGACGCCCAACCTGCCATCACGAGATTTCGATTCCACCGCCGCCTTCTATGAAAGGTTGGGCTTCGGAATCGTTTTCCGGGACGCCGGCTGGATGATCCTCCAGCGCGGGGATCTCATGCTGGAGTTCTTCGCCCACCCTAGGGGGAGGCTAACTGAAACACGGAAGGAGACAATACCGGAAGGAACCCGCGCTATGACGGCAATAAAAAGACAGAATAAAACGCACGGTGTTGGGTCGTTTGTTCATAAACGCGGGGTTCGGTCCCAGGGCTGGCACTCTGTCGATACCCCACCGAGACCCCATTGGGGCCAATACGCCCGCGTTTCTTCCTTTTCCCCACCCCACCCCCCAAGTTCGGGTGAAGGCCCAGGGCTCGCAGCCAACGTCGGGGCGGCAGGCCCTGCCATAGCCTCAGGTTACTCATATATACTTTAGATTGATTTAAAACTTCATTTTTAATTTAAAAGGATCTAGGTGAAGATCCTTTTTGATAATCTCATGACCAAAATCCCTTAACGTGAGTTTTCGTTCCACTGAGCGTCAGACCCCGTAGAAAAGATCAAAGGATCTTCTTGAGATCCTTTTTTTCTGCGCGTAATCTGCTGCTTGCAAACAAAAAAACCACCGCTACCAGCGGTGGTTTGTTTGCCGGATCAAGAGCTACCAACTCTTTTTCCGAAGGTAACTGGCTTCAGCAGAGCGCAGATACCAAATACTGTCCTTCTAGTGTAGCCGTAGTTAGGCCACCACTTCAAGAACTCTGTAGCACCGCCTACATACCTCGCTCTGCTAATCCTGTTACCAGTGGCTGCTGCCAGTGGCGATAAGTCGTGTCTTACCGGGTTGGACTCAAGACGATAGTTACCGGATAAGGCGCAGCGGTCGGGCTGAACGGGGGGTTCGTGCACACAGCCCAGCTTGGAGCGAACGACCTACACCGAACTGAGATACCTACAGCGTGAGCTATGAGAAAGCGCCACGCTTCCCGAAGGGAGAAAGGCGGACAGGTATCCGGTAAGCGGCAGGGTCGGAACAGGAGAGCGCACGAGGGAGCTTCCAGGGGGAAACGCCTGGTATCTTTATAGTCCTGTCGGGTTTCGCCACCTCTGACTTGAGCGTCGATTTTTGTGATGCTCGTCAGGGGGGCGGAGCCTATGGAAAAACGCCAGCAACGCGGCCTTTTTACGGTTCCTGGCCTTTTGCTGGCCTTTTGCTCACATGTTCTTTCCTGCGTTATCCCCTGATTCTGTGGATAACCGTATTACCGCCATGCAT";
	document.getElementById('markers').value = "Hu_CMV_Prom 1 589 arrow_on Filled Lime 12\nMCS 591 671 arrow_off Filled Black 12\nEGFP 679 1398 arrow_on Filled Green 12\npEGFP-N_seq 745 725 arrow_on Filled Lime 12\npEGFP-C_seq 1332 1353 arrow_on Filled Lime 12\nSV40_PolyA 1552 1602 arrow_off Filled Black 12\nf1_Ori 1781 2087 arrow_on Filled Silver 12\nBac-SV40_Prom 2166 2546 arrow_on Filled Lime 12\nKan/Neo 2629 3423 arrow_on Filled Purple 12\nHSV_TK_PolyA 3658 3867 arrow_off Filled Black 12\npUC_Ori 4008 4651 arrow_off Filled Silver 12";
	document.getElementById('enzymes').value = "AseI 8:SnaBI 341:BsrGI 1389:NotI 1402:XbaI 1412:AflII 1640:DraIII 1874:StuI 2579:Eco0109I 3856:ApaLI 4362:";	
	}
</script>


<link rel="stylesheet" href="includes/styles.css" />
</head>

<body>

<?php
$name = "value='Unnamed' onfocus='clearDefault(this);'";
$markers = "";
$enzymes = "";
?>
<div style="width:720px;">
    <div class="tabs">
        <ul>
            <li><a href='index.php'><span>Home</span></a></li>
			<?php 	$login_status = isset($login_status) ? $login_status : "false";
					if($login_status == "true") echo "<li><a href='plasmid_management.php'><span>Manage plasmids</span></a></li>"; ?>
            <li><a href='QandA.php' target="_blank"><span>Q & A</span></a></li>
			<li><a href='soap_server.php'><span>SOAP</span></a></li>
            <li><a href="login.php"><span><?php if($login_status == "true") echo "Log out"; else echo "Log in/Register";  ?></span></a></li>
        </ul>
    </div>
  <h1>Draw Custom Plasmid Map</h1>
  <span style="font-size:10pt;">With great thanks to Dr. Malay Basu for providing the original <a href='http://www.bioinformatics.org/savvy/'>Savvy</a> source code, which was adapted for this website.</span> 
</div>

<div id="plasmid_map_display_box" style="position:absolute; left:690px; top:80px;"></div>
<div id="plasmid_edit_div" style="position:absolute; left:20px; top:130px;">
<form name='savvy_form' action='/cgi-bin/savvy.cgi' target='_blank' method='post'>
    Name:<br />
    <input type='text' name='plasmid_name' id='plasmid_name' <?php echo $name; ?> /><br /><br />
	Plasmid Sequence:<br />
	<textarea name='plasmid_sequence' id='plasmid_sequence' rows=8 cols=90 wrap='no' style='font-size:9pt;' onchange='disable_save()'></textarea><br /><br />
    Markers:<br />
    <textarea name='markers' id='markers' rows=8 cols=90 style='font-size:9pt;' ><?php echo $markers; ?></textarea><br />
        
    
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
	<textarea name='enzymes' id='enzymes'rows=2 cols=90 style='font-size:9pt;' onchange='disable_save()'><?php echo $enzymes; ?></textarea><br />
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
    <input type="button" name="add_to_enzyme_list" value="Add to list" onClick="add_enzyme()" /><br />
    <input type='button' id='redraw' onclick='redrawing()' value='Draw' style="float:right" />
	<br />
    <input type="hidden" name="plasmid_size" id="plasmid_size" value='' />
    <input type='hidden' name='line_thickness' id='line_thickness' value='0.5' />
	<input type='hidden' name='shape' id='shape' value='circular' />
	<div id='saved_alert'></div>
</form>
<br />
<input type="button" style="margin-left:582px;" value="Sample Plasmid" onclick="sample_data()" />
</div>
<div style="position:absolute; left:500px; top:800px;"><?php include("includes/footer.php"); ?></div>
</body>
</html>