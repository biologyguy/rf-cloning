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

require_once('includes/db_connect.php');
include("functions/set_session.php");
$_REQUEST['login'] = isset($_REQUEST['login']) ? $_REQUEST['login'] : "";
$password_fail = "";
$login_fail = "";

if (isset($_POST['to_login']) && ($_POST['to_login'] == "Log out"))
	{
	$session_check = mt_rand(1,1000000000);
	mysql_query("UPDATE users SET session_check = ".$session_check." WHERE user_id = ".$user_info['user_id'].";");	
	
	$session_check = mt_rand(1,1000000000);
	
	unset($_SESSION['user_id']);
	unset($_SESSION['first_name']);
	unset($_SESSION['session_check']);
	session_destroy();
	header ("Location: index.php");
	
	
	exit;
	}

if (isset($_POST['login_submit']))
	{
	$pass = "true";
	if (empty($_POST['login']))
		{
		$login_fail = "You need to provide a login name if you want in...";
		$pass = "false";
		}
	
	if (empty($_POST['password']))
		{
		$password_fail = "You need to provide a password if you want in...";
		$pass = "false";
		}
	
	if ($pass == "true")
		{
		$check_login = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM users WHERE login = '".$_POST['login']."';"));
		if ($check_login[0] == 0)
			{
			$login_fail = "Sorry, this login name doesn't exist. Please try again, or register.";
			$pass = "false";
			}
		
		if ($pass == "true")
			{
			$user_info = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE login = '".$_POST['login']."';"));
			if ($user_info['password'] != md5($_POST['password']))
				{
				$password_fail = "The password provided does not match the login name. Please try again.";
				$pass = "false";
				}
			}		
		}
	
	if ($pass == "true")
		{
		$session_check = mt_rand(1,1000000000);
		mysql_query("UPDATE users SET session_check = ".$session_check." WHERE user_id = ".$user_info['user_id'].";");
		
		$_SESSION['user_id'] = $user_info['user_id'];
		$_SESSION['first_name'] = $user_info['first_name'];
		$_SESSION['session_check'] = $session_check;
		
		header ("Location: index.php");
		
		exit;
		}
	
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="icon" type="image/ico" href="favicon.ico" />
<link rel="stylesheet" href="includes/styles.css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>RF Cloning Login</title>
</head>

<body>
<?php
if($login_status == "true")
	{ 
?>
<form method="post" action="login.php">
<input type="submit" value="Log out" name="to_login"/>
</form>
<?php
	}
	
else
	{
?>		
<form method="post" action="login.php">
<h1>Login</h1>
<table>
	<tr>
    	<td>User name: </td>
        <td><input type="text" name="login" value="<?php echo $_REQUEST['login']; ?>" /></td>
        <td><?php echo $login_fail; ?></td>
    </tr>
    <tr>
		<td>Password: </td>
        <td><input type="password" name="password" /></td>
        <td><?php echo $password_fail; ?></td>
   	</tr>
	<tr>
        <td><input type="submit" value="Submit" name="login_submit"/></td>
    </tr>
</table>

</form>
<br />
<br />
If you do not have an account, please <a href="register.php">create one</a>.
<?php
	}
include("includes/footer.php"); 
?>
</body>
</html>