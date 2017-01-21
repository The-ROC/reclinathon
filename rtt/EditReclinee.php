<?php

include "RECLINATHON_CONTEXT.php";
include "../header.php";

$reclinee = new RECLINEE();
$reclinee->Load($_POST["ReclineeID"]);
$reclinee->DisplayForm();


?>