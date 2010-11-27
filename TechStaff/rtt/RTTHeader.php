<?php

function __autoload($ClassName)
{
    require_once $ClassName . '.php';
}

?>  