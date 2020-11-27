<?php
session_start();
include $_SERVER['DOCUMENT_ROOT']."\include\connect.php";
?>

<bgsound src="alarm.wav" loop=-1">

<CENTER><FONT COLOR="white">

<?php

echo "You have been officially</FONT><BR><h1>!!!BLACKLISTED!!!</H1>"; 
  


  $Name = $FNAME." ".$LNAME;
  $IP = $REMOTE_ADDR;

  $query = $db->prepare(
    "insert into Blacklist (Name, Email, IP) values (?, ?, ?)"
  );
  $query->bind_param('sss', $Name, $EMAIL, $IP);
  
  $result = db_query($db, $query);

  if ($result != TRUE){ 
      echo "We have a problem.";
  } 

  session_destroy();


?>

<EMBED SRC="alarm.wmv" AUTOSTART=TRUE LOOP=TRUE WIDTH=0 HEIGHT=0>
</EMBED>