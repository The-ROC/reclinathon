<?php
session_start();
include "connect.php";
?>

<bgsound src="alarm.wav" loop=-1">

<CENTER><FONT COLOR="white">

<?php

echo "You have been officially</FONT><BR><h1>!!!BLACKLISTED!!!</H1>"; 
  


  $Name = $FNAME." ".$LNAME;
  $IP = $REMOTE_ADDR;

  $query = "insert into Blacklist (Name, Email, IP) values (\"".$Name."\", \"".$EMAIL."\", \"".$IP."\")"; 
  
  $result = mysql_query($query);

  if ($result != TRUE){ 
      echo "We have a problem.";
  } 

  session_destroy();


?>

<EMBED SRC="alarm.wmv" AUTOSTART=TRUE LOOP=TRUE WIDTH=0 HEIGHT=0>
</EMBED>