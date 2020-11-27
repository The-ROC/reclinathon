<?php
include "connect.php";

$query = $db->prepare("SELECT * FROM ARENA_CONTROL ORDER BY QueueLevel");
$result = db_query($db, $query);
$row = $result->fetch_row();

if ($row)
{
    echo $row[1] . " " . $row[2] . " " . $row[3];
    $query = $db->prepare(
        "DELETE FROM ARENA_CONTROL WHERE QueueLevel = ? LIMIT 1"
    );
    $query->bind_param('i', $row[0]);
    $result = db_query($db, $query);
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