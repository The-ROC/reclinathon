<?php 

$TEST_SERVER = file_exists($_SERVER['DOCUMENT_ROOT']."\include\localdb");

if (!$TEST_SERVER)
{
    $db = mysql_pconnect("db1530.perfora.net", "dbo248802449", "Dr.Bundy");
}
else
{
    $db = mysql_connect("localhost", "ROC_USER", "Dr.Bundy");
}

if(!$db)
{
    echo "Error! Could not connect! The server may be down, please check back later."; 
    exit;
}

mysql_select_db("db248802449"); 

?>
