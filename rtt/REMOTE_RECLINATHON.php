<?php

class REMOTE_RECLINATHON extends RTT_COMMON
{
    protected $StartTime;   // STRING?
    protected $Movies;      // MOVIE_NETFLIX list

    public function LoadFromForm()
    {
        $this->StartTime = $_POST["startTime"];
        $this->Movies = [];
        foreach((array)$_POST["movies"] as $movie)
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
        echo "<meta http-equiv='refresh' content='0;mockup/feed_mockup.php?activity=Scheduled' />";
    }
}

?>