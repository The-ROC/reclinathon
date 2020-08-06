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

    public function DisplayBackupMovieList()
    {
        echo "<BR>Backup Movie List<BR>";
        for ($i = 0; $i < $this->NumBackupMovies; $i++)
        {
            echo $this->BackupMovieList[$i] . "<BR>";
        }
        echo "<BR>";
    }

    public function GenerateList($MinimumMovies, $QuotaList, $NumQuotas)
    {
        $TotalMovies = 0;
        $QuotasFull = false;
        if ($NumQuotas == 0)
        {
            $QuotasFull = true;
        }

        //Fetch the full pool of movies
        $query = $this->GetConnection()->prepare("SELECT MovieID, Freshness FROM MOVIE");
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        //Assign tickets based on freshness
        while($row = $result->fetch_assoc())
        {
            $TotalMovies++;
            for ($i = 0; $i < $row["Freshness"]; $i++)
            {
                $this->Tickets[$this->NumTickets] = $row["MovieID"];
                $this->NumTickets++;
            }
        }

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
        $this->DisplayBackupMovieList();

    }

}

?>