<?php

#include RTTHeader.php";

class QUOTA
{
    public $GenreID = 0;
    public $RunTime = 0;

    function __construct($GenreID, $RunTime)
    {
        $this->GenreID = $GenreID;
        $this->RunTime = $RunTime;
    }
}

class MOVIE_LIST extends RTT_COMMON
{
    protected $MovieList; 	// MOVIE list
    protected $NumMovies; 	// INT
    protected $BackupMovieList;	// MOVIE list
    protected $NumBackupMovies;	// INT
    protected $Tickets;		// INT list
    protected $NumTickets;      // INT
   
    function __construct() 
    {
        $this->NumMovies = 0;
        $this->NumBackupMovies = 0;
        $this->NumTickets = 0;
    }

    public function ProcessVoteForm()
    {
        if ($_POST["ReclineeID"] == '')
        {
            return false;
        }

        $query = "SELECT * FROM VOTE WHERE ReclineeID = '" . $_POST["ReclineeID"] . "'";
        $result = $this->query($query);

        if (!$result)
        {
            return false;
        }

        if (mysql_num_rows($result) > 0)
        {
            echo "<BR>You have already voted for this season.  If you feel this is an error, please contact reclinathon@gmail.com<BR>";
            return FALSE;
        }

        $query = "SELECT MovieID FROM MOVIE";
        $result = $this->query($query);
        if (!$result)
        {
            return FALSE;
        }

        while($row = mysql_fetch_assoc($result))
        {
            if ($_POST["vote" . $row["MovieID"]] != '')
            {
                $query2 = "INSERT INTO VOTE(ReclineeID, MovieID) VALUES('" . $_POST["ReclineeID"] . "', '" . $row["MovieID"] . "')";
                $result2 = $this->query($query2);
                if (!$result2)
                {
                    return false;
                }
            }
        }

        return true;
    }


    private function PullTicket()
    {
        $ticket = mt_rand(0, $this->NumTickets-1);
        return $this->Tickets[$ticket];
    }

    private function RemoveTickets($MovieID)
    {
        $NewTickets;
        $NewNumTickets = 0;

        for ($i = 0; $i < $this->NumTickets; $i++)
        {
            if ($this->Tickets[$i] != $MovieID)
            {
                $NewTickets[$NewNumTickets] = $this->Tickets[$i];
                $NewNumTickets++;
            }
        }

        $this->Tickets = $NewTickets;
        $this->NumTickets = $NewNumTickets;
    }

    private function AddToMovieList($movie)
    {
        $this->MovieList[$this->NumMovies] = $movie;
        $this->NumMovies++;
    }

    private function AddToBackupMovieList($movie)
    {
        $this->BackupMovieList[$this->NumBackupMovies] = $movie;
        $this->NumBackupMovies++;
    }

    private function UpdateQuotas($movie, $QuotaList, $NumQuotas)
    {
        $QuotasFull = true;

        for ($i = 0; $i < $NumQuotas; $i++)
        {
            if ($QuotaList[$i]->RunTime > 0)
            {
                if ($movie->ContainsGenre($QuotaList[$i]->GenreID))
                {
                    $QuotaList[$i]->RunTime -= $movie->GetRunTime();
                }

                if ($QuotaList[$i]->RunTime > 0)
                {
                    $QuotasFull = false;
                }
            }
        }

        return $QuotasFull;
    }

    public function DisplayMovieList()
    {
        echo "<BR>Movie List<BR>";
        for ($i = 0; $i < $this->NumMovies; $i++)
        {
            $this->MovieList[$i]->DisplayModule();
        }
        echo "<BR>";
    }

    public function GetTotalRunTime()
    {
        $TotalRunTime = 0;

        for ($i = 0; $i < $this->NumMovies; $i++)
        {
            $TotalRunTime += $this->MovieList[$i]->GetRunTime();
        }

        return $TotalRunTime;
    }

    public function DisplayGenreRunTimes()
    {
        $GenreRunTimes;
        $Genres;
        $NumGenres = 0;

        for ($i = 0; $i < $this->NumMovies; $i++)
        {
            $MovieGenres = $this->MovieList[$i]->GetGenres();
            $MovieNumGenres = $this->MovieList[$i]->GetNumGenres();
            $MovieRunTime = $this->MovieList[$i]->GetRunTime();

            for ($j=0; $j < $MovieNumGenres; $j++)
            {
                if ($GenreRunTimes[$MovieGenres[$j]->GenreID] == '')
                {
                    $Genres[$NumGenres] = $MovieGenres[$j];
                    $NumGenres++;
                }
                $GenreRunTimes[$MovieGenres[$j]->GenreID] += $MovieRunTime;
            }
        }

        for ($i=0; $i < $NumGenres; $i++)
        {
            echo "<BR>" . $Genres[$i]->Name . ":  " . $GenreRunTimes[$Genres[$i]->GenreID];
        }
    }
        


    public function DisplayBackupMovieList()
    {
        echo "<BR>Backup Movie List<BR>";
        for ($i = 0; $i < $this->NumBackupMovies; $i++)
        {
            echo $this->BackupMovieList[$i] . "<BR>";
        }
        echo "<BR>";
    }

    public function GenerateList($VotesPerAutoApprove, $TicketsPerVote, $MinimumMovies, $QuotaList, $NumQuotas)
    {
        $TotalMovies = 0;
        $QuotasFull = false;
        if ($NumQuotas == 0)
        {
            $QuotasFull = true;
        }

        //Fetch the full pool of movies
        $query = "SELECT MovieID, Freshness FROM MOVIE";
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        //Assign tickets based on freshness
        while($row = mysql_fetch_assoc($result))
        {
            $TotalMovies++;
            for ($i = 0; $i < $row["Freshness"]; $i++)
            {
                $this->Tickets[$this->NumTickets] = $row["MovieID"];
                $this->NumTickets++;
            }
        }
        $FreshnessTickets = $this->NumTickets;

        //Fetch the votes
        $query = "SELECT m.MovieID, COUNT(v.VoteID) AS TotalVotes FROM VOTE v JOIN MOVIE m ON v.MovieID = m.MovieID GROUP BY v.MovieID ORDER BY TotalVotes DESC";
        $result = $this->query($query);
        if (!$result)
        {
            return FALSE;
        }

        //Add extra tickets for each vote, and auto-approve movies with sufficient votes
        while($row = mysql_fetch_assoc($result))
        {
            if ($row["TotalVotes"] >= $VotesPerAutoApprove)
            {
                //auto-approve
                $movie = new MOVIE();
                $movie->Load($row["MovieID"]);
                $this->AddToMovieList($movie);
                $QuotasFull = $this->UpdateQuotas($movie, $QuotaList, $NumQuotas);
                $this->RemoveTickets($row["MovieID"]);
                $TotalMovies--;
            }
            else
            {
                for ($i = 0; $i < ($row["TotalVotes"] * $TicketsPerVote); $i++)
                {
                    $this->Tickets[$this->NumTickets] = $row["MovieID"];
                    $this->NumTickets++;
                }
            }
        }

        echo $this->NumTickets . " tickets.<BR>";
        echo $this->NumTickets - $FreshnessTickets . " (" . (($this->NumTickets - $FreshnessTickets)/$this->NumTickets)*100 . "%) come from votes.<BR>";
        //Add all movies to either the movie list or backup movie list
        for($i = 0; $i < $TotalMovies; $i++)
        {
            //Pull a ticket and load the corresponding movie
            $MovieID = $this->PullTicket();
            $movie = new MOVIE();
            $movie->Load($MovieID);
            $MovieSelected = false;

            //Determine if the pulled movie satisfies a genre quota
            if ($QuotasFull)
            {
                $MovieSelected = false;
            }
            else
            {
                for($j = 0; $j < $NumQuotas; $j++)
                {
                    if ($QuotaList[$j]->RunTime > 0                    &&
                        $movie->ContainsGenre($QuotaList[$j]->GenreID)  )
                    {
                        $MovieSelected = true;
                        break;
                    }
                }
            }

            //If the pulled movie satsifies a quota, add it to the movie list and update quotas
            if ($MovieSelected)
            {
                $this->AddToMovieList($movie);
                $QuotasFull = $this->UpdateQuotas($movie, $QuotaList, $NumQuotas);
            }

            //If the pulled movie doesn't satisfy a quota, add it to the backup movie list
            else
            {
                $this->AddToBackupMovieList($movie);
            }

            //Remove all tickets for the pulled movie, so it is not pulled again.
            $this->RemoveTickets($MovieID);
        }

        //If we have not yet satisfied the MinimumMovies requirement, add movies from the backup list
        //until the MinimumMovies requirement is met
        $StartingIndex = $this->NumMovies;
        for ($i = $StartingIndex; $i < $MinimumMovies; $i++)
        {
            $this->AddToMovieList($this->BackupMovieList[$i - $StartingIndex]);
        }

        echo "Quota fulfilled (or gave up) with:  " . $this->MovieList[$StartingIndex - 1] . " (" . $StartingIndex . " movies)<BR>";
        $this->DisplayMovieList();

        echo "<BR><B>Total RunTime:  " . $this->GetTotalRunTime() . "</B><BR>";
        $this->DisplayGenreRunTimes();
        echo "<BR><BR><BR>";

        $this->DisplayBackupMovieList();

    }

}

?>