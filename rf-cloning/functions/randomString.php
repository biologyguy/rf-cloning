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
function randomString($length) 
	{
    $characters = "23456789abcdefghjkmnpqrstwxyzABCDEFGHJKLMNPQRSTWXYZ";
    $string = "";    

    for ($p = 0; $p < $length; $p++) 
		{
        $string .= $characters[mt_rand(0, strlen($characters))];
    	}

    return $string;
	}
?>