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

$savvy_info = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$_POST['database']." WHERE plasmid_id = ".$_POST['id'].";"));


$output['plasmid_name'] = ($_POST['database'] == 'projects') ? $savvy_info['plasmid_name']: $savvy_info['plasmid_name'];
$output['plasmid_size'] = ($_POST['database'] == 'projects') ? $savvy_info['new_size'] : $savvy_info['plasmid_size'];
$output['enzymes'] = $savvy_info['savvy_enzymes'];
$output['markers'] = $savvy_info['savvy_markers'];
$output['plasmid_id'] = $_POST['id'];
$output['database'] = $_POST['database'];

echo json_encode($output);
?>