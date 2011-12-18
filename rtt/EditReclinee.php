<?php

include "RECLINATHON_CONTEXT.php";

$reclinee = new RECLINEE();
$reclinee->Load($_POST["ReclineeID"]);
$reclinee->DisplayForm();


?>