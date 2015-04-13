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
//requires Raphael and the ultimate ajax object to be loaded as well
var $svg2raphael = new ajaxObject("functions/ajax/svg2raphael.php",svg2raphael);
 
function svg2raphael(responseText, responseStatus)
	{
	if (responseStatus == 200)
		{
		eval(responseText);
		//document.getElementById('output_div').innerHTML = "<textarea cols='200' rows='50'>" + responseText + "</textarea>";
		}	
	}