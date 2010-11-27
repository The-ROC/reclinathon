<?php

include "RECLINATHON_CONTEXT.php";
$class = $_POST['class'];

$object = new $class;
if (!$object->ProcessForm())
{
    echo "Failed. <BR>";
}

else
{
    if ($_POST["ObjectID"] == 0)
    {
        echo "<meta http-equiv='refresh' content=\"0;url=insert.php\" />";
    }
    else
    {
        echo "<meta http-equiv='refresh' content=\"0;url=display.php\" />";
    }
}

?>