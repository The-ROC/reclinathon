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
	protected $AllMovies;       // MOVIE list
    protected $PlayedMovies;	// MOVIE list
	protected $NumMovies;
    protected $NumPlayedMovies;	// INT
    protected $UnplayedMovies;	// MOVIE list
    protected $NumUnplayedMovies;	// INT
    protected $EnteredPrimetime;	// BOOL
    protected $EnteredSleeper;	// BOOL
    protected $EnteredWakeup;	// BOOL
   
    function __construct() 
    {
		$this->NumMovies = 0;
        $this->NumPlayedMovies = 0;
        $this->NumUnplayedMovies = 0;
        $this->EnteredPrimetime = false;
        $this->EnteredSleeper = false;
        $this->EnteredWakeup = false;
    }

    public function Load($Season)
    {
        $query = $this->GetConnection()->prepare(
            "SELECT ml.* FROM MOVIE_LIST ml LEFT JOIN RECLINATHON_CONTEXT rc ON ml.MovieID = rc.MovieID WHERE ml.Name = ? ORDER BY ml.Played, rc.TimeStamp"
        );
        $query->bind_param('s', $Season);
        $result = $this->query($query);
        if (!$result || $result->num_rows == 0)
        {
            return false;
        }

        while ($row = $result->fetch_assoc())
        {
            $movie = new MOVIE();
            if (!$movie->Load($row["MovieID"]))
            {
                echo "Failed to load movie " . $row["MovieID"];
                return false;
            }

            switch ($row["Played"])
            {
                case 0:  $this->UnplayedMovies[$this->NumUnplayedMovies++] = $movie;
                         break;
                case 1:  $this->PlayedMovies[$this->NumPlayedMovies++] = $movie;
                         break;
                case 2:  $this->PlayedMovies[$this->NumPlayedMovies++] = $movie;
                         $this->EnteredPrimetime = true;
                         break;
                case 3:  $this->PlayedMovies[$this->NumPlayedMovies++] = $movie;
                         $this->EnteredSleeper = true;
                         break;
                case 4:  $this->PlayedMovies[$this->NumPlayedMovies++] = $movie;
                         $this->EnteredWakeup = true;
                         break;
                default: return false;
           }
        }
		
		$query = $this->GetConnection()->prepare(
            "SELECT * FROM MOVIE_LIST WHERE Name = ? ORDER BY `Order` ASC"
        );
        $query->bind_param('s', $Season);
        $result = $this->query($query);

        while ($row = $result->fetch_assoc())
        {
            $movie = new MOVIE();
            if (!$movie->Load($row["MovieID"]))
            {
                echo "Failed to load movie " . $row["MovieID"];
                return false;
            }
			
			$this->AllMovies[$this->NumMovies++] = $movie;
		}

        return true;
    }

    public function TogglePlayedUnplayed($Played, $MovieID, $Season)
    {
        $query = $this->GetConnection()->prepare(
            "UPDATE MOVIE_LIST SET Played = '" . $Played . "' WHERE Name = '" . $Season . "' AND MovieID = '" . $MovieID . "'"
        );
        $query->bind_param('isi', $Played, $Season, $MovieID);
        $result = $this->query($query);
        if (!$result)
        {
            return false;
        }
        return true;
    }

    public function Delete($Season)
    {
        $query = $this->GetConnection()->prepare(
            "DELETE FROM MOVIE_LIST WHERE Name = ?"
        );
        $query->bind_param('s', $Season);
        $result = $this->Query($query);
        if(!$result)
        {
            return FALSE;
        }

        return TRUE;
    }

    public function Insert($Season)
    {
        $query = $this->GetConnection()->prepare(
            "DELETE FROM MOVIE_LIST WHERE Name = ?"
        );
        $query->bind_param('s', $Season);
        $result = $this->Query($query);
        if(!$result)
        {
            return FALSE;
        }

        for ($i=0; $i < $this->NumUnplayedMovies; $i++)
        {
            $query = $this->GetConnection()->prepare(
                "INSERT INTO MOVIE_LIST (Name, MovieID, Played) VALUES (?, ?, '0')"
            );
            $query->bind_param('si', $this->UnplayedMovies[$i]->GetID());
            $result = $this->Query($query);
            if (!$result)
            {
                return FALSE;
            }
        }

        for ($i=0; $i < $this->NumPlayedMovies; $i++)
        {
            $query = $this->GetConnection()->prepare(
                "INSERT INTO MOVIE_LIST (Name, MovieID, Played) VALUES (?, ?, '0')"
            );
            $query->bind_param('si', $Season, $this->PlayedMovies[$i]->GetID());
            $result = $this->Query($query);
            if (!$result)
            {
                return FALSE;
            }
        }

        return TRUE;
    }


    public function HasUnplayedMovies()
    {
        return (0 != $this->NumUnplayedMovies);
    }


    public function ProcessVoteForm()
    {
        if ($_POST["ReclineeID"] == '')
        {
            return false;
        }

        $CurrentSeason = $this->GetCurrentSeason();

        $query = $this->GetConnection()->prepare(
            "SELECT * FROM VOTE WHERE Season = ? AND ReclineeID = ?"
        );
        $query->bind_param('si', $CurrentSeason, $_POST['ReclineeID']);
        $result = $this->query($query);

        if (!$result)
        {
            return false;
        }

        if ($result->num_rows > 0)
        {
            echo "<BR>You have already voted for this season.  If you feel this is an error, please contact reclinathon@gmail.com<BR>";
            return FALSE;
        }

        $query = $this->GetConnection()->prepare("SELECT MovieID FROM MOVIE");
        $result = $this->query($query);
        if (!$result)
        {
            return FALSE;
        }

        while($row = $result->fetch_assoc())
        {
            if ($_POST["vote" . $row["MovieID"]] != '')
            {
                $golden = 0;
                if ($_POST["GoldenMovieID"] == "movie" . $row["MovieID"])
                {
                    $golden = 1;
                }
                $query2 = $this->GetConnection()->prepare(
                    "INSERT INTO VOTE(Season, ReclineeID, MovieID, Golden) VALUES(?, ?, ?, ?)"
                );
                $query2->bind_param('siii', $CurrentSeason, $_POST['ReclineeID'], $row['MovieID'], $golden);
                $result2 = $this->query($query2);
                if (!$result2)
                {
                    return false;
                }
            }
        }

        return true;
    }


    private function PullTicket($Tickets, $NumTickets)
    {
        $ticket = mt_rand(0, $NumTickets-1);
        return $Tickets[$ticket];
    }

    private function RemoveTickets(&$Tickets, &$NumTickets, $MovieID)
    {
        $NewTickets;
        $NewNumTickets = 0;

        for ($i = 0; $i < $NumTickets; $i++)
        {
            if ($Tickets[$i] != $MovieID)
            {
                $NewTickets[$NewNumTickets] = $Tickets[$i];
                $NewNumTickets++;
            }
        }

        $Tickets = $NewTickets;
        $NumTickets = $NewNumTickets;
    }

    private function AddToList(&$list, &$NumEntries, $movie)
    {
        $list[$NumEntries++] = $movie;
    }

    private function AddToPlayedList($movie)
    {
        $this->PlayedMovies[$this->NumPlayedMovies++] = $movie;
    }

    private function AddToUnplayedList($movie)
    {
        $this->UnplayedMovies[$this->NumUnplayedMovies++] = $movie;
    }

    private function MoveToPlayedList($movie)
    {
        for ($i = 0; $i < $this->NumUnplayedMovies; $i++)
        {
            if ($movie->GetID() == $this->UnplayedMovies[$i]->GetID())
            {
                    $this->NumUnplayedMovies--;
                    for ($j = $i; $j < $this->NumUnplayedMovies; $j++)
                    {
                        $this->UnplayedMovies[$j] = $this->UnplayedMovies[$j+1];
                    }
                    $this->AddToPlayedList($movie);
                    return true;
            }
        }

        return false;
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

    public function DisplayMovieList($list, $NumMovies)
    {
        echo "<BR>Movie List<BR>";
        echo "<TABLE>";
        for ($i = 0; $i < $NumMovies; $i++)
        {
            echo "<TR>";
            echo "<TD>" . $list[$i] . "</TD><TD>";
            $voteQuery = $this->GetConnection()->prepare(
                "SELECT COUNT(v.VoteID) AS TotalVotes, SUM(v.Golden) AS TotalGoldenVotes FROM VOTE v JOIN MOVIE m ON v.MovieID = m.MovieID WHERE v.MovieID = ? GROUP BY v.MovieID"
            );
            $voteQuery->bind_param('i', $list[$i]->GetID());
            $voteResult = $this->query($voteQuery);
            if ($voteResult)
            {
                $voteRow = $voteResult->fetch_assoc();
                if ($voteRow)
	 	{
		    echo "(" . $voteRow["TotalVotes"] . " votes, " . $voteRow["TotalGoldenVotes"] . " golden)";
		}
                else
                {
                    echo "(No votes)";
                }
	    }
            echo "</TD></TR>"; 
        }
        echo "</TABLE><BR>";
    }

    public function DisplaySelectList($SelectedMovieID)
    {
        echo "<SELECT NAME='MovieID'>";
        for ($i = 0; $i < $this->NumUnplayedMovies; $i++)
        {
            echo "<OPTION VALUE='" . $this->UnplayedMovies[$i]->GetID() . "'";
            if ($SelectedMovieID == $this->UnplayedMovies[$i]->GetID())
           {
                echo " SELECTED";
            }
            echo ">" . $this->UnplayedMovies[$i] . "</OPTION>";
        }
        for ($i = 0; $i < $this->NumPlayedMovies; $i++)
        {
            echo "<OPTION VALUE='" . $this->PlayedMovies[$i]->GetID() . "'";
            if ($SelectedMovieID == $this->PlayedMovies[$i]->GetID())
           {
                echo " SELECTED";
            }
            echo ">" . $this->PlayedMovies[$i] . "</OPTION>";
        }

        echo "</SELECT>";

        return true;
    }

    public function GetTotalRunTime()
    {
        $TotalRunTime = 0;

        for ($i = 0; $i < $this->NumUnplayedMovies; $i++)
        {
            $TotalRunTime += $this->UnplayedMovies[$i]->GetRunTime();
        }

        for ($i = 0; $i < $this->NumPlayedMovies; $i++)
        {
            $TotalRunTime += $this->PlayedMovies[$i]->GetRunTime();
        }

        return $TotalRunTime;
    }

    public function DisplayGenreRunTimes()
    {
        $GenreRunTimes;
        $Genres;
        $NumGenres = 0;

        for ($i = 0; $i < $this->NumUnplayedMovies; $i++)
        {
            $MovieGenres = $this->UnplayedMovies[$i]->GetGenres();
            $MovieNumGenres = $this->UnplayedMovies[$i]->GetNumGenres();
            $MovieRunTime = $this->UnplayedMovies[$i]->GetRunTime();

            for ($j=0; $j < $MovieNumGenres; $j++)
            {
                if ($GenreRunTimes[$MovieGenres[$j]->GenreID] == '')
                {
                    $Genres[$NumGenres++] = $MovieGenres[$j];
                }
                $GenreRunTimes[$MovieGenres[$j]->GenreID] += $MovieRunTime;
            }
        }

        for ($i = 0; $i < $this->NumPlayedMovies; $i++)
        {
            $MovieGenres = $this->PlayedMovies[$i]->GetGenres();
            $MovieNumGenres = $this->PlayedMovies[$i]->GetNumGenres();
            $MovieRunTime = $this->PlayedMovies[$i]->GetRunTime();

            for ($j=0; $j < $MovieNumGenres; $j++)
            {
                if ($GenreRunTimes[$MovieGenres[$j]->GenreID] == '')
                {
                    $Genres[$NumGenres++] = $MovieGenres[$j];
                }
                $GenreRunTimes[$MovieGenres[$j]->GenreID] += $MovieRunTime;
            }
        }

        for ($i=0; $i < $NumGenres; $i++)
        {
            echo "<BR>" . $Genres[$i]->Name . ":  " . $GenreRunTimes[$Genres[$i]->GenreID];
        }
    }

    public function GenerateList($VotesPerAutoApprove, $TicketsPerVote, $MinimumMovies, $QuotaList, $NumQuotas)
    {
        $BackupMovieList;		// MOVIE list
        $NumBackupMovies = 0;		// INT
        $Tickets;			// INT list
        $NumTickets = 0;    		// INT
        $TotalMovies = 0;		// INT
	$AutoApprovedMovies;		// INT list
	$NumAutoApprovedMovies = 0;	// INT
        $QuotasFull = ($NumQuotas == 0);

        $CurrentSeason = $this->GetCurrentSeason();

        //Fetch the votes
        $query = $this->GetConnection()->prepare(
            "SELECT m.MovieID, COUNT(v.VoteID) AS TotalVotes, SUM(v.Golden) AS TotalGoldenVotes FROM VOTE v JOIN MOVIE m ON v.MovieID = m.MovieID WHERE v.Season = ? GROUP BY v.MovieID ORDER BY TotalVotes DESC, TotalGoldenVotes DESC"
        );
        $query->bind_param('s', $CurrentSeason);
        $result = $this->query($query);
        if (!$result)
        {
            return FALSE;
        }

        //Add tickets for each vote, and auto-approve movies with sufficient votes
        while($row = $result->fetch_assoc())
        {
	    if ($row["TotalGoldenVotes"] > 0 || $row["TotalVotes"] >= $VotesPerAutoApprove)
            {
                //auto-approve
                $movie = new MOVIE();
                $movie->Load($row["MovieID"]);
                $this->AddToUnplayedList($movie);
                $QuotasFull = $this->UpdateQuotas($movie, $QuotaList, $NumQuotas);
		$AutoApprovedMovies[$NumAutoApprovedMovies++] = $row["MovieID"];
            }
            else
            {
                for ($i = 0; $i < ($row["TotalVotes"] * $TicketsPerVote); $i++)
                {
                    $Tickets[$NumTickets++] = $row["MovieID"];
                }
            }
        }

        $VoteTickets = $NumTickets;

        //Fetch the full pool of movies
        $query = $this->GetConnection()->prepare(
            "SELECT m.MovieID, (m.Freshness + m.MetaScore) AS CriticScore FROM MOVIE m JOIN MOVIE_LIST l on l.MovieID = m.MovieID where l.Name = 'Ballot'"
        );
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        //Assign tickets based on critic score for each non-auto-approved movie
        while($row = $result->fetch_assoc())
        {
	    $MovieAutoApproved = FALSE;

            for ($i = 0; $i < $NumAutoApprovedMovies; $i++)
	    {
	        if ($row["MovieID"] == $AutoApprovedMovies[$i])
    		{
		    $MovieAutoApproved = TRUE;
		    break;
		}
	    }

	    if (!$MovieAutoApproved)
	    {
                $TotalMovies++;
                for ($i = 0; $i < $row["CriticScore"]; $i++)
                {
                    $Tickets[$NumTickets++] = $row["MovieID"];
                }
    	    }
        }

        echo $NumTickets . " tickets.<BR>";
        echo $NumTickets - $VoteTickets . " come from critic score.<BR>";
        echo $VoteTickets . " (" . ($VoteTickets/$NumTickets)*100 . "%) come from votes.<BR>";
        //Add all movies to either the unplayed movie list or backup movie list
        for($i = 0; $i < $TotalMovies; $i++)
        {
            //Pull a ticket and load the corresponding movie
            $MovieID = $this->PullTicket($Tickets, $NumTickets);
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
                $this->AddToUnplayedList($movie);
                $QuotasFull = $this->UpdateQuotas($movie, $QuotaList, $NumQuotas);
            }

            //If the pulled movie doesn't satisfy a quota, add it to the backup movie list
            else
            {
                $this->AddToList($BackupMovieList, $NumBackupMovies, $movie);
            }

            //Remove all tickets for the pulled movie, so it is not pulled again.
            $this->RemoveTickets($Tickets, $NumTickets, $MovieID);
        }

        //If we have not yet satisfied the MinimumMovies requirement, add movies from the backup list
        //until the MinimumMovies requirement is met
        $StartingIndex = $this->NumUnplayedMovies;
        for ($i = $StartingIndex; $i < $MinimumMovies; $i++)
        {
            $this->AddToUnplayedList($BackupMovieList[$i - $StartingIndex]);
        }

        echo "Quota fulfilled (or gave up) with:  " . $this->UnplayedMovies[$StartingIndex - 1] . " (" . $StartingIndex . " movies)<BR>";
        $this->DisplayMovieList($this->UnplayedMovies, $this->NumUnplayedMovies);

        echo "<BR><B>Total RunTime:  " . $this->GetTotalRunTime() . "</B><BR>";
        $this->DisplayGenreRunTimes();
        echo "<BR><BR><BR>";

        //$this->DisplayMovieList($BackupMovieList, $NumBackupMovies);

        return true;

    }

    private function GetTimePeriod($Time)
    {
        $hour = date("G", $Time);
        
        if ($this->NumPlayedMovies == 0)
        {
            return "Opener";
        }

        if ($hour >= 20 || $hour < 2)
        {
            $this->EnteredPrimetime = true;
            return "Primetime";
        }

        if ($hour >= 4 && $hour < 8)
        {
            $this->EnteredSleeper = true;
            return "Sleeper";
        }

        if ($hour >= 8 && $hour < 10)
        {
            $this->EnteredWakeup = true;
             return "Wake-Up";
        }

        return "Free";
    }

    public function GetNextMovie($Time, $EndOfRegulation)
    {
        if ($this->NumUnplayedMovies == 0)
        {
            return "";
        }

        $TimePeriod = $this->GetTimePeriod($Time);
        echo $TimePeriod . "<BR>";

        $PhaseOneList;
        $NumPhaseOneMovies = 0;

        for ($i=0; $i < $this->NumUnplayedMovies; $i++)
        {
            if ($this->UnplayedMovies[$i]->MatchesTimePeriod($Time, $TimePeriod, ($this->EnteredPrimetime && $TimePeriod != "Sleeper"), $this->EnteredSleeper, $this->EnteredWakeup, $EndOfRegulation))
            {
                //echo "PhaseOne: " . $this->UnplayedMovies[$i] . "<BR>";
                $PhaseOneList[$NumPhaseOneMovies++] = $this->UnplayedMovies[$i];
            }
        }

        if ($NumPhaseOneMovies == 0)
        {
            //echo "PhaseOne Empty<BR>";
            $PhaseOneList = $this->UnplayedMovies;
            $NumPhaseOneMovies = $this->NumUnplayedMovies;
        }

        $PhaseTwoList;
        $NumPhaseTwoMovies = 0;

        for ($i = 0; $i < $NumPhaseOneMovies; $i++)
        {
            if ($this->NumPlayedMovies == 0 || $PhaseOneList[$i]->ExcludesGenres($this->PlayedMovies[$this->NumPlayedMovies - 1]))
            {
                //echo "PhaseTwo: " . $PhaseOneList[$i] . "<BR>";
                $PhaseTwoList[$NumPhaseTwoMovies++] = $PhaseOneList[$i];
            }
        }

        if ($NumPhaseTwoMovies == 0)
        {
            //echo "PhaseTwo Empty<BR>";
            $PhaseTwoList = $PhaseOneList;
            $NumPhaseTwoMovies = $NumPhaseOneMovies;
        }

        $index = mt_rand(0, $NumPhaseTwoMovies-1);
        $this->MoveToPlayedList($PhaseTwoList[$index]);
        //echo $index . " " . $this->PlayedMovies[$this->NumPlayedMovies-1];
        //$PhaseTwoList[$index]->DisplayModule();

        return $this->PlayedMovies[$this->NumPlayedMovies-1];
    }

    public function Simulate($StartingTime)
    {
        $Time = $StartingTime;
        $EndOfRegulation = $StartingTime + 94320;
        $DownTimeLength = 600;
        $CloserShown = false;

        echo "Reclinathon Simulation for the list:<BR><BR>";
        //$this->GenerateList($VotesPerAutoApprove, $TicketsPerVote, $MinimumMovies, $QuotaList, $NumQuotas);

        while($this->HasUnplayedMovies())
        {
            echo date("Y-m-d H:i:s", $Time) . "<BR>";
            $movie = $this->GetNextMovie($Time, $EndOfRegulation);
            $Time += ($movie->GetRunTime() * 60);
            if ($Time >= $EndOfRegulation)
            {
                if (!$CloserShown)
                {
                    echo "Closer<BR>";
                    $CloserShown = true;
                }
                else
                {
                    echo "Extra-Innings<BR>";
                }
            }
            $Time += $DownTimeLength;
            $movie->DisplayModule();
            echo "<BR>";
        } 
    }  
	
	public function DisplayFeedImages()
	{
		for ($i=0; $i < $this->NumMovies; $i++)
        {
			$this->AllMovies[$i]->DisplayFeedImage();
		}
	}

}

?>