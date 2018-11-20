<?php
header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: text/xml");

    if(isset($_GET["feedPost"]) && isset ($_GET["username"]))
    {
        $feedPost = $_GET["feedPost"];
        $icon = "images/" . $_GET["username"] . ".png";
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