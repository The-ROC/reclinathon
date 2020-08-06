<?php

class REMOTE_RECLINATHON extends RTT_COMMON
{
    protected $StartTime;   // STRING?
    protected $Movies;      // MOVIE_NETFLIX list
	protected $succeeded;

    public function LoadFromForm()
    {
		$this->succeeded = false;
        $this->Movies = array();
		
		$localStartTime = $_POST["startTime"];
		$localTimezoneOffset = $_POST["timezoneOffset"] * 60;
		$serverStartTime = strtotime($localStartTime);
		$serverTimezoneOffset = date('Z') * -1;
		$this->StartTime = $serverStartTime + ($localTimezoneOffset - $serverTimezoneOffset);
		
		// Create or fetch each movie.
        foreach((array)$_POST["movies"] as $movie)
        {
            $movieNetflix = new MOVIE_NETFLIX();
            $movieNetflix->LoadFromObject($movie);
			
			$title = $movieNetflix->GetTitle();
			$query = $movieNetflix->GetConnection()->prepare(
				"select * from MOVIE where Title = ?"
			);
			$query->bind_param('s', $title);
			$result = $movieNetflix->query($query);
			
			if ($result->num_rows == 0)
			{
				$movieNetflix->CreateReclinathonInsert();
			}
			else
			{
				$row = $result->fetch_assoc();
				$url = $movieNetflix->GetUrl();
				$runtime = $movieNetflix->GetRunTime();
				$movieNetflix->Load($row["MovieID"]);
				$movieNetflix->SetUrl($url);
				$movieNetflix->SetRuntime($runtime);
				$movieNetflix->UpdateUrl();
				$movieNetflix->UpdateRuntime();
			}
			
			if ($movieNetflix->GetID() != 0)
			{
				array_push($this->Movies, $movieNetflix); 
			}
        }
		
		// Create a MOVIE_LIST
		for ($i = 0; $i < count($this->Movies); $i++)
		{
		    if ($i == 0)
		    {
			    $query = $this->GetConnection()->prepare(
					"DELETE FROM MOVIE_LIST WHERE Name = 'demo'"
				);
			    $result = $this->query($query);
				if (!$result)
				{
					echo "Failed to delete existing list";
					return;
				}
			}
			
			$movieId = $this->Movies[$i]->GetID();
			$query = $this->GetConnection()->prepare(
				"INSERT INTO MOVIE_LIST (`Name`, `MovieID`, `Order`, `Played`, `MoviePath`) VALUES ('demo', ?, ?, '0', '')"
			);
			$query->bind_param('ii', $movieId, $i);
			$result = $this->query($query);
			if (!$result)
			{
				echo "Failed to insert movie: $query<br>";
				return;
			}
		}
		
		// Delete out any old contexts
		$query = $this->GetConnection()->prepare(
			"DELETE FROM RECLINATHON_CONTEXT WHERE Season = 'demo'"
		);
	    $result = $this->query($query);
		if (!$result)
		{
			echo "Failed to delete existing contexts";
			return;
		}
				
		// Create the initial countdown context
		$timeStamp = time();
		
		if ($timeStamp > $this->StartTime)
		{
			echo "Cannot create a Reclinathon in the past yet...the scientists are working on that.<br>";
			return;
		}
		
		$duration = $this->StartTime - $timeStamp;
		$initialMovieId = $this->Movies[0]->GetID();
		$query = $this->GetConnection()->prepare(
			"INSERT INTO RECLINATHON_CONTEXT (`TimeStamp`, `EstimatedDuration`, `CaptainID`, `StateID`, `ModifierID`, `MovieID`, `Season`, `LogoID`, `Pending`) VALUES (?, ?, '1', '3', '9', ?, 'demo', '0', '0')"
		);
		$query->bind_param('iii', $timeStamp, $duration, $initialMovieId);
		$result = $this->query($query);	
		if (!$result)
		{
			echo "Failed to create countdown context<br>";
			return;
		}
		
		// Create a context for each movie and downtime
		$timeStamp += 100000;
		for ($i = 0; $i < count($this->Movies); $i++)
		{
		    if ($i != 0)
			{
				// Insert downtime
				$downtimeContext = new RECLINATHON_CONTEXT();
				if (!$downtimeContext->CreateDowntime("demo", $this->Movies[$i]->GetID(), $timeStamp, 300))
				{
					echo "Failed to insert downtime context %i<br>";
					return;
				}
				$timeStamp += 300;
			}
			
			// Insert downtime
			$movieDuration = $this->Movies[$i]->GetRunTime() * 60;
			$movieContext = new RECLINATHON_CONTEXT();
			if (!$movieContext->CreateMovieContext("demo", $this->Movies[$i]->GetID(), $timeStamp, $movieDuration))
			{
				echo "Failed to insert movie context %i<br>";
				return;
			}
			$timeStamp += $movieDuration;
		}
		
		// Make the Reclinathon active
		$query = $this->GetConnection()->prepare(
			"UPDATE current_remote_reclinathon SET RemoteReclinathonId = 'demo' WHERE RemoteReclinathonId = ''"
		);
        $result = $this->query($query);
		if (!$result)
		{
			echo "Failed to set the Reclinathon as active<br>";
			return;
		}
		
		// Post an event to the feed that a Reclinathon was created.
		$feedEvents = new FEED_EVENTS();
		$feedEvents->PostSystemEvent("Created a Reclinathon!", time(), "images/play.png");	
		
		$this->succeeded = true;
    }

    public function ProcessForm()
    {
        $this->LoadFromForm();

        return TRUE;
    }

    public function FinishProcessForm()
    {
		if ($this->succeeded)
		{
            echo "<meta http-equiv='refresh' content='0;mockup/feed.php' />";
		}
    }
	
	public function GetCurrentRemoteReclinathonId()
    {
		$id = "";
        $query = $this->GetConnection()->prepare(
			"SELECT * from current_remote_reclinathon limit 1"
		);
	    $result = $this->Query($query);

	    if ($result && $result->num_rows == 1)
        {
            $row = $result->fetch_assoc();

            if ($row)
            {
				if ($row["RemoteReclinathonId"])
				{
                    $id = $row["RemoteReclinathonId"];
				}
            }
        }

        return $id;
    } 
}

?>