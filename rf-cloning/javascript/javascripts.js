// JavaScript Document
/*-------------------------------------------------------
COPYRIGHT (c) Steve Bond 2009-2014
Distributed under the GNU general public license, version 3:
http://www.gnu.org/licenses
Web site: http://www.rf-cloning.com
Author: Steve R. Bond, Ph.D <biologyguy@gmail.com>	

Plasmid maps are generated using a modified version of Savvy
Web site: http://www.bioinformatics.org/savvy/
Author: Malay Kumar Basu, PhD.
	
I grabbed a lot of the functionallity from 'Sequence 
Massager' to manipulate nucleic acid primary sequences.
http://www.attotron.com/cybertory/analysis/seqMassager.htm
              
COPYRIGHT (c) 1998, 2006
Attotron Biotechnologies Corporation
Distributed under the GNU general public license:
http://www.fsf.org/licensing/licenses/gpl.txt

Web site: http://www.cybertory.org
Author: Robert M. Horton, Ph.D <rmhorton@attotron.com>
Funded by: NIH SBIR grant #R44 RR13645 02A2
--------------------------------------------------------*/
// To reverse complement, run reverse() and complement() functions sequentially
function reverse(aSeq) 
	{
	// Returns reverse of given sequence (not complemented)
	aSeq = aSeq.replace(/[0-9]/g,"");
	var seqArray = aSeq.split("");
	seqArray.reverse();
	return seqArray.join("");
	}
	
/*********************************************************************************/
function complement(aSeq)
	{
	// returns complemment of given sequence (not reversed)
	aSeq = aSeq.toUpperCase();
	aSeq = aSeq.replace(/[0-9]/g,"");
	aSeq = aSeq.replace(/[^ACGTUNRYSWMKBDHV \t\n\r]/g,"x");
		// unknown characters get "x"ed
	aSeq = aSeq.replace(/A/g,"t");
	aSeq = aSeq.replace(/C/g,"g");
	aSeq = aSeq.replace(/G/g,"c");
	aSeq = aSeq.replace(/T/g,"a");
	aSeq = aSeq.replace(/U/g,"a");
	aSeq = aSeq.replace(/N/g,"n");
	aSeq = aSeq.replace(/R/g,"y");
	aSeq = aSeq.replace(/Y/g,"r");
	aSeq = aSeq.replace(/S/g,"s");
	aSeq = aSeq.replace(/W/g,"w");
	aSeq = aSeq.replace(/M/g,"k");
	aSeq = aSeq.replace(/K/g,"m");
	aSeq = aSeq.replace(/B/g,"v");
	aSeq = aSeq.replace(/D/g,"h");
	aSeq = aSeq.replace(/H/g,"d");
	aSeq = aSeq.replace(/V/g,"b");
	return aSeq.toUpperCase();
	}

/*********************************************************************************/
function toRNA(aSeq)
	{
	// Changes Ts to Us...
	aSeq = aSeq.replace(/T/g,"U");
	aSeq = aSeq.replace(/t/g,"u");
	return aSeq.toUpperCase();
	}	
/*********************************************************************************/	
function loadSeq()
	{
	if(window.opener){document.theForm.target.value=window.opener.document.theForm.target.value;}
	}

/*********************************************************************************/
function get_selected_radio($radioObj) 
	{
	if(!$radioObj)
		{
		return "";
		}
	var $radioLength = $radioObj.length;
	if($radioLength == undefined)
		{
		if($radioObj.checked)
			{
			return $radioObj.value;
			}
		else
			{
			return "";
			}
		}		
	for(var $i = 0; $i < $radioLength; $i++) 
		{
		if($radioObj[$i].checked) 
			{
			return $radioObj[$i].value;
			}
		}
	return "";
	}

/*********************************************************************************/
function isInt($value)
	{
  	if((parseFloat($value) == parseInt($value)) && !isNaN(parseInt($value)))
		{
     	return true;
 		} 
	else 
		{
      	return false;
 		}
	}

/*********************************************************************************/	
function getCookie(c_name)
	{
	if (document.cookie.length>0)
	  {
	  c_start=document.cookie.indexOf(c_name + "=");
	  if (c_start!=-1)
		{
		c_start=c_start + c_name.length+1;
		c_end=document.cookie.indexOf(";",c_start);
		if (c_end==-1) c_end=document.cookie.length;
		return unescape(document.cookie.substring(c_start,c_end));
		}
	  }
	return "";
	}
/*********************************************************************************/	
function clearDefault($default_text) 
	{
  	if ($default_text.defaultValue==$default_text.value) 
		{
		$default_text.value = ""
		}
	}
	
/*********************************************************************************/
function plasmid_focus($id,$database)
	{
	if ($id == "nothing")
		{
		var $check = document.getElementById('features_display');
		if ($check != null)
			{
			document.getElementById('target_sequence').value = "";
			document.getElementById('plasmid_sequence').value = "";
			document.getElementById('plasmid_map_display_box').innerHTML = "";	
			document.getElementById('features_display').innerHTML = "";
			}
		
		else
			{
			document.getElementById('plasmid_map_display_box').innerHTML = "";
			document.getElementById('plasmid_edit_div').innerHTML = "";	
			}
		
		$check = document.getElementById('backbone_id');
		if ($check != null)
			{
			document.getElementById('backbone_id').value = "blank";
			}
		}
	else
		{
		$id_array = $id.split("|");
		var $post_parameters = "id="+$id_array[0]+"&database="+$database;	
		$savvy_info_ajax.update($post_parameters, 'POST');
		}
	}

/*********************************************************************************/
function open_colored_sequence_window($id,$database)
	{
	window.open("functions/ajax/colored_sequence_map.php?id=" + $id + "&database=" + $database + "","Colored Sequence Map","status=1")			
	}
	
/*********************************************************************************/
function clear_save_alert()
	{
	document.getElementById("saved_alert").innerHTML = "";
	
	var $check = document.getElementById("insert_shift_alert");
	if ($check != null)
		{
		document.getElementById("insert_shift_alert").innerHTML = "";
		}
	}

/*********************************************************************************/
function trim($str) 
	{
	$str = $str.replace(/^\s+/, '');
	for (var i = $str.length - 1; i >= 0; i--) 
		{
		if (/\S/.test($str.charAt(i))) 
			{
			$str = $str.substring(0, i + 1);
			break;
			}
		}
	return $str;
	}
	
/*********************************************************************************/
function add_basepair($primer_dir_code)
	{
	var $fwd_primer_seq = document.getElementById('fwd_primer_database').value;
	var $rev_primer_seq = document.getElementById('rev_primer_database').value;
	var $plasmid_seq = document.getElementById('plasmid_sequence').value;
	var $insert_seq = document.getElementById('insert_sequence').value;
	var $insert_sites = document.getElementById('insert_sites').value;
	var $post_parameters = 'primer_dir_code='+$primer_dir_code+'&fwd_primer_seq='+$fwd_primer_seq+'&rev_primer_seq='+$rev_primer_seq+'&plasmid_seq='+$plasmid_seq+'&insert_seq='+$insert_seq+'&insert_sites='+$insert_sites;
	$add_basepair.update($post_parameters,'POST');	
	}
	
/*********************************************************************************/
function sub_basepair($primer_dir_code)
	{
	var $fwd_primer_seq = document.getElementById('fwd_primer_database').value;
	var $rev_primer_seq = document.getElementById('rev_primer_database').value;
	var $plasmid_seq = document.getElementById('plasmid_sequence').value;
	var $insert_seq = document.getElementById('insert_sequence').value;
	var $insert_sites = document.getElementById('insert_sites').value;
	var $post_parameters = 'primer_dir_code='+$primer_dir_code+'&fwd_primer_seq='+$fwd_primer_seq+'&rev_primer_seq='+$rev_primer_seq+'&plasmid_seq='+$plasmid_seq+'&insert_seq='+$insert_seq+'&insert_sites='+$insert_sites;
	$sub_basepair.update($post_parameters,'POST');	
	}
	
/*********************************************************************************/
function shift_insert($shift_insert_code)
	{
	var $insert_sequence = document.getElementById('insert_sequence').value;
	var $plasmid_sequence = document.getElementById('plasmid_sequence').value;
	var $insert_name = document.getElementById('insert_name').value;
	var $orientation = document.getElementById('orientation').value;
	var $arrow = document.getElementById('arrow').value;
	var $backbone_database = document.getElementById('backbone_database').value;
	var $backbone_id = document.getElementById('backbone_id').value;
	var $insert_sites = document.getElementById('insert_sites').value;
	var $post_parameters = 'shift_insert_code='+$shift_insert_code+'&target='+$insert_sequence+'&plasmid='+$plasmid_sequence+'&insert_name='+$insert_name+'&orientation='+$orientation+'&arrow='+$arrow+'&database='+$backbone_database+'&backbone_id='+$backbone_id+'&insert_sites='+$insert_sites;
	$shift_insert.update($post_parameters,'POST');
	}

/********************************************************************************************************/
 var MAX_DUMP_DEPTH = 10;
      
function dumpObj(obj, name, indent, depth) 
	{
	if (depth > MAX_DUMP_DEPTH) 
		{
		return indent + name + ": <b>Maximum Depth Reached</b><br />";
		}
	if (typeof obj == "object") 
		{
		var child = null;
		var output = indent + name + "<br />";
		indent += "&nbsp;&nbsp;&nbsp;";
		for (var item in obj)
			{
			try 
				{
				child = obj[item];
				} 
			catch (e) 
				{
				child = "<b>Unable to Evaluate</b>";
				}
			if (typeof child == "object") 
				{
				output += dumpObj(child, item, indent, depth + 1);
				} 
			else 
				{
				output += indent + item + ": " + child + "<br />";
				}
			}
		return output;
		} 
	else 
		{
		return obj;
		}
	}


function utf8_encode(argString) 
	{
	// Encodes an ISO-8859-1 string to UTF-8
	// version: 1103.1210
	// discuss at: http://phpjs.org/functions/utf8_encode
	var string = (argString + '');
	var utftext = "", start, end, stringl = 0;
	start = end = 0;
	stringl = string.length;
	for (var n = 0; n < stringl; n++) 
		{
		var c1 = string.charCodeAt(n);
		var enc = null;
		if (c1 < 128) 
			{
			end++;
			} 
		else if (c1 > 127 && c1 < 2048) 
			{
			enc = String.fromCharCode((c1 >> 6) | 192)
			+ String.fromCharCode((c1 & 63) | 128);
			} 
		else 
			{
			enc = String.fromCharCode((c1 >> 12) | 224)
			+ String.fromCharCode(((c1 >> 6) & 63) | 128)
			+ String.fromCharCode((c1 & 63) | 128);
			}
		if (enc !== null) 
			{
			if (end > start) 
				{
				utftext += string.slice(start, end);
				}
			utftext += enc;
			start = end = n + 1;
			}
		}
	if (end > start) 
		{
		utftext += string.slice(start, stringl);
		}
	return utftext;
	}

function md5(str) 
	{
	// Calculate the md5 hash of a string
	// version: 1103.1210
	// discuss at: http://phpjs.org/functions/md5
	var xl;
	var rotateLeft = function (lValue, iShiftBits) 
		{
		return (lValue << iShiftBits)
		| (lValue >>> (32 - iShiftBits));
		};
	var addUnsigned = function (lX, lY) 
		{
		var lX4, lY4, lX8, lY8, lResult;
		lX8 = (lX & 0x80000000);
		lY8 = (lY & 0x80000000);
		lX4 = (lX & 0x40000000);
		lY4 = (lY & 0x40000000);
		lResult = (lX & 0x3FFFFFFF) + (lY & 0x3FFFFFFF);
		if (lX4 & lY4) 
			{
			return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
			}
		if (lX4 | lY4) 
			{
			if (lResult & 0x40000000) 
				{
				return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
				} 
			else 
				{
				return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
				}
			} 
		else 
			{
			return (lResult ^ lX8 ^ lY8);
			}
		};
	var _F = function (x, y, z) {return (x & y) | ((~x) & z);};
	var _G = function (x, y, z) {return (x & z) | (y & (~z));};
	var _H = function (x, y, z) {return (x ^ y ^ z);};
	var _I = function (x, y, z) {return (y ^ (x | (~z)));};
	var _FF = function (a, b, c, d, x, s, ac) 
		{
		a = addUnsigned(a, addUnsigned(addUnsigned(_F(b, c, d), x), ac));
		return addUnsigned(rotateLeft(a, s), b);
		};
	var _GG = function (a, b, c, d, x, s, ac) 
		{
		a = addUnsigned(a, addUnsigned(addUnsigned(_G(b, c, d), x), ac));
		return addUnsigned(rotateLeft(a, s), b);
		};
	var _HH = function (a, b, c, d, x, s, ac) 
		{
		a = addUnsigned(a, addUnsigned(addUnsigned(_H(b, c, d), x), ac));
		return addUnsigned(rotateLeft(a, s), b);
		};
	var _II = function (a, b, c, d, x, s, ac) 
		{
		a = addUnsigned(a, addUnsigned(addUnsigned(_I(b, c, d), x), ac));
		return addUnsigned(rotateLeft(a, s), b);
		};
	var convertToWordArray = function (str) 
		{
		var lWordCount;
		var lMessageLength = str.length;
		var lNumberOfWords_temp1 = lMessageLength + 8;
		var lNumberOfWords_temp2 = (lNumberOfWords_temp1
		- (lNumberOfWords_temp1 % 64)) / 64;
		var lNumberOfWords = (lNumberOfWords_temp2 + 1) * 16;
		var lWordArray = new Array(lNumberOfWords - 1);
		var lBytePosition = 0;
		var lByteCount = 0;
		while (lByteCount < lMessageLength) 
			{
			lWordCount = (lByteCount - (lByteCount % 4)) / 4;
			lBytePosition = (lByteCount % 4) * 8;
			lWordArray[lWordCount] = (lWordArray[lWordCount]
			| (str.charCodeAt(lByteCount) << lBytePosition));
			lByteCount++;
			}
		lWordCount = (lByteCount - (lByteCount % 4)) / 4;
		lBytePosition = (lByteCount % 4) * 8;
		lWordArray[lWordCount] = lWordArray[lWordCount]
		| (0x80 << lBytePosition);
		lWordArray[lNumberOfWords - 2] = lMessageLength << 3;
		lWordArray[lNumberOfWords - 1] = lMessageLength >>> 29;
		return lWordArray;
		};
	var wordToHex = function (lValue) 
		{
		var wordToHexValue = "",
		wordToHexValue_temp = "",
		lByte, lCount;
		for (lCount = 0; lCount <= 3; lCount++) 
			{
			lByte = (lValue >>> (lCount * 8)) & 255;
			wordToHexValue_temp = "0" + lByte.toString(16);
			wordToHexValue = wordToHexValue
			+ wordToHexValue_temp.substr(wordToHexValue_temp.length - 2, 2);
			}
		return wordToHexValue;
		};
	var x = [], k,AA,BB,CC,DD,a,b,c,d,S11 = 7,
	S12 = 12,S13 = 17,S14 = 22,S21 = 5,S22 = 9,
	S23 = 14,S24 = 20,S31 = 4,S32 = 11,S33 = 16,
	S34 = 23,S41 = 6,S42 = 10,S43 = 15,S44 = 21;
	str = this.utf8_encode(str);
	x = convertToWordArray(str);
	a = 0x67452301;b = 0xEFCDAB89;
	c = 0x98BADCFE;d = 0x10325476;
	xl = x.length;
	for (k = 0; k < xl; k += 16) 
		{
		AA = a;BB = b;CC = c;DD = d;
		a = _FF(a, b, c, d, x[k + 0], S11, 0xD76AA478);
		d = _FF(d, a, b, c, x[k + 1], S12, 0xE8C7B756);
		c = _FF(c, d, a, b, x[k + 2], S13, 0x242070DB);
		b = _FF(b, c, d, a, x[k + 3], S14, 0xC1BDCEEE);
		a = _FF(a, b, c, d, x[k + 4], S11, 0xF57C0FAF);
		d = _FF(d, a, b, c, x[k + 5], S12, 0x4787C62A);
		c = _FF(c, d, a, b, x[k + 6], S13, 0xA8304613);
		b = _FF(b, c, d, a, x[k + 7], S14, 0xFD469501);
		a = _FF(a, b, c, d, x[k + 8], S11, 0x698098D8);
		d = _FF(d, a, b, c, x[k + 9], S12, 0x8B44F7AF);
		c = _FF(c, d, a, b, x[k + 10], S13, 0xFFFF5BB1);
		b = _FF(b, c, d, a, x[k + 11], S14, 0x895CD7BE);
		a = _FF(a, b, c, d, x[k + 12], S11, 0x6B901122);
		d = _FF(d, a, b, c, x[k + 13], S12, 0xFD987193);
		c = _FF(c, d, a, b, x[k + 14], S13, 0xA679438E);
		b = _FF(b, c, d, a, x[k + 15], S14, 0x49B40821);
		a = _GG(a, b, c, d, x[k + 1], S21, 0xF61E2562);
		d = _GG(d, a, b, c, x[k + 6], S22, 0xC040B340);
		c = _GG(c, d, a, b, x[k + 11], S23, 0x265E5A51);
		b = _GG(b, c, d, a, x[k + 0], S24, 0xE9B6C7AA);
		a = _GG(a, b, c, d, x[k + 5], S21, 0xD62F105D);
		d = _GG(d, a, b, c, x[k + 10], S22, 0x2441453);
		c = _GG(c, d, a, b, x[k + 15], S23, 0xD8A1E681);
		b = _GG(b, c, d, a, x[k + 4], S24, 0xE7D3FBC8);
		a = _GG(a, b, c, d, x[k + 9], S21, 0x21E1CDE6);
		d = _GG(d, a, b, c, x[k + 14], S22, 0xC33707D6);
		c = _GG(c, d, a, b, x[k + 3], S23, 0xF4D50D87);
		b = _GG(b, c, d, a, x[k + 8], S24, 0x455A14ED);
		a = _GG(a, b, c, d, x[k + 13], S21, 0xA9E3E905);
		d = _GG(d, a, b, c, x[k + 2], S22, 0xFCEFA3F8);
		c = _GG(c, d, a, b, x[k + 7], S23, 0x676F02D9);
		b = _GG(b, c, d, a, x[k + 12], S24, 0x8D2A4C8A);
		a = _HH(a, b, c, d, x[k + 5], S31, 0xFFFA3942);
		d = _HH(d, a, b, c, x[k + 8], S32, 0x8771F681);
		c = _HH(c, d, a, b, x[k + 11], S33, 0x6D9D6122);
		b = _HH(b, c, d, a, x[k + 14], S34, 0xFDE5380C);
		a = _HH(a, b, c, d, x[k + 1], S31, 0xA4BEEA44);
		d = _HH(d, a, b, c, x[k + 4], S32, 0x4BDECFA9);
		c = _HH(c, d, a, b, x[k + 7], S33, 0xF6BB4B60);
		b = _HH(b, c, d, a, x[k + 10], S34, 0xBEBFBC70);
		a = _HH(a, b, c, d, x[k + 13], S31, 0x289B7EC6);
		d = _HH(d, a, b, c, x[k + 0], S32, 0xEAA127FA);
		c = _HH(c, d, a, b, x[k + 3], S33, 0xD4EF3085);
		b = _HH(b, c, d, a, x[k + 6], S34, 0x4881D05);
		a = _HH(a, b, c, d, x[k + 9], S31, 0xD9D4D039);
		d = _HH(d, a, b, c, x[k + 12], S32, 0xE6DB99E5);
		c = _HH(c, d, a, b, x[k + 15], S33, 0x1FA27CF8);
		b = _HH(b, c, d, a, x[k + 2], S34, 0xC4AC5665);
		a = _II(a, b, c, d, x[k + 0], S41, 0xF4292244);
		d = _II(d, a, b, c, x[k + 7], S42, 0x432AFF97);
		c = _II(c, d, a, b, x[k + 14], S43, 0xAB9423A7);
		b = _II(b, c, d, a, x[k + 5], S44, 0xFC93A039);
		a = _II(a, b, c, d, x[k + 12], S41, 0x655B59C3);
		d = _II(d, a, b, c, x[k + 3], S42, 0x8F0CCC92);
		c = _II(c, d, a, b, x[k + 10], S43, 0xFFEFF47D);
		b = _II(b, c, d, a, x[k + 1], S44, 0x85845DD1);
		a = _II(a, b, c, d, x[k + 8], S41, 0x6FA87E4F);
		d = _II(d, a, b, c, x[k + 15], S42, 0xFE2CE6E0);
		c = _II(c, d, a, b, x[k + 6], S43, 0xA3014314);
		b = _II(b, c, d, a, x[k + 13], S44, 0x4E0811A1);
		a = _II(a, b, c, d, x[k + 4], S41, 0xF7537E82);
		d = _II(d, a, b, c, x[k + 11], S42, 0xBD3AF235);
		c = _II(c, d, a, b, x[k + 2], S43, 0x2AD7D2BB);
		b = _II(b, c, d, a, x[k + 9], S44, 0xEB86D391);
		a = addUnsigned(a, AA);b = addUnsigned(b, BB);
		c = addUnsigned(c, CC);d = addUnsigned(d, DD);
		}
	var temp = wordToHex(a) + wordToHex(b) + wordToHex(c) + wordToHex(d);
	return temp.toLowerCase();
	}

function tm_box($box_num)
	{
	document.getElementById($box_num).className = "tm_box shadow";
	document.getElementById($box_num).innerHTML = "> 14 base pairs:<br />T<sub>m</sub> = 64.9&deg;C + 41&deg;C*(# of G's and C's - 16.4)/#total bps<br /><br /><= 14 base pairs:<br />Tm = 4&deg;C  x  (# of G's and C's) + 2&deg;C  x  (# of A's and T's)";													
	}

function clear_tm_box($box_num)
	{
	document.getElementById($box_num).className = "";
	document.getElementById($box_num).innerHTML = "";		
	}
	
function help_file($index)
	{
	//10
	var $index_array = new Array("Select whether the sequence of the insert is sense (+) or antisense (-).","Select whether the insert in the resulting output plasmid will be denoted by an arrow.","Paste the insert sequence into the text area. FASTA headers and numbers are ignored.","Paste the destination plasmid sequence into the text area, or select one of the popular plasmids from the dropdown menu to auto-fill the sequence. FASTA headers and numbers are ignored.","Specify the positions in the destination plasmid that you want the new sequence to be inserted into. Note that position '0' is the very front of the destination sequence.","Specify Savvy markers. This is a space delimited list: <br /> [Marker name{text, no spaces!}] [beginning position {int}] [ending position{int}] [Arrow for marker? {arrow_on, arrow_off}] [Fill marker? {Filled, Open}] [Color {Color names or CSS HEX color codes (#XXXXXX)}] [Width of marker {int}]<br />Separate markers with a new line.","Specify restriction enzyme positions.<br />[Enzyme name{No spaces}] [Position {int}]:<br />eg. EcoRV 201:","Move the project to the completed or incomplete list in your plasmid management profile.","Pressing this button will identify features in the plasmid sequence and output the plasmid map.","These default settings work well, but feel free to modify them to meet your needs. Keep in mind though that primer size and Tm are linked, and the backend software terminates primer extension when either Target Tm <u>or</u> Max Length are reached (ie. whichever comes first). 83&deg;C is the maximum allowable Tm, and 100bps is the maximum allowable length.",">= 14 base pairs:<br /><p style='text-align:center;'>Nearest-Neighbor</p>< 14 base pairs:<p style='text-align:center;'>4&deg;C  x  (# G's &amp; C's) + 2&deg;C  x  (# A's &amp; T's)</p>");
	var $width = screen.width;
	var $height = screen.height + 60;
	document.getElementById('help_overlay').style.width = $width + "px";	
	document.getElementById('help_overlay').style.height = $height + "px";
	document.getElementById('help_text').style.width = "300px";	
	document.getElementById('help_text').style.height = "150px";
	document.getElementById('help_text').className = "tm_box shadow";
	document.getElementById('help_text').innerHTML = $index_array[$index];
	document.getElementById('close_help').innerHTML = "<img src='images/close_button.png' />";
	}

function clear_help()
	{
	document.getElementById('help_overlay').style.width = "0px";	
	document.getElementById('help_overlay').style.height = "0px";
	document.getElementById('help_text').style.width = "0px";	
	document.getElementById('help_text').style.height = "0px";
	document.getElementById('help_text').innerHTML = "";
	document.getElementById('help_text').className = "";	
	document.getElementById('close_help').innerHTML = "";
	}