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
	protected $Synopsis; //STRING
	protected $Url; //STRING
   
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
		$this->Synopsis = '';
		$this->Url = '';
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
        $query = $this->GetConnection()->prepare("SELECT * FROM MOVIE WHERE MovieID = ?");
        $query->bind_param('i', $MovieID);
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        $row = $result->fetch_assoc();
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
		$this->Synopsis = $row["Synopsis"];
		$this->Url = $row["Url"];

        $this->NumGenres = 0;
        $query = $this->GetConnection()->prepare(
            "SELECT g.GenreID as GenreID, g.Name as Name, g.Canonical as Canonical FROM GENRE g JOIN MOVIE_GENRE mg ON g.GenreID = mg.GenreID WHERE mg.MovieID = ?"
        );
        $query->bind_param('i', $MovieID);
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        while ($row = $result->fetch_assoc())
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
        $query = $this->GetConnection()->prepare("SELECT * FROM ACTORS WHERE MovieID = ?");
        $query->bind_param('i', $MovieID);
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        while ($row = $result->fetch_assoc())
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
        $query = $this->GetConnection()->prepare("SELECT * FROM GENRE");
        $result = $this->query($query);
        while($row = $result->fetch_assoc())
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
            $query = $this->GetConnection()->prepare("SELECT * FROM GENRE WHERE Name = ?");
            $query->bind_param('s', $movie['Genres'][$i]);
            $result = $this->query($query);
            if ($result->num_rows($result) == 0)
            {
                $query = $this->GetConnection()->prepare(
                    "INSERT INTO GENRE (`Name`, `Canonical`) VALUES (?, '1')"
                );
                $query->bind_param('s', $movie['Genres'][$i]);
                $result = $this->query($query);
                if ($result)
                {
                    $query = $this->GetConnection()->prepare(
                        "SELECT * FROM GENRE WHERE Name = ?"
                    );
                    $query->bind_param('s', $movie['Genres'][$i]);
                    $result = $this->query($query);
                }
            }

            $row = $result->fetch_assoc();
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
		
		if ($movie["Synopsis"] != "")
        {
            $this->Synopsis = $movie["Synopsis"];
        }
		
		if ($movie["Url"] != "")
        {
            $this->Url = $movie["Url"];
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
	
	public function GetSynopsis()
	{
		return $this->Synopsis;
	}

    public function DisplayImage()
    {
        $result = "";
        if ($this->Url) {
            $result .= "<a href='" . $this->Url . "'>";
        }
        $result .= "<IMG BORDER='3' WIDTH='200' SRC = '" . str_replace("'", "%27", $this->Image) . "' alt = '$this->Title' >";
        if ($this->Url) {
            $result .= "</a>";
        }
	    return $result;
    }
	
	public function DisplayFeedImage()
	{
		echo "<div class='carousel-cell'><img border='3' height='150px' style='min-width: 100px' src='$this->Image' alt = '$this->Title' /></div>";
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
		
		if ($this->Synopsis != "")
		{
			echo "<BR><BR><B><U><FONT SIZE='+2'>Synopsis:</FONT></U></B><BR>" . $this->Synopsis;
		}
		
        echo "</TD>$OpenCellTag";
        echo "<B><U><FONT SIZE='+2'>Genre(s):</FONT></U></B>" . $this->DisplayGenres() . "<BR><BR><B><U><FONT SIZE='+2'>Runtime:</FONT></U></B><BR>" . $this->RunTime . " min";
        echo "<BR><BR><B><U><FONT SIZE='+2'>Cast and Crew:</FONT></U></B><BR>Director: " . $this->Director . "<BR>" . $this->DisplayCast();
        echo "<BR><BR><BR><A HREF='" . $this->IMDBLink . "' target='_blank'>IMDB</A><BR><A HREF = '" . $this->TrailerLink . "' target='_blank'>Trailer</A></TD></TR>";
        echo "</TABLE>";
    }

    public function DumpMovies($season)
    {
        $query = $this->GetConnection()->prepare(
            "SELECT DISTINCT MovieID, MoviePath FROM MOVIE_LIST WHERE NAME = ?"
        );
        $query->bind_param('s', $season);
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        print "<MovieList>";
        while($row = $result->fetch_assoc())
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

        $query = $this->GetConnection()->prepare(
            "SELECT m.MovieID FROM MOVIE m JOIN MOVIE_LIST l ON m.MovieID = l.MovieID WHERE l.Name = 'Ballot' ORDER BY (m.Freshness + m.Metascore) DESC"
        );
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        echo "<INPUT TYPE='button' VALUE='Reset' onclick='HideAll()'><INPUT TYPE='button' VALUE='Select All' onclick='ShowAll()'><BR>";
        while($row = $result->fetch_assoc())
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

        $query = $this->GetConnection()->prepare("SELECT * FROM GENRE");
        $result = $this->query($query);
        while ($row = $result->fetch_assoc())
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

        $query = $this->GetConnection()->prepare(
            "SELECT m.MovieID FROM MOVIE m JOIN MOVIE_LIST l ON m.MovieID = l.MovieID WHERE l.Name = 'Ballot' ORDER BY (m.Freshness + m.Metascore) DESC"
        );
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        echo "<INPUT TYPE='button' VALUE='Reset' onclick='HideAll()'><INPUT TYPE='button' VALUE='Select All' onclick='ShowAll()'><BR>";
		$ThemeMovies = array();
		$GeneralMovies = array();
		$SpecialEvents = array();
        while($row = $result->fetch_assoc())
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
		    echo "<BR><H2>Special Election: Reclinathon Theme Movies<BR><BR><I><U>'Apocalypse?...Now?!?'</I></U></H2>";
			echo "You know that old saying \"May you live in interesting times\"?  Sure seems like we're in pretty interesting times now, doesn't it?  Climate Change. Financial Crisis. Displaced Masses.<BR>  And the guys in charge?...yeah...let's not talk about the guys in charge.  But, if the movies have taught us anything about the complex nuances of geo-political hegemony coupled <BR> with unchecked anarcho-capitalism and the perils of a dyspeptic underclass (...and I think they <i>have</i>), it's that things could be worse...a LOT worse.  Therefore, to help take your <BR> mind off of all the bad-dude stuff in the world, this year your friends at Reclinathon are showcasing some of the very best examples of humanity at its very worst: Apocalypse films!  <BR> You think you've got problems?  Well, not nearly as bad as these people.  Because, sure, the stock market may be crashing...but at least it isn't crashing into a GIANT KILLER ASTEROID!!!!!  <BR><BR>  So, while it may very well be The End of the World (as we know it)...*I* feel fine...and after watching some of these movies, we hope you will, too!<BR><BR>";
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
		
        echo "<BR>Total Run Time:  " . $this->FormatRunTime($TotalRunTime) . "<BR>";

        $query = $this->GetConnection()->prepare("SELECT * FROM GENRE");
        $result = $this->query($query);
        while ($row = $result->fetch_assoc())
        {
            $genreRunTime = $GenreRunTimes[$row["GenreID"]];
            if($genreRunTime > 0)
            {
                echo "<BR>" . $row["Name"];
                if ($row["Canonical"] == 1)
                {
                    echo "*";
                }
                echo ":  " . $this->FormatRunTime($genreRunTime);
            }   
        }

    }

    private function FormatRunTime($runTimeMinutes)
    {
        $hours = (int)($runTimeMinutes / 60);
        $minutes = $runTimeMinutes % 60;
        $result = "";
        if($hours > 0)
        {
            $result .= $hours . "h";
        }
        if($minutes > 0)
        {
            if(strlen($result) > 0)
            {
                $result .= " ";
            }
            $result .= $minutes . "m";
        }
        return $result;
    }

    public function DisplayForm()
    {
        echo "<FORM ACTION='processform.php' METHOD='post'>";
        echo "<TABLE>";
        echo "<TR><TD>Title</TD><TD><INPUT TYPE='text' NAME='Title' VALUE='" . $this->Title ."'></TD></TR>";
        echo "<TR><TD>RunTime</TD><TD><INPUT TYPE='RunTime' NAME='RunTime' VALUE='" . $this->RunTime ."'></TD></TR>";
        echo "<TR><TD>Genre</TD><TD>(check all that apply)</TD></TR>";

        $query = $this->GetConnection()->prepare("SELECT * FROM GENRE");
        $result = $this->query($query);
        while($row = $result->fetch_assoc())
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
        $query = $this->GetConnection()->prepare("SELECT MovieID, Title FROM MOVIE ORDER BY Title");
        $result = $this->query($query);
        if (!$result)
        {
            return false;
        }

        echo "<SELECT NAME='MovieID'>";

        while($row = $result->fetch_assoc())
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
        $query = $this->GetConnection()->prepare(
            "INSERT INTO MOVIE (Title, RunTime, TrailerLink, IMDBLink, Freshness, Image, Metascore, Director, Year, Synopsis, Url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $query->bind_param(
            'sissisisiss',
            $this->Title, $this->RunTime, $this->TrailerLink, $this->IMDBLink, $this->Freshness, $this->Image,
            $this->Metascore, $this->Director, $this->Year, $this->Synopsis, $this->Url
        );

        //echo $query . "<BR>";
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        $query = $this->GetConnection()->prepare("SELECT LAST_INSERT_ID() AS MovieID");
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }
        $row = $result->fetch_assoc();
        $this->MovieID = $row["MovieID"]; 

        $query2 = $this->GetConnection()->prepare("INSERT INTO MOVIE_LIST (Name, MovieID) VALUES ('Ballot', ?)");
        $query2->bind_param('i', $this->MovieID);
        $result2 = $this->Query($query2);
        if (!$result)
        {
            return FALSE;
        }
        
        return $this->Update();
    }

    public function Update()
    {
        $query = $this->GetConnection()->prepare(
            "UPDATE MOVIE SET Title = ?, RunTime = ?, TrailerLink = ?, IMDBLink = ?, Freshness = ?, Image = ?, Metascore = ?, Director = ?, Year = ?, Synopsis = ?, Url = ? WHERE MovieID = ?"
        );
        $query->bind_param(
            'sissssisissi',
            $this->Title, $this->RunTime, $this->TrailerLink, $this->IMDBLink, $this->Freshness, $this->Image,
            $this->Metascore, $this->Director, $this->Year, $this->Synopsis, $this->Url, $this->MovieID
        );

        //echo "<BR>$query<BR>";
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        $query = $this->GetConnection()->prepare("DELETE FROM MOVIE_GENRE WHERE MovieID = ?");
        $query->bind_param('i', $this->MovieID);
        $result = $this->Query($query);
        if(!$result)
        {
            return FALSE;
        }

        $query = $this->GetConnection()->prepare("SELECT * FROM GENRE");
        $result = $this->query($query);
        while($row = $result->fetch_assoc())
        {
            for ($i = 0; $i < $this->NumGenres; $i++)
            {
                if ($this->Genres[$i]->GenreID == $row["GenreID"])
                {
                    $query2 = $this->GetConnection()->prepare(
                        "INSERT INTO MOVIE_GENRE (MovieID, GenreID) VALUES (?, ?)"
                    );
                    $query2->bind_param('ii', $this->MovieID, $this->Genres[$i]->GenreID);
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

        $query = $this->GetConnection()->prepare("DELETE FROM ACTORS WHERE MovieID = ?");
        $query->bind_param('i', $this->MovieID);
        $result = $this->Query($query);
        if(!$result)
        {
            return FALSE;
        }

        for ($i = 0; $i < $this->NumActors; $i++)
        {
            $query2 = $this->GetConnection()->prepare(
                "INSERT INTO ACTORS (Name, MovieID) VALUES (?, ?)"
            );
            $query2->bind_param('si', $this->Actors[$i], $this->MovieID);
            //echo $query2 . "<BR>";
            $result2 = $this->Query($query2);
            if (!$result2)
            {
                return FALSE;
            }
        }
        
        return TRUE;
    }
	
	public function UpdateUrl()
    {
		$movieId = $this->MovieID;
		$url = $this->Url;
        $query = $this->GetConnection()->prepare("UPDATE MOVIE SET Url = ? WHERE MovieID = ?");
        $query->bind_param('si', $url, $movieId);

        //echo "<BR>$query<BR>";
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }
        
        return TRUE;
    }
	
	public function UpdateRuntime()
    {
		$movieId = $this->MovieID;
		$runtime = $this->RunTime;
        $query = $this->GetConnection()->prepare("UPDATE MOVIE SET RunTime = ? WHERE MovieID = ?");
        $query->bind_param('ii', $runtime, $movieId);

        //echo "<BR>$query<BR>";
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
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
        $query = $this->GetConnection()->prepare("SELECT MovieID, Freshness FROM MOVIE");
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        //assign tickets based on freshness
        while($row = $result->fetch_assoc())
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
	
	public function SetMovieId($id)
	{
		$this->MovieID = $id;
	}
	
	public function GetUrl()
	{
		return $this->Url;
	}
	
	public function SetUrl($url)
	{
		$this->Url = $url;
	}
	
	public function SetRuntime($runtime)
	{
		$this->RunTime = $runtime;
	}
	
	public function GetImage()
	{
		return $this->Image;
	}
	
	public function CreateReclinathonInsert()
    {
        $query = $this->GetConnection()->prepare(
            "INSERT INTO MOVIE (Title, RunTime, TrailerLink, IMDBLink, Freshness, Image, Metascore, Director, Year, Synopsis, Url) VALUES ("
        );
        $query->bind_param(
            'sissisisiss',
            $this->Title, $this->RunTime, $this->TrailerLink, $this->IMDBLink, $this->Freshness, $this->Image,
            $this->Metascore, $this->Director, $this->Year, $this->Synopsis, $this->Url
        );

        //echo "<BR>$query<BR>";
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        $query = $this->GetConnection()->prepare("SELECT LAST_INSERT_ID() AS MovieID");
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }
        $row = $result->fetch_assoc();
        $this->MovieID = $row["MovieID"]; 
        
        return $this->Update();
    }

}

?>