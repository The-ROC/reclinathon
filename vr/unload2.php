<?php

$end = time();
$duration = $end - $begin;

$hours = floor($duration / 3600);
$minutes = floor($duration / 60);
$seconds = $duration % 60;


echo "You virtually reclined for ".$hours." hours, ".$minutes." minutes, and ".$seconds." seconds!";