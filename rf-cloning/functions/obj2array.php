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
function obj2array($object) 
	{
	if (is_array($object))
		return $object;
	   
	if (!is_object($object))
		return FALSE;
	   
	$serial = serialize($object);
	$serial = preg_replace('/O:\d+:".+?"/','a',$serial);
	if(preg_match_all('/s:\d+:"\\0.+?\\0(.+?)"/',$serial,$ms,PREG_SET_ORDER )) 
		{
		foreach($ms as $m) 
			{
			$serial = str_replace($m[0],'s:'.strlen($m[1]).':"'.$m[1].'"',$serial) ;
			}
		}
	return @unserialize($serial) ;
	}
            
?>