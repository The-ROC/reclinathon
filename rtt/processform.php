<?php

include "RECLINATHON_CONTEXT.php";
if(isset($_POST['class'])) {
	$class = $_POST['class'];
} else if(isset($_GET['class'])) {
	$class = $_GET['class'];
}


$object = new $class;
if (!$object->ProcessForm())
{
    echo "Failed. <BR>";
}


else
{
    if (method_exists($object, 'FinishProcessForm'))
    {
        $object->FinishProcessForm();
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

}


?>