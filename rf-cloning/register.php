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

$login_failed = "";
$email_failed = "";

if (isset($_POST['registration_submit']))
	{
	$pass = "true";
	if(empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email']) || empty($_POST['login']) ||  empty($_POST['pswd_1']) || empty($_POST['pswd_2']))
		{
		echo "<b>Registration requires all fields to be filled in.</b><br />"; 
		$pass = "false";
		}
	
	if($_POST['pswd_1'] != $_POST['pswd_2'])
		{
		echo "<b>The two passwords you provided did not match up, please try again.</b><br />";
		$pass = "false"; 
		}
	
	if(strtoupper($_POST['human']) != 'YES')
		{
		echo "<b>Sorry, you don't appear to be human. Please try again.</b><br />";
		$pass = "false";	
		}
	
	if($pass == "true")
		{		
		$check_login = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM users WHERE login = '".$_POST['login']."';"));
		$check_email = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM users WHERE email = '".$_POST['email']."';"));
		
		if ($check_login[0] > 0)
			{
			$login_failed = "Sorry, this login name is already in use. Please select another.";
			$pass = "false";
			}
			
		if ($check_email[0] > 0)
			{
			$email_failed = "Sorry, this email address is already in use. Please select another.";
			$pass = "false";
			}
		
		}
	
	if($pass == "true")
		{
		$password_hash = md5($_POST['pswd_1']);
		mysql_query("INSERT INTO users (first_name,last_name,email,login,password) VALUES ('".$_POST['first_name']."','".$_POST['last_name']."','".$_POST['email']."','".$_POST['login']."','".$password_hash."');") or die(mysql_error());
		header ("Location: login.php?login=".$_POST['login']."");
		exit;		
		}
	}
$first_name = (isset($_POST['first_name'])) ? $_POST['first_name'] : "";
$last_name = (isset($_POST['last_name'])) ? $_POST['last_name'] : "";
$email = (isset($_POST['email'])) ? $_POST['email'] : "";
$login = (isset($_POST['login'])) ? $_POST['login'] : "";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="icon" type="image/ico" href="favicon.ico" />
<link rel="stylesheet" href="includes/styles.css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Register</title>
</head>

<body>
<h1>Create an account</h1>
Accounts are free, and are <i>not</i> required to use the primary restriction free cloning project design tool. Account creation is only required if you wish to save projects for retrieval later. Passwords are encrypted, and nothing evil will be done with your personal information (<a href="privacy.html">privacy policy</a>).<br /><br /> 
<form action="register.php" method="post">
<table>
	<tr>
    	<td>First Name</td>
        <td><input type="text" name="first_name" value="<?php echo $first_name; ?>"/></td>
    </tr>
    <tr>
    	<td>Last Name</td>
        <td><input type="text" name="last_name" value="<?php echo $last_name; ?>" /></td>
    </tr>
    <tr>
    	<td>Email address</td>
        <td><input type="text" name="email" value="<?php echo $email; ?>" /></td>
        <td><?php echo $email_failed; ?></td>
    </tr>
    <tr>
    	<td>Login name</td>
        <td><input type="text" name="login" value="<?php echo $login; ?>" /></td>
        <td><?php echo $login_failed; ?></td>
    </tr>
    <tr>
    	<td>Password</td>
        <td><input type="password" name="pswd_1" /></td>
    </tr>
    <tr>
    	<td>Re-type Password</td>
        <td><input type="password" name="pswd_2" /></td>
    </tr>
    <tr>
    	<td>Are you Human?</td>
        <td><input type="text" name="human" /></td>
        <td style="font-size:9px">(just type the word YES...)</td>
    </tr>
    <tr>
    	<td><input type="submit" value="Submit" name="registration_submit" /></td>
    </tr>
</table>       

</form>
<?php include("includes/footer.php"); ?>
</body>
</html>
