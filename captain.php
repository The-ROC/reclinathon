<?php 

session_name("reclinathon");
session_id("reclinathon");
session_Start();

include $_SERVER['DOCUMENT_ROOT']."\include\connect.php";

if(!session_is_registered("GO")) {
  $GO = 0;
  session_register("GO");
}

if(!session_is_registered("TIMER")) {
  $TIMER = "300";
  session_register("TIMER");
}

if(!session_is_registered("RANDMAX")) {
  $query = $db->prepare("SELECT * FROM Trivia");
  $result = db_query($db, $query);
  $RANDMAX = $result->num_rows;
  $row = $result->fetch_row();
  session_register("RANDMAX");
}

if(!session_is_registered("RAND")) {
  $RAND = rand(1, $RANDMAX);
  echo $RAND;
  session_register("RAND");
}

$GO = 1;
