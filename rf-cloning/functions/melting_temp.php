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
// This program approximates the melting temperature of a DNA primer, more info can be found at http://www.biophp.org/minitools/melting_temperature/
function melting_temp($sequence)
	{
	$sequence = strtoupper($sequence);
	$g_count = substr_count($sequence,"G");
	$c_count = substr_count($sequence,"C");
	$at_count = strlen($sequence) - ($g_count + $c_count);
	$GC_percent = ($g_count + $c_count)/strlen($sequence);
	
	if (strlen($sequence) < 14)
		{
        $melting_temp = round(2 * ($at_count) + 4 * ($g_count + $c_count));
        }
	else
		{
		$melting_temp = base_stack($sequence);
		//$melting_temp = (64.9 + (41*(($g_count + $c_count - 16.4)/strlen($sequence))));
		}
	
	return $melting_temp;
	}

function base_stack($c)
	{
	$conc_primer=500; //nM
	$conc_salt=50; //mM Na
	$conc_mg=0; //mM
	if (CountATCG($c)!= strlen($c))
		{print "The oligonucleotide is not valid";return;}
	$h=$s=0;
	
	// enthalpy values
	$array_h["AA"]= -7.9;
	$array_h["AC"]= -8.4;
	$array_h["AG"]= -7.8;
	$array_h["AT"]= -7.2;
	$array_h["CA"]= -8.5;
	$array_h["CC"]= -8.0;
	$array_h["CG"]=-10.6;
	$array_h["CT"]= -7.8;
	$array_h["GA"]= -8.2;
	$array_h["GC"]=-10.6;
	$array_h["GG"]= -8.0;
	$array_h["GT"]= -8.4;
	$array_h["TA"]= -7.2;
	$array_h["TC"]= -8.2;
	$array_h["TG"]= -8.5;
	$array_h["TT"]= -7.9;
	// entropy values
	$array_s["AA"]=-22.2;
	$array_s["AC"]=-22.4;
	$array_s["AG"]=-21.0;
	$array_s["AT"]=-20.4;
	$array_s["CA"]=-22.7;
	$array_s["CC"]=-19.9;
	$array_s["CG"]=-27.2;
	$array_s["CT"]=-21.0;
	$array_s["GA"]=-22.2;
	$array_s["GC"]=-27.2;
	$array_s["GG"]=-19.9;
	$array_s["GT"]=-22.4;
	$array_s["TA"]=-21.3;
	$array_s["TC"]=-22.2;
	$array_s["TG"]=-22.7;
	$array_s["TT"]=-22.2;
	
	// effect on entropy by salt correction; von Ahsen et al 1999
	// Increase of stability due to presence of Mg;
	$salt_effect= ($conc_salt/1000)+(($conc_mg/1000) * 140);
	// effect on entropy
	$s+=0.368 * (strlen($c)-1)* log($salt_effect);
	
	// terminal corrections. Santalucia 1998
	$firstnucleotide=substr($c,0,1);
	if ($firstnucleotide=="G" or $firstnucleotide=="C"){$h+=0.1; $s+=-2.8;}
	if ($firstnucleotide=="A" or $firstnucleotide=="T"){$h+=2.3; $s+=4.1;}
	
	$lastnucleotide=substr($c,strlen($c)-1,1);
	if ($lastnucleotide=="G" or $lastnucleotide=="C"){$h+=0.1; $s+=-2.8;}
	if ($lastnucleotide=="A" or $lastnucleotide=="T"){$h+=2.3; $s+=4.1;}
	
	// compute new H and s based on sequence. Santalucia 1998
	for($i=0; $i<strlen($c)-1; $i++)
		{
		$subc=substr($c,$i,2);
		$h+=$array_h[$subc];
		$s+=$array_s[$subc];
		}
	$tm=((1000*$h)/($s+(1.987*log($conc_primer/2000000000))))-273.15;
	//print "Tm:                 <font color=880000><b>".round($tm,1)." &deg;C</b></font>";
	//print  "\n<font color=008800>  Enthalpy: ".round($h,2)."\n  Entropy:  ".round($s,2)."</font>";
	return $tm;
	}



function CountATCG($c)
	{
	$cg=substr_count($c,"A")+substr_count($c,"T")+substr_count($c,"G")+substr_count($c,"C");
	return $cg;
	}







?>