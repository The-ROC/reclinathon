<?php 

include "connect.php";

if ($_GET["action"] == "Empty")
{
  $query = $db->prepare("TRUNCATE TABLE ARENA_CONTROL");
  $result = db_query($db, $query);
} 

if ($_GET["action"] == "Send Command" && is_numeric($_GET["Command"]) && is_numeric($_GET["Param1"]) && is_numeric($_GET["Param2"]))
{
  $QueueLevel = 0;
  $query = $db->prepare("SELECT MAX(QueueLevel) FROM ARENA_CONTROL");
  $result = db_query($db, $query);
  $row = $result->fetch_row();
  if ($row)
  {
    $QueueLevel = $row[0] + 1;
  }

  $query = $db->prepare(
    "INSERT INTO ARENA_CONTROL VALUES(?, ?, ?, ?)"
  );
  $query->bind_param('iiii', $QueueLevel, $_GET['Command'], $_GET['Param1'], $_GET['Param2']);
  $result = db_query($db, $query);
}

if ($_GET["action"] == "TubeTechOn")
{
  $QueueLevel = 0;
  $query = $db->prepare("SELECT MAX(QueueLevel) FROM ARENA_CONTROL");
  $result = db_query($db, $query);
  $row = $result->fetch_row();
  if ($row)
  {
    $QueueLevel = $row[0] + 1;
  }

  $query = $db->prepare(
    "INSERT INTO ARENA_CONTROL VALUES(?, '1', '6', '100')"
  );
  $query->bind_param('i', $QueueLevel);
  $result = db_query($db, $query);
}

if ($_GET["action"] == "TubeTechOff")
{
  $QueueLevel = 0;
  $query = $db->prepare("SELECT MAX(QueueLevel) FROM ARENA_CONTROL");
  $result = db_query($db, $query);
  $row = $result->fetch_row();
  if ($row)
  {
    $QueueLevel = $row[0] + 1;
  }

  $query = $db->prepare(
    "INSERT INTO ARENA_CONTROL VALUES(?, '1', '6', '0')"
  );
  $query->bind_param('i', $QueueLevel);
  $result = db_query($db, $query);
}

if ($_GET["action"] == "LightsOn")
{
  $QueueLevel = 0;
  $query = $db->prepare("SELECT MAX(QueueLevel) FROM ARENA_CONTROL");
  $result = db_query($db, $query);
  $row = $result->fetch_row();
  if ($row)
  {
    $QueueLevel = $row[0] + 1;
  }

  $query = $db->prepare(
    "INSERT INTO ARENA_CONTROL VALUES(?, '1', '1', '100')"
  );
  $query->bind_param('i', $QueueLevel);
  $result = db_query($db, $query);
  $query = $db->prepare(
    "INSERT INTO ARENA_CONTROL VALUES(?, '1', '2', '100')"
  );
  $query->bind_param('i', $QueueLevel+1);
  $result = db_query($db, $query);
  $query = $db->prepare(
    "INSERT INTO ARENA_CONTROL VALUES(?, '1', '3', '100')"
  );
  $query->bind_param('i', $QueueLevel+2);
  $result = db_query($db, $query);
}

if ($_GET["action"] == "LightsOff")
{
  $QueueLevel = 0;
  $query = $db->prepare("SELECT MAX(QueueLevel) FROM ARENA_CONTROL");
  $result = db_query($db, $query);
  $row = $result->fetch_row();
  if ($row)
  {
    $QueueLevel = $row[0] + 1;
  }

  $query = $db->prepare(
    "INSERT INTO ARENA_CONTROL VALUES(?, '1', '1', '0')"
  );
  $query->bind_param('i', $QueueLevel);
  $result = db_query($db, $query);
  $query = $db->prepare(
    "INSERT INTO ARENA_CONTROL VALUES(?, '1', '2', '0')"
  );
  $query->bind_param('i', $QueueLevel+1);
  $result = db_query($db, $query);
  $query = $db->prepare(
    "INSERT INTO ARENA_CONTROL VALUES(?, '1', '3', '0')"
  );
  $query->bind_param('i', $QueueLevel+2);
  $result = db_query($db, $query);
}

if ($_GET["action"] == "LightsDim")
{
  $QueueLevel = 0;
  $query = $db->prepare("SELECT MAX(QueueLevel) FROM ARENA_CONTROL");
  $result = db_query($db, $query);
  $row = $result->fetch_row();
  if ($row)
  {
    $QueueLevel = $row[0] + 1;
  }

  $query = $db->prepare(
    "INSERT INTO ARENA_CONTROL VALUES(?, '1', '1', '20')"
  );
  $query->bind_param('i', $QueueLevel);
  $result = db_query($db, $query);
  $query = $db->prepare(
    "INSERT INTO ARENA_CONTROL VALUES(?, '1', '2', '20')"
  );
  $query->bind_param('i', $QueueLevel+1);
  $result = db_query($db, $query);
  $query = $db->prepare(
    "INSERT INTO ARENA_CONTROL VALUES(?, '1', '3', '20')"
  );
  $query->bind_param('i', $QueueLevel+2);
  $result = db_query($db, $query);
}

if ($_GET["action"] == "KitchenLightOn")
{
  $QueueLevel = 0;
  $query = $db->prepare("SELECT MAX(QueueLevel) FROM ARENA_CONTROL");
  $result = db_query($db, $query);
  $row = $result->fetch_row();
  if ($row)
  {
    $QueueLevel = $row[0] + 1;
  }

  $query = $db->prepare(
    "INSERT INTO ARENA_CONTROL VALUES(?, '1', '4', '100')"
  );
  $query->bind_param('i', $QueueLevel);
  $result = db_query($db, $query);
}

if ($_GET["action"] == "KitchenLightOff")
{
  $QueueLevel = 0;
  $query = $db->prepare("SELECT MAX(QueueLevel) FROM ARENA_CONTROL");
  $result = db_query($db, $query);
  $row = $result->fetch_row();
  if ($row)
  {
    $QueueLevel = $row[0] + 1;
  }

  $query = $db->prepare(
    "INSERT INTO ARENA_CONTROL VALUES(?, '1', '4', '0')"
  );
  $query->bind_param('i', $QueueLevel);
  $result = db_query($db, $query);
}

?>

<FORM ACTION='ArenaControl.php' METHOD='GET'>

<INPUT STYLE="height: 50px; width: 150px;" TYPE='submit' NAME='action' VALUE='TubeTechOn'></INPUT>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<INPUT STYLE="height: 50px; width: 150px;" TYPE='submit' NAME='action' VALUE='TubeTechOff'></INPUT>
<BR><BR>

<INPUT STYLE="height: 50px; width: 150px;" TYPE='submit' NAME='action' VALUE='LightsOn'></INPUT>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<INPUT STYLE="height: 50px; width: 150px;" TYPE='submit' NAME='action' VALUE='LightsOff'></INPUT>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<INPUT STYLE="height: 50px; width: 150px;" TYPE='submit' NAME='action' VALUE='LightsDim'></INPUT>
<BR><BR>

<INPUT STYLE="height: 50px; width: 150px;" TYPE='submit' NAME='action' VALUE='KitchenLightOn'></INPUT>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<INPUT STYLE="height: 50px; width: 150px;" TYPE='submit' NAME='action' VALUE='KitchenLightOff'></INPUT>
<BR><BR>
 
<SELECT NAME='Command'>
<OPTION VALUE='1'>ControlDevice</OPTION>
</SELECT>

<INPUT TYPE='text' NAME='Param1'></INPUT>
<INPUT TYPE='text' NAME='Param2'></INPUT>

<BR><BR>

<INPUT TYPE='submit' NAME='action' VALUE='Send Command'></INPUT>

<?php

$query = $db->prepare("SELECT * FROM ARENA_CONTROL ORDER BY QueueLevel");
$result = db_query($db, $query);

if (!$result || $result->num_rows == 0)
{
    echo "No Queued Commands.<BR>";
}
else
{
    echo "Queued Commands: <INPUT TYPE='submit' NAME='action' VALUE='Empty'></INPUT> <INPUT TYPE='submit' NAME='action' VALUE='Refresh'></INPUT><BR>";
}

while ($row = $result->fetch_row())
{
    echo $row[1] . " " . $row[2] . " " . $row[3] . "<BR>";
}

?>


</FORM>

