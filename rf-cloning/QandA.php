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

$svg_file = file_get_contents("images/rf-diagram.svg");
$svg_file = preg_replace("/[\n\r]/","",$svg_file);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="keywords" content="rf-cloning, restriction free, restriction free cloning, restriction-free cloning, plasmid, overlap extension, overlap extension PCR" />
<meta name="description" content="Restriction free cloning question and answer." />
<link rel="icon" type="image/ico" href="favicon.ico" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>RF Cloning</title>
<link rel="stylesheet" href="includes/styles.css" />
<script src="classes/ajaxObj.js" language="javascript" type="text/javascript"></script>
<script src="classes/raphael_uncompressed.js" language="javascript" type="text/javascript"></script>
<script src="javascript/javascripts.js" language="javascript" type="text/javascript"></script>
<script src="javascript/svg2raphael.js" language="javascript" type="text/javascript"></script>
<script language="javascript" type="text/javascript">
function rf_diagram()
	{
	//var rf_diagram = Raphael("rf_diagram", 800, 450);	
	svg_rf_diagram = new Raphael("rf_diagram", 947, 615); //GLOBAL		
	var $params = 'svg_text=<?php echo $svg_file; ?>&div_id=rf_diagram&width=800&height=450';
	$svg2raphael.update($params,"POST");
	}

var $email = new ajaxObject("functions/ajax/email.php",get_email)

function get_email(responseText, responseStatus)
	{
	if (responseStatus == 200)
		{			
		document.getElementById('email1').innerHTML = responseText + "let me know</a>";
		document.getElementById('email2').innerHTML = responseText + "Steve Bond</a>";
		}
	}
</script>
<script src="javascript/analytics.js" language="javascript" type="text/javascript"></script>
</head>

<body onload="rf_diagram(); $email.update('check=ok','POST');">
<div class="tabs">
    <ul>
        <li><a href='index.php'><span>Home</span></a></li>
			<?php 	$login_status = isset($login_status) ? $login_status : "false";
					if($login_status == "true") echo "<li><a href='plasmid_management.php'><span>Manage plasmids</span></a></li>"; ?>
        <li><a href='savvy.php'><span>Savvy</span></a></li>
        <li><a href='soap_server.php'><span>SOAP</span></a></li>
        <li><a href="login.php"><span><?php if($login_status == "true") echo "Log out"; else echo "Log in/Register";  ?></span></a></li>
    </ul>
</div>
<h4>Please note, this site is provided as is, with no warranty. I will only make feature updates that I think are stable, but bugs have been known to lurk in the dark recesses... if you notice something amiss or feel like an important feature should be added, <span id="email1">let me know</span>!</h4>  
    
<p>Q: What is restriction-free cloning?</p>
<p>A: RF cloning (aka overlap extension PCR cloning) is a PCR-based method for the creation of custom DNA plasmids. Essentially, it allows for the insertion of any sequence into any position within any plasmid, independent of restriction enzyme recognition sites or homologous recombination sites within these sequences*. To accomplish this, a pair of hybrid primers are designed containing complementary sequence to both the desired insert and the target plasmid. These primers are used to amplify the insert from an appropriate source using high-fidelity PCR conditions. The resulting product is purified, and used as a 'mega-primer' in a secondary PCR reaction, with the target plasmid acting as template. The plasmid is amplified in both directions, and the mega-primers act as long single-stranded overhangs that allow the complementary strands of the plasmid to anneal, forming a nicked hybrid molecule. DpnI is used to degrade any parental plasmid (based on its methylation), and the final product is used to transform competent bacterial strains normally. This process is depicted below.</p>
		<p class='indent note'>*In practice, certain constraints exist. For example, very large sequences (>8KB), highly repetitive sequences, and sequences with significant secondary structure can all interfere with an RF-cloning project.</p>
<br />

<div id="rf_diagram"></div>

<p>Q: How do I use the website to design an RF cloning project?</p>
<p>A: From the home page, paste your desired insert sequence into the upper text area, and your destination plasmid sequence in the lower text area. You can also select from popular plasmid sequences, or any sequences you have saved to the database from the associated pull-down menu. Indicate the insertion points within the plasmid, either by adding two exclamation "!" points directly into the sequence, or by specifying the positions in the appropriate text boxes. Click run, and the software will generate custom hybrid primers to accomplish your cloning project. You can further manipulate the primers by shifting the insertion points, or by increasing/decreasing the length of the sequences complementary to the plasmid or insert, using the provided arrow buttons. Annealing temperatures are calculated for the primary and secondary PCR reactions, the expected length of the primary PCR product is given, as well as recommendations for the secondary PCR conditions. It has been my experience that the high-fidelity long-read polymerases iProof and Phusion (available from Bio-Rad and NEB respectively) produce the best results, and the extension times calculated are based on these enzymes. Other high fidelity polymerases can also work, although the manufacturer's instructions should be followed to determine appropriate PCR conditions.</p>
<br />

<p>Q: What is a good starting protocol for my RF cloning project?</p>
<p>A: By default, the hybrid primers designed for you will be at least 40bps long, with a T<sub>m</sub> of at least 55&deg;C for the primary PCR (amplification of insert) and at least 60&deg; for the secondary PCR (extension around the plasmid). If you notice a big difference in T<sum>m</sub> between the two target sequences, or the two plasmid sequences, it may be a good idea to adjust the lengths of the respective primers to get them to within a couple degrees of one another. </p>
<h3> Primary PCR</h3>

<table><tr><td style="padding-right:40px;">
<table border="1">
	<tr>
		<th colspan='3'>PCR components</th>
	</tr>
	<tr>
		<td>5X</td><td>PCR Buffer</td><td>10μl</td>
	</tr>
	<tr> 
		<td style="padding-right:10px;">10mM</td><td>dNTP mix</td><td>1μl</td>
	</tr>
	<tr>

		<td>10μM</td><td>Fwd Primer </td><td>2.5μl</td>
	</tr>
	<tr>
		<td>10μM</td><td>Rev Primer </td><td>2.5μl</td>
	</tr>
	<tr>
		<td> </td><td>DNA template</td><td>Xμl</td>
	</tr>
<tr>
		<td>2U/μl</td><td style="padding-right:10px;">iProof or Phusion</td><td>0.5μl</td>
	</tr>
<tr>
		<td> </td><td>H<sub>2</sub>O</td><td>To 50μl</td>
	</tr>
</table></td>
<td style="vertical-align:top"><table border="1">
	<tr>
		<th colspan='4'>Thermal Cycler Conditions</th>
	</tr>
	<tr>
		<td>Denature</td><td>98&deg;C</td><td>30sec</td><td>1X</td>
	</tr>
<tr style="background:#CCC"> 
		<td>Denature</td><td>98&deg;C</td><td>8sec</td><td rowspan="3" style="vertical-align:middle">35X</td>
</tr>
	<tr style="background:#CCC">
		<td>Anneal</td><td style="padding-right:10px;">~55-60&deg;C</td><td>20sec</td>
	</tr>
	<tr style="background:#CCC">
		<td>Extension</td><td>72&deg;</td><td style="padding-right:10px;">15-30sec/kb</td>
	</tr>
	<tr>
		<td style="padding-right:10px;">Final Extension</td><td>72&deg;C</td><td>5min</td><td>1X</td>
	</tr>
</table></td></tr>
</table>
<p>It's generally a good idea to do 50μl reactions for the primary PCR, to ensure you generate enough megaprimer for the secondary PCR. Gel extract the PCR product, and determine its concentration.</p>
<p>For the secondary PCR reaction, you should aim for a molar insert:plasmid ratio of 20, with ~100ng of parental plasmid (the defaults calculated for you are a good starting point).</p>
<h3>Secondary PCR</h3>
<table><tr><td style="padding-right:40px;">
<table border="1">
	<tr>
		<th colspan='3'>PCR components</th>
	</tr>
	<tr>
		<td>5X</td><td>PCR Buffer</td><td>4μl</td>
	</tr>
	<tr> 
		<td style="padding-right:10px;">10mM</td><td>dNTP mix</td><td>0.4μl</td>
	</tr>
	<tr>
		<td> </td><td>Megaprimer</td><td>Xμl</td>
	</tr>
	<tr>
		<td> </td><td>Destination Plasmid</td><td>Xμl</td>
	</tr>
<tr>
		<td>2U/μl</td><td style="padding-right:10px;">iProof or Phusion</td><td>0.2μl</td>
	</tr>
<tr>
		<td> </td><td>H<sub>2</sub>O</td><td>To 20μl</td>
	</tr>
</table></td>
<td style="vertical-align:top"><table border="1">
	<tr>
		<th colspan='4'>Thermal Cycler Conditions</th>
	</tr>
	<tr>
		<td>Denature</td><td>98&deg;C</td><td>30sec</td><td>1X</td>
	</tr>
<tr style="background:#CCC"> 
		<td>Denature</td><td>98&deg;C</td><td>8sec</td><td rowspan="3" style="vertical-align:middle">15X</td>
</tr>
	<tr style="background:#CCC">
		<td>Anneal</td><td style="padding-right:10px;">~60&deg;C</td><td>20sec</td>
	</tr>
	<tr style="background:#CCC">
		<td>Extension</td><td>72&deg;</td><td style="padding-right:10px;">15-30sec/kb</td>
	</tr>
	<tr>
		<td style="padding-right:10px;">Final Extension</td><td>72&deg;C</td><td>5min</td><td>1X</td>
	</tr>
</table></td></tr>
</table>
<p>When the secondary PCR has completed, add 1μl of DpnI directly to the reaction mix (don't worry, it's 100% active in PCR buffers) and incubate for 2 hours at 37&deg;C, followed by 20mins at 80&deg;. Your sample should now be ready to transform into your favourite competent cells. Be aware that rf-cloning reactions tend to be fairly low efficiency, so high competency cells are beneficial, but by no means necessary. If you are using standard sub-cloning grade cells, you may find it advantageous to spread the entire transformation reaction out across 2-4 selective plates.</p><br />

<!-- <p>Q: Yikes!! It didn't work! What now?</p>
<p>A: Don't panic quite yet. Here is a little <a href="trouble.php">trouble shooting guide</a> that may help you along.</p> 
-->
<p>Q: How does the software calculate annealing tempurature?</p>
<p>A: DNA melting temperatures are approximated with the Wallace-Itakura rule for short sequences less than 14bps:</p>
<p>T<sub>m</sub> = 4&deg;C &times; (# of G's and C's) + 2&deg;C &times; (# of A's and T's)</p>

<p>For sequences &ge; 14 base pairs (and your primers should be at least this big), I've borrowed a function from <a href="http://www.biophp.org/minitools/melting_temperature/demo.php">BioPHP</a> that uses nearest-neighbor thermodynamics to approximate T<sub>m</sub>. The equation is influenced by salt and primer concentrations, and I've hard-coded 500nM as the primer conc. and 50mM as the monovalent cation conc. Magnesium is also an important variable in the equation, and I've set its value to zero for the purposes of calculating annealing temperature. Obviously you're going to be adding Mg<sup>2+</sup> to your PCR reaction if you want the polymerase to do its thing, and this will increase the T<sub>m</sub> by 5-10&deg;C. Since you want your annealing temp 5-10&deg;C <i>below</i> T<sub>m</sub>, I think you'll find that the software generated values meet your needs nicely.</p>
<br />

<p>Q: How about a reference list for some further reading?</p>
<p>A: Absolutely! This should get you started</p>
<ul>
	<li>van den Ent F, Lowe J 2006 <a href="http://www.ncbi.nlm.nih.gov/pubmed/16480772">RF cloning: a restriction-free method for inserting target genes into plasmids.</a> J Biochem Biophys Methods 67(1):67-74.</li>
    <li>Unger T, Jacobovitch Y, Dantes A, Bernheim R, Peleg Y 2010 <a href="http://www.ncbi.nlm.nih.gov/pubmed/20600952">Applications of the Restriction Free (RF) cloning procedure for molecular manipulations and protein expression.</a> J Struct Biol 172(1):34-44.</li>
    <li>Bryksin AV, Matsumura I 2010 <a href="http://www.ncbi.nlm.nih.gov/pubmed/20569222">Overlap extension PCR cloning: a simple and reliable way to create recombinant plasmids</a>. Biotechniques 48(6):463-5.</li>
</ul><br />

<p>Q: Can I return to projects later, after I've left the site?</p>
<p>A: Yep. All projects are assigned a unique 32 byte hash code at runtime, and saved to the database. You'll find the code embedded in the URL on the project page, so you can bookmark your projects and return whenever you like. Alternately, you can register an account with the site, and use the plasmid management system I've built to keep your projects and backbones organized. See below for more on that.</p> 
<br />

<p>Q: How do I manage my plasmids and projects?</p>
<p>A: When you are satisfied with the design of your project, you can save it to your account (provided you have registered first) by clicking the save button at the bottom of the page. These can then be accessed from the plasmid management system, accessible via the 'manage plasmids' tab. All of your past projects can be found in the projects drop-down menu. From the plasmid management system, you are also able to design and save your own plasmid backbones; click the 'add new backbone' button to display the blank plasmid form. Copy your plasmid sequence into the provided text area, give it a name, and indicate the plasmid's features and restriction sites. Common plasmid features can be annotated automatically by clicking the 'Auto Find' button, but you should always manually inspect any results for potential inaccuracies. If you ever find any mistakes in the features database, or obvious omissions, please don't hesitate to contact me so I can make the corrections required. An output of the current features database can be found <a href="/cgi-bin/features_list.cgi">here</a></p>
<br />

<p>Q: I really like these plasmid maps, but sometimes the feature names overlap with one another, or extend outside of the plasmid map*. How can I fix this?</p>
<p>A: At the bottom of all plasmid maps, you will notice a small print icon. Clicking on this icon will load the plasmid map in a new browser window, and from there you can save the file in the SVG format. SVG stands for 'scalable vector graphic', and the format can be opened in Adobe Illustrator, CorelDRAW, or the freely available Inkscape (<a href="http://www.inkscape.org">www.inkscape.org</a>). You are not currently allowed to upload the modified files back onto the server, but you can of course print them for your own records.</p>
<p class='indent note'>*If you happen to be a wiz at perl programming and would like to take a crack at fixing this, I'd love to here from you.</p>
<br />

<p>Q: My boss just freaked out at me because I designed my rf-cloning project on your site. It's really important/sensitive/dangerous stuff, and don't take this the wrong way, but we don't trust the internet... Errrmmm, help?</p>
</p>A: Okay, two things. First, if you are super worried about any projects you've run on the server, please don't hesitate to get in touch with me and we'll get all the important/sensitive/dangerous stuff deleted from the database. I've done what I can to secure the server, but hey I get it, even the FBI gets hacked. Second, projects sent directly to the server via <a href="soap_server.php">SOAP requests</a> are NOT saved to the database, so you might want to go that route from now on. Or alternatively, see the next question...</p><br />

<p>Q: I want to clone rf-cloning.org on my own server. Can I have a copy?</p>
<p>A: You sure can. All the source files are available for download <a href="download_site.php">right here</a>!</p><br />  

<p>Q: I've been using your site, and would like to reference it in a publication I'm putting together. How should I do that?</p>
<p>A: Well... I've submitted the site to the Journal of Nucleic Acid Research for publication in their next web server issue (this would be June 2012), but that doesn't help much right now. I'd certainly like to be reference though, so maybe fire me an email and we'll work something out.</p> 
<br />

<p>Q: How can I contact you?</p>
<p>A: Please direct all questions, comments, or concerns to <span id="email2">Steve Bond</span></p>


<?php include("includes/footer.php"); ?>
</body>
</html>