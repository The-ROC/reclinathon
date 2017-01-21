<?php

#include RTTHeader.php";

class GENRE
{
    public $GenreID = 0;
    public $Name = "unknown";
    public $Canonical = 0;

    function __construct($GenreID, $Name, $Canonical)
    {
        $this->GenreID = $GenreID;
        $this->Name = $Name;
        $this->Canonical = $Canonical;
    }
}

class MOVIE extends RTT_COMMON
{
    protected $MovieID = 0; 	// INT
    protected $Title; 	    	// STRING
    protected $RunTime; 	// INT
    protected $NumGenres;	// INT
    protected $Genres;		// GENRE list
    protected $TrailerLink;	// STRING
    protected $IMDBLink;        // STRING
    protected $Freshness;	// INT
    protected $Image;		// STRING
    protected $MoviePath;       // STRING
    protected $Director;	// STRING
    protected $Year;		// INT
    protected $Metascore;	// INT
    protected $NumActors;	// INT
    protected $Actors;		// STRING list
	protected $ThemeMovie;   // BOOL
	protected $HighlightThemeMovies; // BOOL
	protected $SpecialEvent; // BOOL
	protected $ShowVoting; // BOOL
   
    function __construct() 
    {
        $this->Title = 'TBA';
        $this->RunTime = 0;
        $this->NumGenres = 0;
        $this->TrailerLink = '#';
        $this->IMDBLink = '#';
        $this->Freshness = 0;
        $this->Image = 'unknown';
        $this->MoviePath = '';
        $this->Director = 'N/A';
        $this->Year = 0;
        $this->Metascore = 0;
        $this->NumActors = 0;
		$this->ThemeMovie = false;
		$this->HighlightThemeMovies = false;
		$this->SpecialEvent = false;
		$this->ShowVoting = true;
    }
	
	public function HideVotingInfo()
	{
		$this->ShowVoting = false;
	}
	
	public function HighlightThemeMovies()
	{
		$this->HighlightThemeMovies = true;
	}

    public function Load($MovieID)
    {
        $query = "SELECT * FROM MOVIE WHERE MovieID = " . $MovieID;
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        $row = mysql_fetch_assoc($result);
        if (!$row)
        {
            return FALSE;
        }

        $this->MovieID = $row["MovieID"];
        $this->Title = $row["Title"];
        $this->RunTime = $row["RunTime"];
        $this->TrailerLink = $row["TrailerLink"];
        $this->IMDBLink = $row["IMDBLink"];
        $this->Freshness = $row["Freshness"];
        $this->Image = $row["Image"];
        $this->Year = $row["Year"];
        $this->Metascore = $row["Metascore"];
        $this->Director = $row["Director"];

        $this->NumGenres = 0;
        $query = "SELECT g.GenreID as GenreID, g.Name as Name, g.Canonical as Canonical FROM GENRE g JOIN MOVIE_GENRE mg ON g.GenreID = mg.GenreID WHERE mg.MovieID = " . $MovieID;
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        while ($row = mysql_fetch_assoc($result))
        {
			if ($row["Name"] == "Reclinathon Theme")
			{
				$this->ThemeMovie = true;
			}
			else if ($row["Name"] == "Reclinathon Special Event")
			{
				$this->SpecialEvent = true;
			}
            $this->Genres[$this->NumGenres] = new GENRE($row["GenreID"], $row["Name"], $row["Canonical"]);
            $this->NumGenres++;
        }

        $this->NumActors = 0;
        $query = "SELECT * FROM ACTORS WHERE MovieID = '" . $MovieID . "'";
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        while ($row = mysql_fetch_assoc($result))
        {
            $this->Actors[$this->NumActors] = $row["Name"];
            $this->NumActors++;
        }

        return TRUE;
    }

    public function LoadFromMovieList($MovieID, $MoviePath)
    {
        $this->Load($MovieID);
        $this->MoviePath = $MoviePath;
    }

    public function LoadFromForm()
    {
        $this->MovieID = $_POST["ObjectID"];
        $this->Title = $_POST["Title"];
        $this->RunTime = $_POST["RunTime"];

        $this->NumGenres = 0;
        $query = "SELECT * FROM GENRE";
        $result = $this->query($query);
        while($row = mysql_fetch_assoc($result))
        {
            if ($_POST["genre" . $row[GenreID]] != "")
            {
				if ($row["Name"] == "Reclinathon Theme")
			    {
				    $this->ThemeMovie = true;
			    }
				
                $this->Genre += $row["Value"];
                $this->Genres[$this->NumGenres] = new GENRE($row["GenreID"], $row["Name"], $row["Canonical"]);
                $this->NumGenres++;
            }
        }

        $this->TrailerLink = $_POST["TrailerLink"];
        $this->IMDBLink = $_POST["IMDBLink"];
        $this->Freshness = $_POST["Freshness"];
        $this->Image = $_POST["Image"];

        return TRUE;
    }

    public function LoadFromArray($movie)
    {
        if ($movie["Title"] != "")
        {
            $this->Title = $movie["Title"];
        }


        if ($movie["RunTime"] != "")
        {
            $this->RunTime = $movie["RunTime"];
        }

        $this->NumGenres = 0;
        $GenreCount = count($movie["Genres"]);

        for ($i = 0; $i < $GenreCount; $i++)
        {
            $query = "SELECT * FROM GENRE WHERE Name = '" . $movie["Genres"][$i] . "'";
            $result = $this->query($query);
            if (mysql_num_rows($result) == 0)
            {
                $query = "INSERT INTO GENRE (`Name`, `Canonical`) VALUES ('" . $movie["Genres"][$i] . "', '1')";
                $result = $this->query($query);
                if ($result)
                {
                    $query = "SELECT * FROM GENRE WHERE Name = '" . $movie["Genres"][$i] . "'";
                    $result = $this->query($query);
                }
            }

            $row = mysql_fetch_assoc($result);
			if ($row["Name"] == "Reclinathon Theme")
			{
				$this->ThemeMovie = true;
			}
            $this->Genres[$this->NumGenres] = new GENRE($row["GenreID"], $row["Name"], $row["Canonical"]);
            $this->NumGenres++;
        }        

        if ($movie["TrailerLink"] != "")
        {
            $this->TrailerLink = str_replace("*", "&", $movie["TrailerLink"]);
        }

        if ($movie["IMDBLink"] != "")
        {
            $this->IMDBLink = $movie["IMDBLink"];
        }

        if ($movie["Freshness"] != "")
        {
            $this->Freshness = $movie["Freshness"];
        }

        if ($movie["PosterLink"] != "")
        {
            $this->Image = $movie["PosterLink"];
        }

        if ($movie["Director"] != "")
        {
            $this->Director = $movie["Director"];
        }

        if ($movie["Year"] != "")
        {
            $this->Year = $movie["Year"];
        }

        if ($movie["MetaScore"] != "")
        {
            $this->Metascore = $movie["MetaScore"];
        }

        $ActorCount = count($movie["Actors"]);

        for ($i = 0; $i < $ActorCount; $i++)
        {
            if ($movie["Actors"][$i] != "")
            {
                $this->Actors[$this->NumActors] = $movie["Actors"][$i];
                $this->NumActors++;
            }
        }

        return TRUE;
    }

    public function GetRunTime()
    {
        return $this->RunTime;
    }

    public function GetID()
    {
        return $this->MovieID;
    }
	
	public function GetTitle()
    {
        return $this->Title;
    }

    public function DisplayImage()
    {
	return "<IMG BORDER='3' WIDTH='200' SRC = '" . str_replace("'", "%27", $this->Image) . "' alt = '$this->Title' >";
    }

    public function DisplayGenres()
    {
        $genres = "";
        for ($i = 0; $i < $this->NumGenres; $i++)
        {
            $genres = $genres . "<BR>";
            if ($this->Genres[$i]->Canonical == 1)
            {
                $genres = $genres . "<B>";
            } 
            $genres = $genres . $this->Genres[$i]->Name;
            if ($this->Genres[$i]->Canonical == 1)
            {
                $genres = $genres . "</B>";
            }
        }
        return $genres;
    }

    public function DisplayCast()
    {
        $cast = "";
        for ($i = 0; $i < $this->NumActors; $i++)
        {
            $cast = $cast . "<BR>";
            $cast = $cast . $this->Actors[$i];
        }
        return $cast;
    }

    public function GetGenres()
    {
        return $this->Genres;
    }

    public function GetNumGenres()
    {
        return $this->NumGenres;
    }

    public function ContainsGenre($GenreID)
    {
        for ($i = 0; $i < $this->NumGenres; $i++)
        {
            if ($this->Genres[$i]->GenreID == $GenreID)
            {
                return true;
            }
        }

        return false;
    }

    public function ContainsGenreByName($Genre)
    {
        //echo $Genre . "<BR>";
        for ($i = 0; $i < $this->NumGenres; $i++)
        {
            if ($this->Genres[$i]->Name == $Genre)
            {
                return true;
            }
        }

        return false;
    }

    public function ExcludesGenres($movie)
    {
        for ($i = 0; $i < $movie->NumGenres; $i++)
        {
            $genre = $movie->Genres[$i];
            if ($genre->Canonical && $this->ContainsGenre($genre->GenreID))
            {
                return false;
            }
        }

        return true;
    }

    public function MatchesTimePeriod($Time, $TimePeriod, $PrimetimeOK, $SleeperOK, $WakeupOK, $EndOfRegulation)
    {
        if ($EndOfRegulation <= ($Time + ($this->RunTime * 60)))
        {
            //echo "Closer<BR>";
            return $this->ContainsGenreByName("Closer");
        }

        if ($TimePeriod != "Free")
        {
            return $this->ContainsGenreByName($TimePeriod);
        }

        if (!$PrimetimeOK && $this->ContainsGenreByName("Primetime"))
        {
            return false;
        }

        if (!$SleeperOK && $this->ContainsGenreByName("Sleeper"))
        {
            return false;
        }

        if (!$WakeupOK && $this->ContainsGenreByName("Wake-Up"))
        {
            return false;
        }

        return true;
    }

    public function DisplayModule()
    {
        echo "<TABLE>";
        echo "<TR cellspacing='0'>";
		
		if ($this->HighlightThemeMovies && $this->ThemeMovie)
		{
			echo "<TH class='theme'>";
		}
		else
		{
			echo "<TH>";
		}
		
        //echo "<A HREF = 'insert.php?class=MOVIE&ObjectID=" . $this->MovieID . "'>";
        echo $this->Title;

        if ($this->Year != 0)
        {
            echo " (" . $this->Year . ")";
        }

        //echo "</A>";
        echo "</TH>";
		
		$class = ($this->HighlightThemeMovies && $this->ThemeMovie) ? "themeRight" : "right";
		echo "<TH CLASS='$class'>";
		
		if ($this->Freshness != 0 && $this->Freshness != "")
		{
			echo "$this->Freshness" . "%";
		}

        if ($this->Metascore != 0 && $this->Metascore != "")
        {
            echo "(" . $this->Metascore . ")";
        }

		$OpenCellTag = ($this->HighlightThemeMovies && $this->ThemeMovie) ? "<TD class='theme'>" : "<TD>";
		$inputClass = ($this->ThemeMovie) ? "themeMovie" : "generalMovie";
		
		if ($this->ShowVoting)
		{
            echo "<INPUT TYPE='checkbox' CLASS='$inputClass' NAME='vote" . $this->MovieID . "' ID='vote" . $this->MovieID . "' VALUE='movie" . $this->MovieID . "' onclick='ToggleDisplay(this)'><INPUT TYPE='button' VALUE='GOLD' onclick='SetGolden(" . $this->MovieID . ", \"" . $this->Title . "\")'>";
		}
		echo "</TH></TR>";
        echo "<TR ID='movie" . $this->MovieID . "'>$OpenCellTag";
        echo $this->DisplayImage();
        echo "</TD>$OpenCellTag";
        echo "<B><U><FONT SIZE='+2'>Genre(s):</FONT></U></B>" . $this->DisplayGenres() . "<BR><BR><B><U><FONT SIZE='+2'>Runtime:</FONT></U></B><BR>" . $this->RunTime . " min";
        echo "<BR><BR><B><U><FONT SIZE='+2'>Cast and Crew:</FONT></U></B><BR>Director: " . $this->Director . "<BR>" . $this->DisplayCast();
        echo "<BR><BR><BR><A HREF='" . $this->IMDBLink . "' target='_blank'>IMDB</A><BR><A HREF = '" . $this->TrailerLink . "' target='_blank'>Trailer</A></TD></TR>";
        echo "</TABLE>";
    }

    public function DumpMovies($season)
    {
        $query = "SELECT DISTINCT MovieID, MoviePath FROM MOVIE_LIST WHERE NAME = '" . $season . "'";
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        print "<MovieList>";
        while($row = mysql_fetch_assoc($result))
        {
            $movie = new MOVIE();
            $movie->LoadFromMovieList($row["MovieID"], $row["MoviePath"]);
            $movie->DumpXml();
        }
        print "</MovieList>";
    }

    public function DisplayAll()
    {
        $TotalRunTime = 0;
        $GenreRunTimes;

        $query = "SELECT m.MovieID FROM MOVIE m JOIN MOVIE_LIST l ON m.MovieID = l.MovieID WHERE l.Name = 'Ballot' ORDER BY (m.Freshness + m.Metascore) DESC";
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        echo "<INPUT TYPE='button' VALUE='Reset' onclick='HideAll()'><INPUT TYPE='button' VALUE='Select All' onclick='ShowAll()'><BR>";
        while($row = mysql_fetch_assoc($result))
        {
            $movie = new MOVIE();
            $movie->Load($row["MovieID"]);
            $movie->DisplayModule();
            $TotalRunTime += $movie->RunTime;
            for ($i = 0; $i < $movie->NumGenres; $i++)
            {
                $GenreRunTimes[$movie->Genres[$i]->GenreID] += $movie->RunTime;
            }
        }
        echo "<BR>Total Run Time:  " . ((int)($TotalRunTime / 60)) . " hours " . $TotalRunTime % 60 . " min<BR>";

        $query = "SELECT * FROM GENRE";
        $result = $this->query($query);
        while ($row = mysql_fetch_assoc($result))
        {
            echo "<BR>" . $row["Name"];
            if ($row["Canonical"] == 1)
            {
                echo "*";
            }
            echo ":  " . $GenreRunTimes[$row["GenreID"]] . " min";
        }

    }
	
	public function DisplaySpecialElection()
    {
        $TotalRunTime = 0;
        $GenreRunTimes;

        $query = "SELECT m.MovieID FROM MOVIE m JOIN MOVIE_LIST l ON m.MovieID = l.MovieID WHERE l.Name = 'Ballot' ORDER BY (m.Freshness + m.Metascore) DESC";
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        echo "<INPUT TYPE='button' VALUE='Reset' onclick='HideAll()'><INPUT TYPE='button' VALUE='Select All' onclick='ShowAll()'><BR>";
		$ThemeMovies = array();
		$GeneralMovies = array();
		$SpecialEvents = array();
        while($row = mysql_fetch_assoc($result))
        {
            $movie = new MOVIE();
            $movie->Load($row["MovieID"]);
			$movie->HighlightThemeMovies();
			
			if ($movie->ThemeMovie)
			{
				array_push($ThemeMovies, $movie);
			}
			else if ($movie->SpecialEvent)
			{
				array_push($SpecialEvents, $movie);
			}
			else
			{
				array_push($GeneralMovies, $movie);
			}
		}
		
		if (count($ThemeMovies) > 0)
		{
		    echo "<BR><H2>Special Election: Reclinathon Theme Movies<BR><I><U>'In Memoriam 2016: So Long, and Thanks for all the Fish'</I></U></H2>";
			echo "At this year's Reclinathon, we'll honor some of the acting legends that were lost this year.  What better tribute can be offered than a screening of your film in Mary's basement with a bunch of idiots?  Some of those being recognized in the special election ballot are:<br><ul><li>Alan Rickman (Die Hard, Alice Through the Looking Glass)</li><li>Gene Wilder (Blazing Saddles, Willy Wonka and the Chocolate Factory)</li><li>David Bowie (Labyrinth)</li><li>Prince (Purple Rain)</li><li>David Huddleston (The Big Lebowski)</li><li>Jon Polito (The Big Lebowski)</li><li>Anton Yelchin (Star Trek Beyond)</li><li>Gary Shandling (The Jungle Book)</li><li>Kenny Baker (Star Wars: Episode VII - The Force Awakens)</li></ul><br>";
		    foreach ($ThemeMovies as &$movie)
		    {			
                $movie->DisplayModule();
                $TotalRunTime += $movie->RunTime;
                for ($i = 0; $i < $movie->NumGenres; $i++)
                {
                    $GenreRunTimes[$movie->Genres[$i]->GenreID] += $movie->RunTime;
                }
            }
		}
		
		if (count($SpecialEvents) > 0)
		{
		    echo "<BR><H2>Reclinathon Special Events</H2>";
		    foreach ($SpecialEvents as &$movie)
		    {			
                $movie->DisplayModule();
                $TotalRunTime += $movie->RunTime;
                for ($i = 0; $i < $movie->NumGenres; $i++)
                {
                    $GenreRunTimes[$movie->Genres[$i]->GenreID] += $movie->RunTime;
                }
            }
		}
		
		echo "<BR><H2>General Election</H2>";
		foreach ($GeneralMovies as &$movie)
		{			
            $movie->DisplayModule();
            $TotalRunTime += $movie->RunTime;
            for ($i = 0; $i < $movie->NumGenres; $i++)
            {
                $GenreRunTimes[$movie->Genres[$i]->GenreID] += $movie->RunTime;
            }
        }
		
        echo "<BR>Total Run Time:  " . ((int)($TotalRunTime / 60)) . " hours " . $TotalRunTime % 60 . " min<BR>";

        $query = "SELECT * FROM GENRE";
        $result = $this->query($query);
        while ($row = mysql_fetch_assoc($result))
        {
            echo "<BR>" . $row["Name"];
            if ($row["Canonical"] == 1)
            {
                echo "*";
            }
            echo ":  " . $GenreRunTimes[$row["GenreID"]] . " min";
        }

    }

    public function DisplayForm()
    {
        echo "<FORM ACTION='processform.php' METHOD='post'>";
        echo "<TABLE>";
        echo "<TR><TD>Title</TD><TD><INPUT TYPE='text' NAME='Title' VALUE='" . $this->Title ."'></TD></TR>";
        echo "<TR><TD>RunTime</TD><TD><INPUT TYPE='RunTime' NAME='RunTime' VALUE='" . $this->RunTime ."'></TD></TR>";
        echo "<TR><TD>Genre</TD><TD>(check all that apply)</TD></TR>";

        $query = "SELECT * FROM GENRE";
        $result = $this->query($query);
        while($row = mysql_fetch_assoc($result))
        {
            echo "<TR><TD>" . $row["Name"];
            if ($row["Canonical"] == 1)
            {
                echo "*";
            }
            echo "</TD><TD><INPUT TYPE='checkbox' NAME='genre" . $row["GenreID"] . "'";
            if ($this->ContainsGenre($row["GenreID"]))
            {
                echo " CHECKED";
            }
            echo "></TD></TR>";
        }

        echo "<TR><TD>TrailerLink</TD><TD><INPUT TYPE='text' NAME='TrailerLink' VALUE='" . $this->TrailerLink ."'></TD></TR>";
        echo "<TR><TD>IMDBLink</TD><TD><INPUT TYPE='IMDBLink' NAME='IMDBLink' VALUE='" . $this->IMDBLink ."'></TD></TR>";
        echo "<TR><TD>Freshness</TD><TD><INPUT TYPE='Freshness' NAME='Freshness' VALUE='" . $this->Freshness ."'></TD></TR>";
        echo "<TR><TD>Image</TD><TD><INPUT TYPE='text' NAME='Image' VALUE='" . $this->Image ."'></TD></TR>";
        echo"<TR><TD> </TD><TD><INPUT TYPE='hidden' NAME='class' VALUE='MOVIE'><INPUT TYPE='hidden' NAME='ObjectID' VALUE='" . $this->MovieID . "'><INPUT  TYPE='submit' VALUE='Submit'></TD></TR>";
        echo "</TABLE>";
        echo "</FORM>";
    }

    public function DisplaySelectList()
    {
        $query = "SELECT MovieID, Title FROM MOVIE ORDER BY Title";
        $result = $this->query($query);
        if (!$result)
        {
            return false;
        }

        echo "<SELECT NAME='MovieID'>";

        while($row = mysql_fetch_assoc($result))
        {
            echo "<OPTION VALUE='" . $row["MovieID"] . "'";
            if ($this->MovieID == $row["MovieID"])
           {
                echo " SELECTED";
            }
            echo ">" . $row["Title"] . "</OPTION>";
        }

        echo "</SELECT>";

        return true;
    }
        

    public function Insert()
    {
        $query = "INSERT INTO MOVIE (Title, RunTime, TrailerLink, IMDBLink, Freshness, Image, Metascore, Director, Year) VALUES (";
        $query = $query . "'" . $this->Title . "', ";
        $query = $query . "'" . $this->RunTime . "', ";
        $query = $query . "'" . $this->TrailerLink . "', ";
        $query = $query . "'" . $this->IMDBLink . "', ";
        $query = $query . "'" . $this->Freshness . "', ";
        $query = $query . "'" . $this->Image . "', ";
        $query = $query . "'" . $this->Metascore . "', ";
        $query = $query . "'" . $this->Director . "', ";
        $query = $query . "'" . $this->Year . "')";

        //echo $query . "<BR>";
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        $query = "SELECT LAST_INSERT_ID() AS MovieID";
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }
        $row = mysql_fetch_assoc($result);
        $this->MovieID = $row["MovieID"]; 

        $query2 = "INSERT INTO MOVIE_LIST (Name, MovieID) VALUES ('Ballot', '" . $this->MovieID . "')";
        $result2 = $this->Query($query2);
        if (!$result)
        {
            return FALSE;
        }
        
        return $this->Update();
    }

    public function Update()
    {
        $query = "UPDATE MOVIE SET ";
        $query = $query . "Title = '" . $this->Title . "'";
        $query = $query . ", RunTime = '" . $this->RunTime . "'";
        $query = $query . ", TrailerLink = '" . $this->TrailerLink . "'";
        $query = $query . ", IMDBLink = '" . $this->IMDBLink . "'";
        $query = $query . ", Freshness = '" . $this->Freshness . "'";
        $query = $query . ", Image = '" . $this->Image . "'";
        $query = $query . ", Metascore = '" . $this->Metascore . "'";
        $query = $query . ", Director = '" . $this->Director . "'";
        $query = $query . ", Year = '" . $this->Year . "' WHERE MovieID = '" . $this->MovieID . "'";

        //echo $query . "<BR>";
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        $query = "DELETE FROM MOVIE_GENRE WHERE MovieID = '" . $this->MovieID . "'";
        $result = $this->Query($query);
        if(!$result)
        {
            return FALSE;
        }

        $query = "SELECT * FROM GENRE";
        $result = $this->query($query);
        while($row = mysql_fetch_assoc($result))
        {
            for ($i = 0; $i < $this->NumGenres; $i++)
            {
                if ($this->Genres[$i]->GenreID == $row["GenreID"])
                {
                    $query2 = "INSERT INTO MOVIE_GENRE (MovieID, GenreID) VALUES ('" . $this->MovieID . "', '" . $this->Genres[$i]->GenreID . "')";
                    //echo $query2 . "<BR>";
                    $result2 = $this->Query($query2);
                    if (!$result2)
                    {
                        return FALSE;
                    }
                    break;
                }
            }
        }

        $query = "DELETE FROM ACTORS WHERE MovieID = '" . $this->MovieID . "'";
        $result = $this->Query($query);
        if(!$result)
        {
            return FALSE;
        }

        for ($i = 0; $i < $this->NumActors; $i++)
        {
            $query2 = "INSERT INTO ACTORS (Name, MovieID) VALUES ('" . $this->Actors[$i] . "', '" . $this->MovieID . "')";
            //echo $query2 . "<BR>";
            $result2 = $this->Query($query2);
            if (!$result2)
            {
                return FALSE;
            }
        }
        
        return TRUE;
    }

    public function ProcessForm()
    {
        $this->LoadFromForm();
        if ($this->MovieID == 0)
        {
            return $this->Insert();
        }
        else
        {
            return $this->Update();
        }
    }

    public function __tostring()
    {
        return $this->Title;
    }

    public function GenerateList($MinimumMovies)
    {
        $Tickets;
        $NumTickets = 0;
        $MovieList;
        $query = "SELECT MovieID, Freshness FROM MOVIE";
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        //assign tickets based on freshness
        while($row = mysql_fetch_assoc($result))
        {
            for ($i = 0; $i < $row["Freshness"]; $i++)
            {
                $Tickets[$NumTickets] = $row["MovieID"];
                $NumTickets++;
            }
        }

        for($i = 0; $i < $MinimumMovies; $i++)
        {
            $NewMovieSelected = false;
            while(!$NewMovieSelected)
            {
                $NewMovieSelected = true;
                $ticket = mt_rand(0, $NumTickets-1);
                $MovieID = $Tickets[$ticket];
                for($j = 0; $j < $i; $j++)
                {
                    if ($MovieList[$j] == $MovieID)
                    {
                        $NewMovieSelected = false;
                        break;
                    }
                }
            }

            $MovieList[$i] = $MovieID;
            $movie = new MOVIE();
            $movie->Load($MovieID);
            $movie->DisplayModule();
        }
    }

}

?>