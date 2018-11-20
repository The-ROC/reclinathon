<?php
header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: text/xml");

    if(isset($_GET["feedPost"]))
    {
        $feedPost = $_GET["feedPost"];
        $icon = "images/reclinathon.jpg";
        $postTime = time();

        echo "<FeedEvents>";
            echo "<FeedEvent>";
                echo "<Icon>" . $icon . "</Icon>";
                echo "<Message>" . $feedPost . "</Message>";
                echo "<Timestamp>" . $postTime . "</Timestamp>";
            echo "</FeedEvent>";
        echo "</FeedEvents>";
    }
?>