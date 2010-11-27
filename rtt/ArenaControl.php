<?php 

include "connect.php";

if ($_GET["action"] == "Empty")
{
  $query = "TRUNCATE TABLE ARENA_CONTROL";
  $result = mysql_query($query);
} 

if ($_GET["action"] == "Send Command" && is_numeric($_GET["Command"]) && is_numeric($_GET["Param1"]) && is_numeric($_GET["Param2"]))
{
  $QueueLevel = 0;
  $query = "SELECT MAX(QueueLevel) FROM ARENA_CONTROL";
  $result = mysql_query($query);
  $row = mysql_fetch_row($result);
  if ($row)
  {
    $QueueLevel = $row[0] + 1;
  }

  $query = "INSERT INTO ARENA_CONTROL VALUES('" . $QueueLevel . "', '" . $_GET["Command"] . "', '" . $_GET["Param1"] . "', '" . $_GET["Param2"] . "')";
  $result = mysql_query($query);
}

if ($_GET["action"] == "TubeTechOn")
{
  $QueueLevel = 0;
  $query = "SELECT MAX(QueueLevel) FROM ARENA_CONTROL";
  $result = mysql_query($query);
  $row = mysql_fetch_row($result);
  if ($row)
  {
    $QueueLevel = $row[0] + 1;
  }

  $query = "INSERT INTO ARENA_CONTROL VALUES('" . $QueueLevel . "', '1', '6', '100')";
  $result = mysql_query($query);
}

if ($_GET["action"] == "TubeTechOff")
{
  $QueueLevel = 0;
  $query = "SELECT MAX(QueueLevel) FROM ARENA_CONTROL";
  $result = mysql_query($query);
  $row = mysql_fetch_row($result);
  if ($row)
  {
    $QueueLevel = $row[0] + 1;
  }

  $query = "INSERT INTO ARENA_CONTROL VALUES('" . $QueueLevel . "', '1', '6', '0')";
  $result = mysql_query($query);
}

?>

<FORM ACTION='ArenaControl.php' METHOD='GET'>
 
<SELECT NAME='Command'>
<OPTION VALUE='1'>ControlDevice</OPTION>
</SELECT>

<INPUT TYPE='text' NAME='Param1'></INPUT>
<INPUT TYPE='text' NAME='Param2'></INPUT>

<BR><BR>

<INPUT TYPE='submit' NAME='action' VALUE='Send Command'></INPUT>

<BR><BR>
<INPUT TYPE='submit' NAME='action' VALUE='TubeTechOn'></INPUT>
<BR>
<INPUT TYPE='submit' NAME='action' VALUE='TubeTechOff'></INPUT>
<BR><BR>

<?php

$query = "SELECT * FROM ARENA_CONTROL ORDER BY QueueLevel";
$result = mysql_query($query);

if (!$result || mysql_num_rows($result) == 0)
{
    echo "No Queued Commands.<BR>";
}
else
{
    echo "Queued Commands: <INPUT TYPE='submit' NAME='action' VALUE='Empty'></INPUT> <INPUT TYPE='submit' NAME='action' VALUE='Refresh'></INPUT><BR>";
}

while ($row = mysql_fetch_row($result))
{
    echo $row[1] . " " . $row[2] . " " . $row[3] . "<BR>";
}

?>


</FORM>

