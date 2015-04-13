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
$a = substr_count($_POST['sequence_export'],"A");
$c = substr_count($_POST['sequence_export'],"C");
$t = substr_count($_POST['sequence_export'],"T");
$t += substr_count($_POST['sequence_export'],"U");
$g = substr_count($_POST['sequence_export'],"G");
$other = $_POST['length_export'] - $a - $t - $c - $g;

//f1_origin 444 138 arrow_on Filled Silver 12 
$marker_array = explode("\r",$_POST['marker_export']);
$annotation_array = array(	"Silver" => "Ori", 
							"Purple" => "Antibiotic_res", 
							"Lime" => "Promoter", 
							"Yellow" => "IRES", 
							"Black" => "Primer", 
							"Aqua" => "Operator", 
							"Olive" => "Enhancer", 
							"Blue" => "Repressor", 
							"Maroon" => "Regulator", 
							"Orange" => "ORF", 
							"Teal" => "Recombination-Site", 
							"Pink" => "Tag", 
							"Brown" => "Cut_site");
?>
<pre>
LOCUS <?php echo $_POST['name_export']." ".$_POST['length_export']." bp
";?>
FEATURES		Location/Qualifiers
<?php 
foreach($marker_array as $row)
	{
	$values_array = explode(" ",$row);

	$position = $values_array[1] < $values_array[2] ? $values_array[1]."..".$values_array[2] : "complement(".$values_array[2]."..".$values_array[1].")";
	$annotation = array_key_exists($values_array[5],$annotation_array) ? $annotation_array[$values_array[5]] : "Other";
	
echo "     ".$annotation."    ".$position."
                     /gene=\"".trim($values_array[0])."\"
";	
	}

?>
BASE COUNT <?php echo $a; ?> a   <?php echo $c; ?> c   <?php echo $g; ?> g   <?php echo $t; ?> t   <?php echo $other; ?> others
ORIGIN
<?php echo $_POST['sequence_export']."
"; ?>
//
</pre>