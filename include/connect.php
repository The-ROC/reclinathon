<?php 

$TEST_SERVER = 
    $_SERVER['SERVER_NAME'] == 'localhost' || 
    file_exists($_SERVER['DOCUMENT_ROOT']."\include\localdb") || 
    file_exists($_SERVER['DOCUMENT_ROOT']."include/localdb");

if (!$TEST_SERVER)
{
    $db = mysqli_connect("db1530.perfora.net", "dbo248802449", "Dr.Bundy", "db248802449");
}
else
{
    $db = mysqli_connect("localhost", "ROC_USER", "Dr.Bundy", "db248802449");
}

if(!$db)
{
    echo "Error! Could not connect! The server may be down, please check back later."; 
    exit;
}

function db_query ($db, $query)
{
    $result = $query->execute();
    if (!$result)
    {
        $error = 'Error executing query: ' . $db->error;
    }
    else
    {
        $selectResult = $query->get_result();
        if ($selectResult) {
            $result = $selectResult;
        } 
    }

    return $result;
}

?>
