<?php

include "RECLINATHON_CONTEXT.php";

$ml = new MOVIE_LIST();

if (!$ml->ProcessVoteForm())
{
    echo "<BR>Error processing votes.  Please try again or contact reclinathon@gmail.com<BR>";
}

else
{
    echo "<BR>Your vote has been cast!  Thank you for supporting the Reclinathon democratic proccess.  Print this page and bring it with you to Reclinathon for a special gift for voting.<BR><BR>";
    echo "<IMG SRC='images/vote_stub.jpg' WIDTH='300px'><BR>";
}

echo "<BR><BR><A HREF='http://www.reclinathon.com/rtt'>Continue Tracking the LA-Z-Dude Reclinathon</A><BR>";

?>