<?php

include 'RTTHeader.php';

$DEFAULT_LOGO = "images/DefaultLogo.png";

class RECLINATHON_CONTEXT extends RTT_COMMON
{
    protected $ContextID;		// INT
    protected $TimeStamp; 		// INT	    	
    protected $EstimatedDuration; 	// INT
    protected $Captain;		// RECLINEE
    protected $RecliningState;        	// RECLINING_STATE
    protected $RecliningStateModifier;	// STATE_MODIFIER
    protected $Movie;                  		// MOVIE
    protected $ReclineeList;           	// RECLINEE_LIST
    protected $OptionalInfo;           	// OPTIONAL_CONTEXT
    protected $Season;                    	// STRING
    protected $Logo;                      	// STRING
	protected $Pending;
   
    function __construct() 
    {
        $this->ContextID = 0;
        $this->TimeStamp = 0;
        $this->EstimatedDuration = 300;
        $this->Captain = new RECLINEE();
        $this->RecliningState = 0;
        $this->RecliningStateModifier = 0;
        $this->Movie = new MOVIE();
        $this->ReclineeList = new RECLINEE_LIST();
        $this->OptionalInfo = new OPTIONAL_CONTEXT();
        $this->Season = '';
        $this->Logo = 0;
		$this->Pending = 0;
    }
	
	public function GetContextId()
	{
		return $this->ContextID;
	}
	
	public function GetRecliningState()
	{
		return $this->RecliningState;
	}

    public function DisplayState()
    {
        $StateString = "";
        if ($this->RecliningState == "")
        {
            $StateString .= "Unknown";
        }
        else
        {
            $StateString .= $this->RecliningState;
        }

        if ($this->RecliningStateModifier != "")
        {
            $StateString .= ":" . $this->RecliningStateModifier;
        }

        return $StateString;
            
    }

    public function HasMovie()
    {
        if ($this->RecliningState == 'Reclining' ||
            $this->RecliningState == 'Downtime'  ||
            ($this->RecliningState == 'Preseason' && $this->RecliningStateModifier == 'Final Countdown'))
        {
            return ($this->Movie->GetID() != 0);
        }
        else
        {
            return false;
        }
    }

    public function Playable()
    {
        return ($this->RecliningState == 'Downtime' ||
                $this->RecliningState == 'Emergency Maintenance' ||
                ($this->RecliningState == 'Preseason' && $this->RecliningStateModifier == 'Final Countdown'));
    }

    public function Pauseable()
    {
        return ($this->RecliningState == 'Reclining');
    }

    public function Stoppable()
    {
        return ($this->RecliningState == 'Reclining');
    }

    private function DisplayTime()
    {
        return date("m/d/Y g:i A", $this->TimeStamp);
    }

    private function SetTimeRemaining($duration)
    {
        $currenttime = time();
        $this->EstimatedDuration = $currenttime - $this->TimeStamp + $duration;
        return true;
    }

    public function GetTimeRemaining()
    {
        $currenttime = time();
        $duration = $this->TimeStamp + $this->EstimatedDuration - $currenttime;  

        if($duration < 0) 
        {
            $duration = 0;
        } 

        //echo $currenttime . '<BR>';
        return $duration;
    }

    private function IncrementTimeRemaining($duration)
    {
        if ($this->GetTimeRemaining() == 0)
        {
            $this->SetTimeRemaining($duration);
        }
        else
        {
            $this->EstimatedDuration += $duration;
        }
    }

    public function Load($ContextID)
    {
        $query = $this->GetConnection()->prepare(
            "SELECT * FROM RECLINATHON_CONTEXT rc LEFT JOIN RECLINING_STATE rs ON rc.StateID = rs.StateID LEFT JOIN STATE_MODIFIER sm ON rc.ModifierID = sm.ModifierID LEFT JOIN LOGO lg ON rc.LogoID = lg.LogoID WHERE ContextID = ?"
        );
        $query->bind_param('i', $ContextID);
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

        $this->ContextID = $row["ContextID"];
        $this->TimeStamp = $row["TimeStamp"];
        $this->EstimatedDuration = $row["EstimatedDuration"];
        $this->Captain->Load($row["CaptainID"]);
        $this->RecliningState = $row["State"];
        $this->RecliningStateModifier = $row["Modifier"];
        $this->Movie->Load($row["MovieID"]);
        $this->ReclineeList->Load($row["ContextID"]);
        $this->OptionalInfo->Load($ContextID);
        $this->Season = $row["Season"];
        if ($row["Logo"] == '')
        {
            $this->Logo = 'images/DefaultLogo.png';
        }
        else
        {
            $this->Logo = $row["Logo"];
        }

        return TRUE;
    }

    public function LoadFromForm()
    {
        $this->ContextID = $_POST["ObjectID"];
        $this->TimeStamp = $_POST["TimeStamp"];
        if ($_POST["EstimatedDuration"] != '')
        {
            $this->SetTimeRemaining($_POST["EstimatedDuration"]);
        }
        $this->Captain->Load($_POST["ReclineeID"]);
        $this->RecliningState = $_POST["RecliningState"];
        $this->RecliningStateModifier = $_POST["RecliningStateModifier"];
        $this->Movie->Load($_POST["MovieID"]);
        $this->ReclineeList->Load($_POST["ContextID"]);
        $this->OptionalInfo->Load($_POST["ContextID"]);
        $this->Season = $this->GetCurrentSeason();

        return TRUE;
    }

    public function LoadCurrent($Season)
    {
        $query = $this->GetConnection()->prepare(
            "SELECT ContextID FROM RECLINATHON_CONTEXT WHERE TimeStamp <= ? AND Season = ? ORDER BY TimeStamp DESC"
        );
        $query->bind_param('is', date('U'), $Season);
        $result = $this->query($query);
        if (!$result)
        {
            return false;
        }
        $row = $result->fetch_assoc();
        if (!$row)
        {
            return false;
        }
        $ContextID = $row["ContextID"];
        
        return $this->Load($ContextID);
    }

    public function SwitchMovie()
    {
        $END_OF_REGULATION = time() + (26.2 * 60 * 60);
        $MovieList = new MOVIE_LIST();
        if (!$MovieList->Load($this->Season))
        {
            return false;
        }
        $NextMovie = $MovieList->GetNextMovie(time(), $END_OF_REGULATION);

        $MovieList->TogglePlayedUnplayed(0, $this->Movie->GetID(), $this->Season);
        $this->Movie = $NextMovie;
        $MovieList->TogglePlayedUnplayed(1, $this->Movie->GetID(), $this->Season);

        return true;
    }

    public function TransitionState($Action)
    {
        $END_OF_REGULATION = 1261998720;
        $MovieList = new MOVIE_LIST();
        if (!$MovieList->Load($this->GetCurrentSeason()))
        {
            return false;
        }
        $NextMovie = $MovieList->GetNextMovie(time(), $END_OF_REGULATION);

        $TRANSITION_TABLE["Reclining"]["Pause"] = array("State" => "Emergency Maintenance", "Modifier" => "", "Movie" => $this->Movie, "Duration" => "600");
        $TRANSITION_TABLE["Reclining"]["Stop"] = array("State" => "Downtime", "Modifier" => "", "Movie" => $NextMovie, "Duration" => "600");
        $TRANSITION_TABLE["Downtime"]["Play"] = array("State" => "Reclining", "Modifier" => "", "Movie" => $this->Movie, "Duration" => ($this->Movie->GetRunTime() * 60));
        $TRANSITION_TABLE["Emergency Maintenance"]["Play"] = array("State" => "Reclining", "Modifier" => "", "Movie" => $this->Movie, "Duration" => $this->GetTimeRemaining());
        $TRANSITION_TABLE["Preseason"]["Play"] = array("State" => "Reclining", "Modifier" => "", "Movie" => $this->Movie, "Duration" => ($this->Movie->GetRunTime() * 60));

        $TransitionArray = $TRANSITION_TABLE[$this->RecliningState][$Action];
        if ($TransitionArray == '')
        {
            return false;
        }

        $this->TimeStamp = time();
        $this->RecliningState = $TransitionArray["State"];
        $this->RecliningStateModifier = $TransitionArray["Modifier"];
        $this->Movie = $TransitionArray["Movie"];
        $this->EstimatedDuration = $TransitionArray["Duration"];

        return true;
    }


    public function DisplayStateSelectList()
    {
        $query = $this->GetConnection()->prepare("SELECT StateID, State FROM RECLINING_STATE ORDER BY State");
        $result = $this->query($query);
        if (!$result)
        {
            return false;
        }

        echo "<SELECT NAME='RecliningState'>";

        while($row = $result->fetch_assoc())
        {
            echo "<OPTION VALUE='" . $row["State"] . "'";
            if ($this->RecliningState == $row["State"])
           {
                echo " SELECTED";
            }
            echo ">" . $row["State"] . "</OPTION>";
        }

        echo "</SELECT>";

        return true;
    }

    public function DisplayStateModifierSelectList()
    {
        $query = $this->GetConnection()->prepare(
            "SELECT ModifierID, Modifier FROM STATE_MODIFIER ORDER BY Modifier"
        );
        $result = $this->query($query);
        if (!$result)
        {
            return false;
        }

        echo "<SELECT NAME='RecliningStateModifier'>";
        echo "<OPTION VALUE=''> </OPTION>";

        while($row = $result->fetch_assoc())
        {
            echo "<OPTION VALUE='" . $row["Modifier"] . "'";
            if ($this->RecliningStateModifier == $row["Modifier"])
           {
                echo " SELECTED";
            }
            echo ">" . $row["Modifier"] . "</OPTION>";
        }

        echo "</SELECT>";

        return true;
    }

    public function DisplayTitleLogo()
    {
        echo "<DIV CLASS='title'><CENTER><IMG SRC='" . $this->Logo . "'></CENTER></DIV>";
    }

    public function DisplayModule()
    {
        echo "<FORM NAME='sw'>";
        echo "<TABLE>";
        echo "<TR cellspacing='0'><TH>";
        //echo "<A HREF='insert.php?class=RECLINATHON_CONTEXT&ObjectID=" . $this->ContextID . "'>";
        echo $this->DisplayState();
        //echo "</A>";
        echo "</TH><TH CLASS='right'>" . $this->DisplayTime() . "</TH></TR>";
        if ($this->HasMovie())
        {
            echo "<TR><TD>Movie:</TD><TD>" . $this->Movie . "</TD></TR>";
        }
        echo "<TR><TD>Time Remaining:*</TD><TD><input type='text' name='disp2' size='18' style=\"border: 0px; background-color: #C6D9F1; font-size: 125%;\" readonly></INPUT></TD></TR>";
        echo "<TR><TD>Captain:</TD><TD>" . $this->Captain . "</TD></TR>";
        $this->OptionalInfo->DisplayOptionalRows();
        echo "</TABLE>";
        echo "</FORM>";
    }

    public function DisplayCaptainModule()
    {
        $MovieList = new MOVIE_LIST();
        if (!$MovieList->Load($this->GetCurrentSeason()))
        {
            return false;
        }

        echo "<FORM NAME='sw' ACTION='processform.php' METHOD='post'>";
        if ($this->Playable())
        {
            echo "<INPUT TYPE='submit' NAME='InputButton' VALUE='Play'>";
        }
        if ($this->Pauseable())
        {
            echo "<INPUT TYPE='submit' NAME='InputButton' VALUE='Pause'>";
        }
        if ($this->Stoppable())
        {
            echo "<INPUT TYPE='submit' NAME='InputButton' VALUE='Stop'>";
        }
        echo "<INPUT TYPE='submit' NAME='InputButton' VALUE='Update'>";
        echo "<BR>";
        echo "<TABLE>";
        echo "<TR cellspacing='0'><TH>";
        $this->DisplayStateSelectList();
        echo ":<BR>";
        $this->DisplayStateModifierSelectList();
        echo "</TH><TH CLASS='right'>" . $this->DisplayTime() . "</TH></TR>";
        echo "<TR><TD valign='top'>Movie:</TD><TD>";
        $MovieList->DisplaySelectList($this->Movie->GetID());
        echo "<BR><INPUT TYPE='submit' NAME='InputButton' VALUE='Pick New Movie'><BR><BR></TD></TR>";
        echo "<TR><TD valign='top'>Time Remaining:</TD><TD><input type='text' name='disp2' size='18' style=\"border: 0px; background-color: #C6D9F1; font-size: 125%;\" readonly></INPUT><BR><INPUT TYPE='text' NAME='EstimatedDuration'></INPUT> seconds<BR><INPUT TYPE='submit' NAME='InputButton' VALUE='+1'><INPUT TYPE='submit' NAME='InputButton' VALUE='+5'><INPUT TYPE='submit' NAME='InputButton' VALUE='+10'> minutes<BR><BR></TD></TR>";
        echo "<TR><TD valign='top'>Captain:</TD><TD>";
        $this->Captain->DisplaySelectList();
        echo "</TD></TR>";
        $this->OptionalInfo->DisplayOptionalRows();
        echo "</TABLE>";
        echo "<INPUT TYPE='hidden' NAME='class' VALUE='RECLINATHON_CONTEXT'><INPUT TYPE='hidden' NAME='ObjectID' VALUE='" . $this->ContextID . "'><INPUT TYPE='hidden' NAME='TimeStamp' VALUE='" . $this->TimeStamp . "'>";
        echo "</FORM>";
    }

    public function DisplayHistoryModuleInternal($page)
    {
        echo "<TABLE><TR><TH>History</TH><TH CLASS='right'><A HREF = '" . $page . "'>Go To Current</A></TH></TR>";
        
        /*
        $query = "SELECT TimeStamp FROM RECLINATHON_CONTEXT WHERE TimeStamp > '" . $this->TimeStamp . "' AND TimeStamp < UNIX_TIMESTAMP() AND Season = '" . $this->Season . "' ORDER BY TimeStamp LIMIT 2";
        $result = $this->query($query);
        $MaxTimeStamp = $this->TimeStamp;
        while($row = mysql_fetch_assoc($result))
        {
            $MaxTimeStamp = $row["TimeStamp"];
        }
        $query = "SELECT ContextID FROM RECLINATHON_CONTEXT WHERE TimeStamp <= '" . $MaxTimeStamp . "' AND Season = '" . $this->Season . "' ORDER BY TimeStamp DESC LIMIT 5"; 
        */

        $query = $this->GetConnection()->prepare(
            "SELECT ContextID FROM RECLINATHON_CONTEXT WHERE TimeStamp <= UNIX_TIMESTAMP() AND Season = ? ORDER BY TimeStamp DESC"
        );
        $query->bind_param('s', $this->Season);
        $result = $this->query($query);
        while($row = $result->fetch_assoc())
        {
            $rcx = new RECLINATHON_CONTEXT();
            $rcx->Load($row["ContextID"]);
            echo "<TR><TD><A HREF = '" . $page . "?ContextID=" . $rcx->ContextID . "'>";
            if ($rcx->RecliningState == 'Reclining')
            {
                echo $rcx->Movie;
            }
            else
            {
                echo $rcx->DisplayState();
            }
            echo "</A></TD><TD align='right'>" . $rcx->DisplayTime() . "</TD></TR>";
        }
        
        echo "</TABLE>";
 
        return true;
    }

    public function DisplayCaptainHistoryModule()
    {
        return $this->DisplayHistoryModuleInternal("captain.php");
    }

    public function DisplayHistoryModule()
    {
        return $this->DisplayHistoryModuleInternal("index.php");
    }
	
	public function DisplayCaptainDowntimeModule()
	{
		echo "<TABLE>";
        echo "<TR cellspacing='0'><TH colspan='2'>Downtime Entertainment Manager</TH></TR>";
		
		$query = $this->GetConnection()->prepare(
            "SELECT * FROM VideoClips WHERE Played = ? ORDER BY Ordering"
        );
        $query->bind_param('i', $this->ContextID);
        $result = $this->query($query);
		
		while ($row = $result->fetch_assoc())
        {
			$clipId = $row["VCID"];
            $clipCaption = $row["Caption"];
            echo "<TR ID='ClipCaption$i'><TD width='10px'><BUTTON onclick='RemoveClip($clipId);'>x</BUTTON></TD><TD width='90%'>$clipCaption</TD></TR>";
        }
		
		echo "<TR><TD><SELECT id='videoClipToAdd' name='videoClipToAdd' style='width: 100%'>";
		
		$query = $this->GetConnection()->prepare(
            "SELECT * FROM VideoClips WHERE Played != ? ORDER BY Caption"
        );
        $query->bind_param('i', $this->ContextID);
        $result = $this->query($query);
		
		while ($row = mysql_fetch_assoc($result))
        {
			$clipId = $row["VCID"];
			$clipCaption = $row["Caption"];
			echo "<OPTION value='$clipId'>$clipCaption</OPTION>";
		}
		
		echo "</TD><TD><BUTTON onclick='AddClip($this->ContextID);'>add</BUTTON></TD></TR>";
		echo "</TABLE>";
	}

    public function DisplayDowntimeModule($ShowDowntime)
    {
        echo "<TABLE>";
        echo "<TR cellspacing='0'><TH>Entertainment</TH></TR>";
		
        $query = $this->GetConnection()->prepare(
            "SELECT * FROM VideoClips WHERE Played = ?"
        );
        $query->bind_param('i', $this->ContextID);
        $result = $this->query($query);
		$numClips = $result->num_rows;
		
		echo "<INPUT type='hidden' name='numClips' id='numClips' value='$numClips' />";
		
		$i = 0;
        while ($row = $result->fetch_assoc())
        {
			$clipUrl = $row["URL"];
            $clipCaption = $row["Caption"];
            echo "<TR ID='ClipUrl$i'><TD>$clipUrl</TD></TR>";
            echo "<TR ID='ClipCaption$i'><TD>$clipCaption</TD></TR>";
			$i++;
        }

        echo "<TR><TD ID='TriviaQuestion'></TD></TR>";
        echo "<TR><TD ID='TriviaAnswer'></TD></TR>";
		
		if ($numClips > 0)
		{
			echo "<TR><TD><BUTTON onclick='PreviousEntertainmentItem()'>&larr;</BUTTON><BUTTON onclick='NextEntertainmentItem()'>&rarr;</BUTTON></TD></TR>";
		}
        echo "</TABLE>";
    }

	public function GetRandomTriviaItem()
	{
		$query = $this->GetConnection()->prepare("SELECT MAX(TID) AS LargestID FROM Trivia");
		$result = $this->query($query);
		$row = $result->fetch_assoc();
		$RandID = mt_rand(1, $row["LargestID"]);
		$query = $this->GetConnection()->prepare(
            "SELECT * FROM Trivia WHERE TID >= ? ORDER BY TID LIMIT 1"
        );
        $query->bind_param('i', $RandID);
		$result = $this->query($query);
		$row = $result->fetch_assoc();
		$question = htmlspecialchars($row["Question"], ENT_QUOTES | ENT_XML1);
		$answer = htmlspecialchars($row["Answer"], ENT_QUOTES | ENT_XML1);
		
		echo "<trivia question='$question' answer='$answer' />";
	}

    public function GetMovie()
    {
        return $this->Movie;
    }

    public function DisplayForm()
    {
        echo "<FORM ACTION='processform.php' METHOD='post'>";
        echo "<TABLE>";
        echo "<TR><TD>Timestamp (Blank = current time)</TD><TD><INPUT TYPE='text' NAME='TimeStamp' VALUE='" . $this->TimeStamp ."'></TD></TR>";
        echo "<TR><TD>Estimated Duration</TD><TD><INPUT TYPE='text' NAME='EstimatedDuration' VALUE='" . $this->EstimatedDuration ."'></TD></TR>";
        echo "<TR><TD>Captain</TD><TD>";
            $this->Captain->DisplaySelectList();
            echo "</TD></TR>";
        echo "<TR><TD>Reclinathon State</TD><TD><INPUT TYPE='text' NAME='RecliningState' VALUE='" . $this->RecliningState ."'></TD></TR>";
        echo "<TR><TD>State Modifier</TD><TD><INPUT TYPE='text' NAME='RecliningStateModifier' VALUE='" . $this->RecliningStateModifier ."'></TD></TR>";
        echo "<TR><TD>Movie</TD><TD>";
            $this->Movie->DisplaySelectList();
            echo "</TD></TR>";
        echo"<TR><TD> </TD><TD><INPUT TYPE='hidden' NAME='class' VALUE='RECLINATHON_CONTEXT'><INPUT TYPE='hidden' NAME='ObjectID' VALUE='" . $this->ContextID . "'><INPUT  TYPE='submit' NAME='InputButton' VALUE='Submit'></TD></TR>";
        echo "</TABLE>";
        echo "</FORM>";
    }

    public function DisplayAll()
    {
        $query = $this->GetConnection()->prepare("SELECT * FROM RECLINATHON_CONTEXT ORDER BY TimeStamp DESC");
        $result = $this->query($query);

        while($row = $result->fetch_assoc())
        {
            $context = new RECLINATHON_CONTEXT();
            $context->Load($row["ContextID"]);
            $context->DisplayModule();
        }
    }  

    public function DumpSeason($season)
    {
        $query = $this->GetConnection()->prepare(
            "SELECT * FROM RECLINATHON_CONTEXT WHERE Season = ? ORDER BY TimeStamp"
        );
        $query->bind_param('s', $season);
        $result = $this->query($query);

        print "<ContextList>";
        while($row = $result->fetch_assoc())
        {
            $context = new RECLINATHON_CONTEXT();
            $context->Load($row["ContextID"]);
            $context->DumpXml();
        }
        print "</ContextList>";
    }
    
	public function GetContextListBySeason($season)
    {
        $query = $this->GetConnection()->prepare(
            "SELECT * FROM RECLINATHON_CONTEXT WHERE Season = ? ORDER BY TimeStamp"
        );
        $query->bind_param('s', $season);
        $result = $this->query($query);

		$ContextList = array();
        while($row = mysql_fetch_assoc($result))
        {
            $context = new RECLINATHON_CONTEXT();
            $context->Load($row["ContextID"]);
            $ContextList[] = $context;
        }
		
		return $ContextList;
    }
	
	public function GetMovieListBySeason($season)
    {
        $query = $this->GetConnection()->prepare(
            "SELECT * FROM RECLINATHON_CONTEXT WHERE Season = ? ORDER BY TimeStamp"
        );
        $query->bind_param('s', $season);
        $result = $this->query($query);

		$MovieList = array();
        while($row = $result->fetch_assoc())
        {
            $context = new RECLINATHON_CONTEXT();
            $context->Load($row["ContextID"]);
			
			if ($context->RecliningState == "Reclining")
			{
				$title = $context->Movie->GetTitle();
				if (!array_key_exists($title, $MovieList))
				{
					$MovieList[$title] = $context->Movie;
				}
			}
        }
		
		return $MovieList;
    }
	
    public function ShowRecliningRatio($season)
    {
        echo "<RecliningRatios>";
        $ContextList = $this->GetContextListBySeason($season);
        $Preseason = true;
        $PreviousRecliningState = "";
        $PreviousTimeStamp = 0;
        $TimeReclined = 0;
        $TotalTime = 0;
		
        foreach ($ContextList as $i => $context) 
        {
            if ($Preseason)
            {
                if ($context->RecliningState == "Reclining")
                {
                    $Preseason = false;
                    $PreviousTimeStamp = $context->TimeStamp;
                    $PreviousRecliningState = $context->RecliningState;
                }
				
                continue;
            }
			
            $TimeElapsed = $context->TimeStamp - $PreviousTimeStamp;
			
            if ("Reclining" == $PreviousRecliningState)
            {
                $TimeReclined += $TimeElapsed;
            }
			
            $TotalTime += $TimeElapsed;
			
            $PreviousTimeStamp = $context->TimeStamp;
            $PreviousRecliningState = $context->RecliningState;
			
            $RecliningRatio = $TimeReclined / $TotalTime;
            echo "<RecliningRatio timestamp='$TotalTime' ratio='$RecliningRatio' />";

            if ($context->RecliningState == "Postseason")
            {
                break;
            }
        }

        echo "</RecliningRatios>";
    }
	
	public function GetSeasons()
    {
        echo "<Seasons>";
        
		$query = $this->GetConnection()->prepare(
            "SELECT DISTINCT Season FROM RECLINATHON_CONTEXT ORDER BY Season DESC"
        );
        $result = $this->query($query);

        while($row = $result->fetch_assoc())
        {
		    $season = $row["Season"];
		    echo "<Season name='$season' />";
        }
		
        echo "</Seasons>";
    }

    public function Insert()
    {
        $StateID = 0;
        $query = $this->GetConnection()->prepare("SELECT StateID FROM RECLINING_STATE WHERE State = ?");
        $query->bind_param('s', $this->RecliningState);
        $result = $this->query($query);
        if (!$result)
        {
            return false;
        }
        if ($row = $result->fetch_assoc())
        {
            $StateID = $row["StateID"];
        }
        else
        {
            $queryString = "INSERT INTO RECLINING_STATE (State) VALUES ('" . $this->RecliningState . "')";
            $query = $this->GetConnection()->prepare("INSERT INTO RECLINING_STATE (State) VALUES (?)");
            $query->bind_param('s', $this->RecliningState);
            echo $queryString . "<BR>";
        }

        $ModifierID = 0;
        $query = $this->GetConnection()->prepare("SELECT ModifierID FROM STATE_MODIFIER WHERE Modifier = ?");
        $query->bind_param('s', $this->RecliningStateModifier);
        $result = $this->query($query);
        if (!$result)
        {
            return false;
        }
        if ($row = $result->fetch_assoc())
        {
            $ModifierID = $row["ModifierID"];
        }
        else
        {
            if ($this->RecliningStateModifier != "")
            {
                $queryString = "INSERT INTO STATE_MODIFIER (Modifier) VALUES ('" . $this->RecliningStateModifier . "')";
                $query = $this->GetConnection()->prepare(
                    "INSERT INTO STATE_MODIFIER (Modifier) VALUES (?)"
                );
                $query->bind_param('s', $this->RecliningStateModifier);
                echo $queryString . "<BR>";
            }
        }
        
        // Building querystring purely for logging/debugging purposes
        // See prepared statement below
        $query = "INSERT INTO RECLINATHON_CONTEXT (TimeStamp, EstimatedDuration, CaptainID, StateID, ModifierID, MovieID, Season, LogoID, Pending) VALUES (";
        $query = $query . "'" . $this->TimeStamp . "', ";
        $query = $query . "'" . $this->EstimatedDuration . "', ";
        $query = $query . "'" . $this->Captain->GetID() . "', ";
        $query = $query . "'" . $StateID . "', ";
        $query = $query . "'" . $ModifierID . "', ";
        $query = $query . "'" . $this->Movie->GetID() . "', ";
        $query = $query . "'" . $this->Season . "', ";
        $query = $query . "'0', ";
		$query = $query . "'" . $this->Pending . "')";

        echo $query . "<BR>";
        $query = $this->GetConnection()->prepare(
            "INSERT INTO RECLINATHON_CONTEXT (TimeStamp, EstimatedDuration, CaptainID, StateID, ModifierID, MovieID, Season, LogoID, Pending) VALUES (?, ?, ?, ?, ?, ?, ?, '0', ?)"
        );
        $query->bind_param(
            'iiiiiisi', 
            $this->TimeStamp, $this->EstimatedDuration, $this->Captain->GetID(), $StateID, 
            $ModifierID, $this->Movie->GetID(), $this->Season, $this->Pending
        );
        $result = $this->Query($query);
        if (!$result)
        {
          return FALSE;
        }
        
        return TRUE;
    }

    public function Update()
    {
        $StateID = 0;
        $query = $this->GetConnection()->prepare("SELECT StateID FROM RECLINING_STATE WHERE State = ?");
        $query->bind_param('s', $this->RecliningState);
        $result = $this->query($query);
        if (!$result)
        {
            return false;
        }
        if ($row = $result->fetch_assoc())
        {
            $StateID = $row["StateID"];
        }
        else
        {
            $query = $this->GetConnection()->prepare("INSERT INTO RECLINING_STATE (State) VALUES (?)");
            $query->bind_param('s', $this->RecliningState);
            //echo $query . "<BR>";
        }

        $ModifierID = 0;
        $query = $this->GetConnection()->prepare("SELECT ModifierID FROM STATE_MODIFIER WHERE Modifier = ?");
        $query->bind_param('s', $this->RecliningStateModifier);
        $result = $this->query($query);
        if (!$result)
        {
            return false;
        }
        if ($row = $result->fetch_assoc())
        {
            $ModifierID = $row["ModifierID"];
        }
        else
        {
            $query = $this->GetConnection()->prepare("INSERT INTO STATE_MODIFIER (Modifier) VALUES (?)");
            $query->bind_param('s', $this->RecliningStateModifier);
            //echo $query . "<BR>";
        }
        
        $query = $this->GetConnection()->prepare(
            "UPDATE RECLINATHON_CONTEXT SET CaptainID = ?, EstimatedDuration = ?, StateID = ?, ModifierID = ?, MovieID = ? WHERE ContextID = ?"
        );
        $query->bind_param(
            'iiiiii', 
            $this->Captain->GetID(), $this->EstimatedDuration, $StateID, 
            $ModifierID, $this->Movie->GetID(), $this->ContextID
        );

        //echo $query . "<BR>";
        $result = $this->Query($query);
        if (!$result)
        {
          return FALSE;
        }
        
        return TRUE;
    }

    public function ProcessForm()
    {
        $mode = $_POST["InputButton"];
        $process = "Update";
        $status = false;
        echo $mode . "<BR>";
        $this->Load($_POST["ObjectID"]);

        switch($mode)
        {
            case "+1":
                $this->IncrementTimeRemaining(60);
                break;
            case "+5":
                $this->IncrementTimeRemaining(300);
                break;
            case "+10":
                $this->IncrementTimeRemaining(600);
                break;
            case "Pick New Movie":
                $this->SwitchMovie();
                break;
            case "Update":
            case "Submit":
                $this->LoadFromForm();
                break;
            case "Play":
            case "Pause":
            case "Stop":
                $process = "Insert";
                $this->TransitionState($mode);
                break;
            default:
                echo "Illegal Mode<BR>";
                return false;
        }

        //$this->Dump(0, true);

        if ($process == "Update")
        {
            $status = $this->Update();
            $URL = "captain.php?ContextID=" . $this->ContextID;
        }
        else if ($process == "Insert")
        {
            $status = $this->Insert();
            $URL = "captain.php";
        }
        
        if ($status)
        {
            echo "<meta http-equiv='refresh' content=\"0;url=" . $URL . "\" />";
        }

        return $status;
    }
	
	public function FinishProcessForm()
	{
		return;
	}

    public function ProcessAutomationCommand()
    {
        $status = false;
        $Season = $_GET["Season"];
        $Command = $_GET["Command"];

        if ($Season == "" || $Command == "")
        {
            return false;
        }

        if ($Season == "latest")
        {
            $Season = $this->GetCurrentSeason();
        }

        if ($Command != "DB_DumpMovies" && $Command != "SENSOR_GetData" && !$this->LoadCurrent($Season))
        {
            return false;
        }

        switch($Command)
        {
            case "RFID_UpdateCaptain":
                $status = $this->RFID_UpdateCaptain($Season, $_GET["Tag"]);
                break;
            case "RFID_Swipe":
                $this->RFID_Swipe($Season, $_GET["Tag"]);
                break;
            case "IR_Play":
                $this->TransitionState("Play");
                $this->Dump(true, 0);
                $status = $this->Insert();
                break;
            case "IR_Stop":
                $this->TransitionState("Stop");
                $this->Dump(true, 0);
                $status = $this->Insert();
                break;
            case "IR_Pause":
                $this->TransitionState("Pause");
                $this->Dump(true, 0);
                $status = $this->Insert();
                break;
            case "DB_DumpContexts":
                $this->DumpSeason($Season);
                break;
            case "DB_DumpMovies":
                $this->Movie->DumpMovies($Season);
                break;
            case "SENSOR_GetData":
                $query = $this->GetConnection()->prepare("SELECT * FROM SENSOR");
                $result = $this->query($query);
                $NumRows = $result->num_rows;
                echo "<SensorDataList><NumDataPoints>" . $NumRows . "</NumDataPoints>";
                while ($row = $result->fetch_row())
                {
                    echo "<SensorData><SensorName>" . $row[0] . "</SensorName><TimeStamp>" . $row[1] . "</TimeStamp><SensorValue>" . $row[2] . "</SensorValue></SensorData>";
                }
                echo "</SensorDataList>";
            default:
                break;
        }

        if ($status)
        {
            echo "<meta http-equiv='refresh' content=\"0;url=captain.php\" />";
        }
    }

    private function RFID_UpdateCaptain($Season, $Tag)
    {
        if ($Tag == "")
        {
            echo "Tag Not Specified";
            exit();
        }

        $query = $this->GetConnection()->prepare(
            "SELECT r.ReclineeID, r.FormalName FROM RECLINEE r JOIN RFID rf on rf.ReclineeID = r.ReclineeID WHERE rf.Tag = ?"
        );
        $query->bind_param('s', $Tag);
        $result = $this->query($query);
        if (!$result)
        {
            echo "SQL Error";
            return false;
        }
        $row = $result->fetch_assoc();
        if (!$row)
        {
            echo "Tag Not Found";
            return false;
        }

        echo $row["FormalName"];
        if (!$this->Captain->Load($row["ReclineeID"]))
        {
            return false;
        }

        return $this->Update();
    }

    private function RFID_Swipe($Season, $Tag)
    {
        if ($Tag == "")
        {
            echo "Tag Not Specified";
            exit();
        }

        $query = $this->GetConnection()->prepare(
            "SELECT r.ReclineeID, r.FormalName FROM RECLINEE r JOIN RFID rf on rf.ReclineeID = r.ReclineeID WHERE rf.Tag = ?"
        );
        $query->bind_param('s', $Tag);
        $result = $this->query($query);
        if (!$result)
        {
            echo "SQL Error";
            return false;
        }
        $row = $result->fetch_assoc();
        if (!$row)
        {
            echo "Tag Not Found";
            return false;
        }

        echo $row["FormalName"];

        $query = $this->GetConnection()->prepare(
            "INSERT INTO RFID_SWIPE (ReclineeID, TimeStamp, Season) VALUES (?, ?, ?)"
        );
        $query->bind_param('iis', $row["ReclineeID"], time(), $Season);
        $result = $this->query($query);
        if (!$result)
        {
            //echo $query;
            echo "SQL Insert Error";
            return false;
        }

        return true;

    }  
	
	public function SetSeason($season)
	{
		$this->Season = $season;
	}
	
	public function DisplayFeedMovieList()
	{
		$MovieList = new MOVIE_LIST();
        if (!$MovieList->Load($this->Season))
        {
            return false;
        }
		
		$MovieList->DisplayFeedImages();
	}
	
	public function DisplayFeedModule()
	{
		if ($this->RecliningState == 'Preseason')
		{
			$timeRemaining = $this->GetTimeRemaining() * 1000;
			
            echo "<div id='scheduledReclinathon' class='container' style='padding:15px 0; width:100%'>
			      <div id='nowPlayingText' class='container'><div class='content'><b>Countdown to Reclinathon!</b></div></div>
				  <div id='timeRemaining' class='container'><div id='countdown' class='content'><script>this.displayTimer($timeRemaining); this.setCountdownTimer($timeRemaining);</script></div></div>
				  <div id='timeRemaining' class='container' style='height:10px'><div class='content'></div></div>
				  <div class='main-carousel' data-flickity='{ \"cellAlign\": \"center\", \"contain\": false}' style=\"width:100%;background-image:url('film.png'); background-size: 300px 150px\">";

            $this->DisplayFeedMovieList();

            echo "</div><div id='timeRemaining' class='container' style='height:25px'><div class='content'></div></div></div>";
        }
		else if ($this->RecliningState == 'Reclining' || $this->RecliningState == 'Downtime')
		{
			$topDivId = ($this->RecliningState == 'Reclining' ? "nowPlaying" : "upNext");
			$posterDivId = $topDivId + "Poster";
			$infoDivId = $topDivId + "Info";
			$textDivId = $topDivId + "Text";
			$timeRemaining = $this->GetTimeRemaining() * 1000;
			$state = $this->DisplayState();
			$movieTitle = $this->GetMovie()->GetTitle();
			
			echo "<div id='$topDivId' class='container' style='padding:15px'>
                  <div id='$posterDivId' class='content' style='text-align:right'>";

            if ($this->HasMovie())
            {
                $this->GetMovie()->DisplayFeedImage();
            }

            echo "</div>
                  <div class='content' style='width:20px'></div>
                  <div id='$infoDivId' class='content' style='text-align:left'>
                  <div id='$textDivId' class='container' style='height:40px'><div class='content'><b>$state</b></div></div>
                  <div id='movieTitle' class='container' style='height:40px'><div class='content'>$movieTitle</div></div>
                  <div id='timeRemaining' class='container' style='height:40px'><div id='countdown' class='content'><script>this.displayTimer($timeRemaining); this.setCountdownTimer($timeRemaining);</script></div></div>
				  <div id='timeRemaining' class='container' style='height:10px'><div class='content'></div></div>
                  </div>
                  </div>";
		}
	}
	
	public function LoadCurrentNonPending($Season)
    {
        $query = $this->GetConnection()->prepare(
            "SELECT ContextID FROM RECLINATHON_CONTEXT WHERE Season = ? AND Pending='0' ORDER BY ContextID DESC"
        );
        $query->bind_param('s', $Season);
        $result = $this->query($query);
        if (!$result)
        {
            return false;
        }
        $row = $result->fetch_assoc();
        if (!$row)
        {
            return false;
        }
        $ContextID = $row["ContextID"];
        
        return $this->Load($ContextID);
    }
	
    public function Advance()
	{
		// Find the next pending context for this Reclinathon.
        $query = $this->GetConnection()->prepare(
            "SELECT ContextID, Pending FROM RECLINATHON_CONTEXT WHERE Season = ? AND ContextId > ? ORDER BY ContextID LIMIT 1"
        );
        $query->bind_param('si', $this->Season, $this->ContextID);
        $result = $this->query($query);
		if (!$result)
        {
            return false;
        }		
		
		// If there are no remaining pending contexts, the Reclinathon is over :-(
		if ($result->num_rows == 0)
		{
			// Check to see if someone has already ended the Reclinathon.
			$query = $this->GetConnection()->prepare(
                "SELECT * FROM current_remote_reclinathon WHERE RemoteReclinathonId = ?"
            );
            $query->bind_param('s', $this->Season);
			$result = $this->Query($query);
			
			if ($result->num_rows == 0)
			{
				return true;
			}
			
			// We've won the race and are responsible for ending the Reclinathon.  Do so now.
			
			$query = $this->GetConnection()->prepare(
                "UPDATE current_remote_reclinathon SET RemoteReclinathonId = '' WHERE RemoteReclinathonId = ?"
            );
            $query->bind_param('s', $this->Season);
            $result = $this->Query($query);
			if (!$result)
			{
				return false;
			}
			
			// Log a system event for the final movie ending and for the Reclinathon ending.
			$feedEvents = new FEED_EVENTS();
			
			if ($this->GetRecliningState() == "Reclining" && $this->GetMovie()->GetID() != 0)
		    { 
			    $title = $this->GetMovie()->GetTitle();   
			    $feedEvents->PostSystemEvent("Finished $title", time(), "images/downtime.png");				
		    }
			
			$feedEvents->PostSystemEvent("Completed the Reclinathon!", time(), "images/finish.png");	
			
			return $result;
		}
		
		// Fetch details about the next pending context.
        $row = $result->fetch_assoc();
        if (!$row)
        {
            return false;
        }
        $ContextID = $row["ContextID"];
		$pending = $row["Pending"];
		
		// Only the first advance request for this context should take effect.  If someone has already advanced the state, exit.
		if ($pending != "1")
		{
			return false;
		}
		
		// We've won the race and are responsible for advancing the state.  Do so now.
		$query = $this->GetConnection()->prepare(
            "UPDATE RECLINATHON_CONTEXT SET TimeStamp = UNIX_TIMESTAMP(), Pending = '0' WHERE ContextID = ?"
        );
        $query->bind_param('i', $ContextID);
        $result = $this->query($query);
		
		// Post a system event for this advancement.
		$message = "";
		$timeStamp = time();
		$image = "";
		
		if ($this->GetRecliningState() == "Reclining" && $this->GetMovie()->GetID() != 0)
		{
			// If we've just advanced and the current state is 'Reclining', log a system event that a movie has ended. 
			$title = $this->GetMovie()->GetTitle();
			$message = "Finished $title";
            $image = "images/downtime.png";			
		}
		else if (($this->GetRecliningState() == "Downtime" || ($this->RecliningState == 'Preseason' && $this->RecliningStateModifier == 'Final Countdown')) && 
		          $this->GetMovie()->GetID() != 0)
		{
			// If we've just advanced and the current state is 'Downtime' or the 'Final Countdown', log a system event that a new movie is starting. 
			$title = $this->GetMovie()->GetTitle();
			$message = "Started $title";
            $image = $this->GetMovie()->GetImage();	
		}	
		
		if ($message != "" && $image != "")
		{
			$feedEvents = new FEED_EVENTS();
			$feedEvents->PostSystemEvent($message, $timeStamp, $image);
		}
		
		return $result;
	}
	
	public function CreateDowntime($season, $movieId, $timeStamp, $duration)
	{
        $this->TimeStamp = $timeStamp;
        $this->EstimatedDuration = $duration;
        $this->Captain->Load(1);
        $this->RecliningState = "Downtime";
        $this->Movie->Load($movieId);
        $this->Season = $season;
		$this->Pending = 1;
		
		return $this->Insert();
	}
	
	public function CreateMovieContext($season, $movieId, $timeStamp, $duration)
	{
        $this->TimeStamp = $timeStamp;
        $this->EstimatedDuration = $duration;
        $this->Captain->Load(1);
        $this->RecliningState = "Reclining";
        $this->Movie->Load($movieId);
        $this->Season = $season;
		$this->Pending = 1;
		
		return $this->Insert();
	}
	
	public function GetUrl()
	{
		if ($this->GetRecliningState() == "Downtime")
	    {
	        return "https://hangouts.google.com/call/styow2upujcp3hvhninl64l5uee";
	    }
	    else if ($this->GetRecliningState() == "Reclining")
	    {
		    $runTime = $this->GetMovie()->GetRunTime() * 60;
		    $timeRemaining = $this->GetTimeRemaining();
		    $timeCode = $runTime - $timeRemaining;

		    $url = $this->GetMovie()->GetUrl();
			
			if (strpos($this->GetMovie()->GetUrl(), "?") === false)
			{
				$url = $url . "?t=$timeCode";
			}
			else
			{
				$url = $url . "&amp;t=$timeCode";
			}
			
	        return $url;
	    }
		
		return "";
	}

    public function __tostring()
    {
        return $this->Title;
    }

}

?>
