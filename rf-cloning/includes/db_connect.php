<?php
$hostname = "localhost";
$username = "root";
$password = "";

$handle = mysql_pconnect($hostname, $username, $password) or trigger_error(mysql_error(),E_USER_ERROR); 
$dbselected = mysql_select_db("rfcloning");

?>