<?php

include "RECLINATHON_CONTEXT.php";
$class = $_GET['class'];

if ($class == "")
{
    echo "What type of object would you like to insert?<BR>";
    echo "<FORM METHOD='get' ACTION='insert.php'><SELECT NAME='class'>";
    echo "<OPTION VALUE='MOVIE_IMDB'>MOVIE</OPTION>";
    echo "<OPTION VALUE='RECLINATHON_CONTEXT'>RECLINATHON_CONTEXT</OPTION>";
    echo "</SELECT><INPUT TYPE='submit' VALUE='Go!'></FORM>";
    exit();
}

$object = new $class;
if ($_GET["ObjectID"] != "")
{
    $object->Load($_GET["ObjectID"]);
}
$object->DisplayForm();


?>