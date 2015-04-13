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
session_start();
$login_status = "false";
$_POST['submit'] = isset($_POST['submit']) ? $_POST['submit'] : "";
$_POST['database'] = isset($_POST['database']) ? $_POST['database'] : "";
	
if (isset($_SESSION['user_id']))
	{
	$user_info = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE user_id='".$_SESSION['user_id']."' ;"));	
	}
else
	{
	$user_info['session_check'] = mt_rand(1,1000000000);
	$_SESSION['session_check'] = mt_rand(1,1000000000);	
	$_COOKIE['session_check'] = mt_rand(1,1000000000);
	}
	
if ($user_info['session_check'] == $_SESSION['session_check'])
	{
	$session_check = mt_rand(1,1000000000);
	mysql_query("UPDATE users SET session_check = ".$session_check." WHERE user_id = ".$user_info['user_id'].";");
	
	$user_info['session_check'] = $session_check;
	$_COOKIE['session_check'] = $session_check;
	
	setcookie("user_id",$user_info['user_id'],time()+605000);
	setcookie("first_name",$user_info['first_name'],time()+605000);
	setcookie("session_check",$session_check,time()+605000);
	
	$_SESSION['user_id'] = $user_info['user_id'];
	$_SESSION['first_name'] = $user_info['first_name'];
	$_SESSION['session_check'] = $session_check;
		
	$login_status = "true";
	$welcome_message = "Welcome ".$user_info['first_name'].",<br />you are currently logged in.";
	}

if ($login_status != "true" && ($_POST['submit'] == "Get project" || $_POST['database'] == "projects"))
	{
	header ("Location: login.php");
	exit;	
	}



?>