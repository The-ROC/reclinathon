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
   
    function __construct() 
    {
        $this->Title = 'TBA';
        $this->RunTime = 0;
        $this->NumGenres = 0;
        $this->TrailerLink = '#';
        $this->IMDBLink = '#';
        $this->Freshness = 0;
        $this->Image = 'unknown';
    }

    public function Load($MovieID)
    {
        $query = $this->GetConnection()->prepare(
            "SELECT * FROM MOVIE WHERE MovieID = ?"
        );
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
            $this->Genres[$this->NumGenres] = new GENRE($row["GenreID"], $row["Name"], $row["Canonical"]);
            $this->NumGenres++;
        }

        return TRUE;
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

    public function GetRunTime()
    {
        return $this->RunTime;
    }

    public function GetID()
    {
        return $this->MovieID;
    }

    public function DisplayImage()
    {
	return "<IMG BORDER='3' WIDTH='200' SRC = '" . $this->Image . "' alt = '$this->Title' >";
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
        echo "<TR cellspacing='0'><TH>";
        //echo "<A HREF = 'insert.php?class=MOVIE&ObjectID=" . $this->MovieID . "'>";
        echo $this->Title;
        //echo "</A>";
        echo "</TH><TH CLASS='right'>" . $this->Freshness . "% fresh <INPUT TYPE='checkbox' NAME='vote" . $this->MovieID . "' VALUE='movie" . $this->MovieID . "' onclick='ToggleDisplay(this)'></TH></TR>";
        echo "<TR ID='movie" . $this->MovieID . "'><TD>";
        echo $this->DisplayImage();
        echo "</TD><TD>";
        echo "<B><U><FONT SIZE='+2'>Genre(s):</FONT></U></B>" . $this->DisplayGenres() . "<BR><BR><B><U><FONT SIZE='+2'>Runtime:</FONT></U></B><BR>" . $this->RunTime . " min";
        echo "<BR><BR><BR><A HREF='" . $this->IMDBLink . "' target='_blank'>IMDB</A><BR><A HREF = '" . $this->TrailerLink . "' target='_blank'>Trailer</A></TD></TR>";
        echo "</TABLE>";
    }

    public function DumpMovies($season)
    {
        $query = $this->GetConnection()->prepare(
            "SELECT DISTINCT MovieID FROM MOVIE_LIST WHERE NAME = ?"
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
            $movie->Load($row["MovieID"]);
            $movie->DumpXml();
        }
        print "</MovieList>";
    }

    public function DisplayAll()
    {
        $TotalRunTime = 0;
        $GenreRunTimes;

        $query = $this->GetConnection()->prepare(
            "SELECT MovieID FROM MOVIE ORDER BY Freshness DESC"
        );
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        echo "<INPUT TYPE='button' VALUE='Hide Details' onclick='HideAll()'><INPUT TYPE='button' VALUE='Show Details' onclick='ShowAll()'><BR>";
        while($row =  $result->fetch_assoc())
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
        $query = $this->GetConnection()->prepare(
            "SELECT MovieID, Title FROM MOVIE ORDER BY Title"
        );
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
            "INSERT INTO MOVIE (Title, RunTime, TrailerLink, IMDBLink, Freshness, Image) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $query->bind_param(
            'sissis', 
            $this->Title, $this->RunTime, $this->TrailerLink, 
            $this->IMDBLink, $this->Freshness, $this->Image
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
        
        return $this->Update();
    }

    public function Update()
    {
        $query = $this->GetConnection()->prepare(
            "UPDATE MOVIE SET Title = ?, RunTime = ?, TrailerLink = ?, IMDBLink = ?, Freshness = ?, Image = ? WHERE MovieID = ?"
        );
        $query->bind_param(
            'sissisi',
            $this->Title, $this->RunTime, $this->TrailerLink,
            $this->IMDBLink, $this->Freshness, $this->Image, $this->MovieID
        );

        //echo $query . "<BR>";
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        $query = $this->GetConnection()->prepare(
            "DELETE FROM MOVIE_GENRE WHERE MovieID = ?"
        );
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

}

?>