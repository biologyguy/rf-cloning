<?php
 die("Sorry, I've removed the site download. If you want a copy, email me.");
 require_once('../includes/rf-cloning/db_connect.php');
 mysqli_query($conn, "UPDATE `rf-cloning`.`usage` SET `count` = count+1 WHERE `usage`.`id` =2;") or die(mysqli_error());
 
 // Define the path to file
 $file = "rf-cloning.zip";
 
 if(!file)
 {
     // File doesn't exist, output error
     die('file not found');
 }
 else
 {
	 // Set headers
     header("Cache-Control: public");
     header("Content-Description: File Transfer");
     header("Content-Disposition: attachment; filename=$file");
     header("Content-Type: application/zip");
     header("Content-Transfer-Encoding: binary");
     // Read the file from disk
     readfile($file);
 
 }
 ?>
