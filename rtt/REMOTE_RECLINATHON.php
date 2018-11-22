<?php

class REMOTE_RECLINATHON extends RTT_COMMON
{
    protected $StartTime;   // STRING?
    protected $Movies;      // MOVIE_NETFLIX list
	protected $succeeded;

    public function LoadFromForm()
    {
		$this->succeeded = false;
        $this->StartTime = $_POST["startTime"];
        $this->Movies = array();
		
		// Create or fetch each movie.
        foreach((array)$_POST["movies"] as $movie)
        {
            $movieNetflix = new MOVIE_NETFLIX();
            $movieNetflix->LoadFromObject($movie);
			
			$title = $movieNetflix->GetTitle();
			$query = "select * from MOVIE where Title = '$title'";
			$result = $movieNetflix->query($query);
			
			if (mysql_num_rows($result) == 0)
			{
				$movieNetflix->CreateReclinathonInsert();
			}
			else
			{
				$row = mysql_fetch_assoc($result);
				$url = $movieNetflix->GetUrl();
				$movieNetflix->Load($row["MovieID"]);
				$movieNetflix->SetUrl($url);
				$movieNetflix->UpdateUrl();
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
			    $query = "DELETE FROM MOVIE_LIST WHERE Name = 'demo'";
			    $result = $this->query($query);
				if (!$result)
				{
					echo "Failed to delete existing list";
					return;
				}
			}
			
			$movieId = $this->Movies[$i]->GetID();
			$query = "INSERT INTO MOVIE_LIST (`Name`, `MovieID`, `Order`, `Played`, `MoviePath`) VALUES ('demo', '$movieId', '$i', '0', '')";
			$result = $this->query($query);
			if (!$result)
			{
				echo "Failed to insert movie: $query<br>";
				return;
			}
		}
		
		// Delete out any old contexts
		$query = "DELETE FROM RECLINATHON_CONTEXT WHERE Season = 'demo'";
	    $result = $this->query($query);
		if (!$result)
		{
			echo "Failed to delete existing contexts";
			return;
		}
				
		// Create the initial countdown context
		$timeStamp = time();
		$initialMovieId = $this->Movies[0]->GetID();
		$query = "INSERT INTO RECLINATHON_CONTEXT (`TimeStamp`, `EstimatedDuration`, `CaptainID`, `StateID`, `ModifierID`, `MovieID`, `Season`, `LogoID`, `Pending`) VALUES ('$timeStamp', '100000', '1', '3', '9', '$initialMovieId', 'demo', '0', '0')";
		$result = mysql_query($query);	
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
		$query = "UPDATE current_remote_reclinathon SET RemoteReclinathonId = 'demo' WHERE RemoteReclinathonId = ''";
        $result = $this->query($query);
		if (!$result)
		{
			echo "Failed to set the Reclinathon as active<br>";
			return;
		}
		
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
        $query = "SELECT * from current_remote_reclinathon limit 1";
	    $result = $this->Query($query);

	    if ($result && mysql_num_rows($result) == 1)
        {
            $row = mysql_fetch_assoc($result);

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