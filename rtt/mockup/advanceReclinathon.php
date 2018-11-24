<?php

include '../RECLINATHON_CONTEXT.php';

$contextId = $_GET["contextId"];

if ($contextId == "" || $contextId == "0")
{
	echo "Invalid ContextId";
	exit();
}

$rcx = new RECLINATHON_CONTEXT();

if (!$rcx->Load($contextId))
{
	echo "Context $contextId not found. <br>";
	exit();
}

$rcx->Advance();
	
?>