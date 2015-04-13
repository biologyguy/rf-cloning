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

if(isset($_COOKIE['user_id']))
	{
	include("functions/set_session.php");	
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="icon" type="image/ico" href="favicon.ico" />
<link rel="stylesheet" href="includes/styles.css" />

<meta name="description" content="SOAP service for accessing the restriction free cloning algorithm." />
<title>Restriction Free Cloning SOAP Service</title>
<script src="classes/ajaxObj.js" language="javascript" type="text/javascript"></script>
<script src="javascript/javascripts.js" language="javascript" type="text/javascript"></script>

<script language="javascript" type="text/javascript">

var $email = new ajaxObject("functions/ajax/email.php",get_email)

function get_email(responseText, responseStatus)
	{
	if (responseStatus == 200)
		{			
		document.getElementById('email1').innerHTML = responseText + "hear from you</a>";
		}
	}
</script>
</head>

<body style="width:710px;" onload="$email.update('check=ok','POST');">
    <div class="tabs">
        <ul>
            <li><a href='index.php'><span>Home</span></a></li>
			<?php 	$login_status = isset($login_status) ? $login_status : "false";
					if($login_status == "true") echo "<li><a href='plasmid_management.php'><span>Manage plasmids</span></a></li>"; ?>
        	<li><a href='savvy.php'><span>Savvy</span></a></li>
            <li><a href='QandA.php' target="_blank"><span>Q & A</span></a></li>
            <li><a href="login.php"><span><?php if($login_status == "true") echo "Log out"; else echo "Log in/Register";  ?></span></a></li>
        </ul>
    </div>

<h1>SOAP web service</h1>
<p>If you would like to write your own programs that use the primer design algorithm on this site, the rf-cloning server can handle XML requests using the Simple Object Access Protocol (SOAP).</p> 
<p>The wsdl file is at <a href="classes/rf_cloning.wsdl">http://www.rf-cloning.org/classes/rf_cloning.wsdl</a></p> 
<p>I have also written a client class in php: <a href="classes/rf_cloning_client.txt">http://www.rf-cloning.org/classes/rf_cloning_client.txt</a>.<br />
As well as an example Perl client: <a href="classes/perl_soap_client.txt">http://www.rf-cloning.org/classes/perl_soap_client.txt</a>.</p>
<p>If you write a client class in another language and are willing to share, I'd love to <span id="email1">hear from you</span>.</p>
<div style="position:absolute; left:500px; top:800px;"><?php include("includes/footer.php"); ?></div>
</body>
</html>