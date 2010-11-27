<?php
include "connect.php";

$query = "SELECT * FROM ARENA_CONTROL ORDER BY QueueLevel";
$result = mysql_query($query);
$row = mysql_fetch_row($result);

if ($row)
{
    echo $row[1] . " " . $row[2] . " " . $row[3];
    $query = "DELETE FROM ARENA_CONTROL WHERE QueueLevel = '" . $row[0] ."' LIMIT 1";
    $result = mysql_query($query);
    if (!$result)
    {
        echo "-1 " . $row[0] . " 0";
    }
}
else
{
    echo "0 0 0";
}

?>