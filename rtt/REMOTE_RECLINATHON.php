<?php

class REMOTE_RECLINATHON extends RTT_COMMON
{
    protected $StartTime;   // STRING?
    protected $Movies;      // MOVIE_NETFLIX list

    public function LoadFromForm()
    {
        $this->StartTime = $_POST["startTime"];
        $this->Movies = [];
        foreach($_POST["movies"] as $movie)
        {
            $movieNetflix = new MOVIE_NETFLIX();
            $movieNetflix->LoadFromObject($movie);
            $this->Movies[] = $movieNetflix;
        }
    }

    public function ProcessForm()
    {
        $this->LoadFromForm();

        return TRUE;
    }

    public function FinishProcessForm()
    {
        // Option 1 - send to downtime feed as a proxy for countdown to reclinathon.
        echo "<meta http-equiv='refresh' content='0;mockup/feed_mockup.php?activity=Downtime' />";
        
        // Option 2 - send to first movie link as a proxy for starting a reclinathon.
        //echo "<meta http-equiv='refresh' content='0;" . $this->Movies[0]->GetNetflixURL() . "' />";
    }
}

?>