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
require_once('../../../includes/rf-cloning/db_connect.php');

if ($_POST['project_id'] == "new")
	{
	echo 0;	
	}

else
	{
	mysql_query("UPDATE projects SET complete=".$_POST['complete_check']." WHERE plasmid_id=".$_POST['project_id'].";");
	echo $_POST['complete_check'];
	}
?>