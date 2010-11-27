<?php

include 'RTTHeader.php';

$CURRENT_SEASON = "Winter 2009";
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
    }

    private function DisplayState()
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
            return true;
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
        $query = "SELECT * FROM RECLINATHON_CONTEXT rc LEFT JOIN RECLINING_STATE rs ON rc.StateID = rs.StateID LEFT JOIN STATE_MODIFIER sm ON rc.ModifierID = sm.ModifierID LEFT JOIN LOGO lg ON rc.LogoID = lg.LogoID WHERE ContextID = " . $ContextID;
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
        $this->Season = $CURRENT_SEASON;

        return TRUE;
    }

    public function LoadCurrent($Season)
    {
        $query = "SELECT ContextID FROM RECLINATHON_CONTEXT WHERE TimeStamp <= '" . date("U") . "' AND Season = '" . $Season . "' ORDER BY TimeStamp DESC";
        $result = $this->query($query);
        if (!$result)
        {
            return false;
        }
        $row = mysql_fetch_assoc($result);
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
        if (!$MovieList->Load("Winter2009"))
        {
            return false;
        }
        $NextMovie = $MovieList->GetNextMovie(time(), $END_OF_REGULATION);

        $TRANSITION_TABLE["Reclining"]["Pause"] = array("State" => "Emergency Maintenance", "Modifier" => "", "Movie" => $this->Movie, "Duration" => "600");
        $TRANSITION_TABLE["Reclining"]["Stop"] = array("State" => "Downtime", "Modifier" => "", "Movie" => $NextMovie, "Duration" => "300");
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
        $query = "SELECT StateID, State FROM RECLINING_STATE ORDER BY State";
        $result = $this->query($query);
        if (!$result)
        {
            return false;
        }

        echo "<SELECT NAME='RecliningState'>";

        while($row = mysql_fetch_assoc($result))
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
        $query = "SELECT ModifierID, Modifier FROM STATE_MODIFIER ORDER BY Modifier";
        $result = $this->query($query);
        if (!$result)
        {
            return false;
        }

        echo "<SELECT NAME='RecliningStateModifier'>";
        echo "<OPTION VALUE=''> </OPTION>";

        while($row = mysql_fetch_assoc($result))
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
        if (!$MovieList->Load("Winter2009"))
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

        $query = "SELECT ContextID FROM RECLINATHON_CONTEXT WHERE TimeStamp <= UNIX_TIMESTAMP() AND Season = '" . $this->Season . "' ORDER BY TimeStamp DESC";
        $result = $this->query($query);
        while($row = mysql_fetch_assoc($result))
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

    public function DisplayDowntimeModule($ShowDowntime)
    {
        $question = "";
        $asnwer = "";
        $refresh = 20;
        $ShowDowntimeClip = false;

        $query = "SELECT * FROM VideoClips WHERE Played = '" . $this->ContextID . "'";
        $result = $this->query($query);
        if ($row = mysql_fetch_assoc($result))
        {
            $query = "SELECT * FROM VideoClips WHERE Played = 0 AND Ordering = '" . $row["Ordering"] . "'";
            $result = $this->query($query);
            if (mysql_num_rows($result) > 0)
            {
                $ShowDowntimeClip = true;
            }
        }
        else
        {
            $query = "SELECT * FROM VideoClips WHERE Played = 0";
            $result = $this->query($query);
            if (mysql_num_rows($result) > 0)
            {
                $ShowDowntimeClip = true;
            }
        }

        //if($this->ShowDowntime == false)
        //{
        //    $ShowDowntimeClip = false;
        //}

        if($this->RecliningState != 'Downtime' || !$ShowDowntimeClip) 
        {
            $query = "SELECT MAX(TID) AS LargestID FROM Trivia";
            $result = $this->query($query);
            $row = mysql_fetch_assoc($result);
            $RandID = mt_rand(1, $row["LargestID"]);
            $query = "SELECT * FROM Trivia WHERE TID >= '" . $RandID . "' ORDER BY TID LIMIT 1";
            $result = $this->query($query);
            $row = mysql_fetch_assoc($result);
            $question = $row["Question"];
            $answer = $row["Answer"];
        }
        else 
        {
            $query = "SELECT * FROM VideoClips WHERE Played = 0 ORDER BY Ordering LIMIT 1";
            $result = $this->query($query);
            $row = mysql_fetch_assoc($result);
            $question = $row["URL"];
            $answer = $row["Caption"];
            $refresh = $row["Refresh"];
            $query = "UPDATE VideoClips SET Played = '" . $this->ContextID . "' WHERE VCID = '" . $row["VCID"] . "' LIMIT 1";
            $result = $this->query($query);
        }

        echo "<TABLE>";
        echo "<TR cellspacing='0'><TH>Entertainment</TH>";
        echo "<TR><TD><FONT SIZE='+1'%'>" . $question . "<BR><BR></TD></TR>";
        echo "<TR ID='DowntimeAnswer' style=\"display:none;\"><TD><FONT SIZE='+2'%'>" . $answer . "</TD></TR>";
        echo "<INPUT TYPE='hidden' ID='DowntimeAnswerInterval' VALUE='" . $refresh/2 . "'>";
        echo "<meta http-equiv='refresh' content=\"" . $refresh . ";url=" . $URL . "\" />";
        echo "</TABLE>";
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
        $query = "SELECT * FROM RECLINATHON_CONTEXT ORDER BY TimeStamp DESC";
        $result = $this->query($query);

        while($row = mysql_fetch_assoc($result))
        {
            $context = new RECLINATHON_CONTEXT();
            $context->Load($row["ContextID"]);
            $context->DisplayModule();
        }
    }  

    public function DumpSeason($season)
    {
        $query = "SELECT * FROM RECLINATHON_CONTEXT WHERE Season = '" . $season . "' ORDER BY TimeStamp";
        $result = $this->query($query);

        print "<ContextList>";
        while($row = mysql_fetch_assoc($result))
        {
            $context = new RECLINATHON_CONTEXT();
            $context->Load($row["ContextID"]);
            $context->DumpXml();
        }
        print "</ContextList>";
    }
    

    public function Insert()
    {
        $StateID = 0;
        $query = "SELECT StateID FROM RECLINING_STATE WHERE State = '" . $this->RecliningState . "'";
        $result = $this->query($query);
        if (!$result)
        {
            return false;
        }
        if ($row = mysql_fetch_assoc($result))
        {
            $StateID = $row["StateID"];
        }
        else
        {
            $query = "INSERT INTO RECLINING_STATE (State) VALUES ('" . $this->RecliningState . "')";
            echo $query . "<BR>";
        }

        $ModifierID = 0;
        $query = "SELECT ModifierID FROM STATE_MODIFIER WHERE Modifier = '" . $this->RecliningStateModifier . "'";
        $result = $this->query($query);
        if (!$result)
        {
            return false;
        }
        if ($row = mysql_fetch_assoc($result))
        {
            $ModifierID = $row["ModifierID"];
        }
        else
        {
            if ($this->RecliningStateModifier != "")
            {
                $query = "INSERT INTO STATE_MODIFIER (Modifier) VALUES ('" . $this->RecliningStateModifier . "')";
                echo $query . "<BR>";
            }
        }
        
        $query = "INSERT INTO RECLINATHON_CONTEXT (TimeStamp, EstimatedDuration, CaptainID, StateID, ModifierID, MovieID, Season, LogoID) VALUES (";
        $query = $query . "'" . $this->TimeStamp . "', ";
        $query = $query . "'" . $this->EstimatedDuration . "', ";
        $query = $query . "'" . $this->Captain->GetID() . "', ";
        $query = $query . "'" . $StateID . "', ";
        $query = $query . "'" . $ModifierID . "', ";
        $query = $query . "'" . $this->Movie->GetID() . "', ";
        $query = $query . "'" . $this->Season . "', ";
        $query = $query . "'0')";

        echo $query . "<BR>";
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
        $query = "SELECT StateID FROM RECLINING_STATE WHERE State = '" . $this->RecliningState . "'";
        $result = $this->query($query);
        if (!$result)
        {
            return false;
        }
        if ($row = mysql_fetch_assoc($result))
        {
            $StateID = $row["StateID"];
        }
        else
        {
            $query = "INSERT INTO RECLINING_STATE (State) VALUES ('" . $this->RecliningState . "')";
            //echo $query . "<BR>";
        }

        $ModifierID = 0;
        $query = "SELECT ModifierID FROM STATE_MODIFIER WHERE Modifier = '" . $this->RecliningStateModifier . "'";
        $result = $this->query($query);
        if (!$result)
        {
            return false;
        }
        if ($row = mysql_fetch_assoc($result))
        {
            $ModifierID = $row["ModifierID"];
        }
        else
        {
            $query = "INSERT INTO STATE_MODIFIER (Modifier) VALUES ('" . $this->RecliningStateModifier . "')";
            //echo $query . "<BR>";
        }
        
        $query = "UPDATE RECLINATHON_CONTEXT SET ";
        $query = $query . "CaptainID = '" . $this->Captain->GetID() . "'";
        $query = $query . ", EstimatedDuration = '" . $this->EstimatedDuration . "'";
        $query = $query . ", StateID = '" . $StateID . "'";
        $query = $query . ", ModifierID = '" . $ModifierID . "'";
        $query = $query . ", MovieID = '" . $this->Movie->GetID() . "' WHERE ContextID = '" . $this->ContextID . "'";

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
            $Season = "Winter2010";
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
                $query = "SELECT * FROM SENSOR";
                $result = $this->query($query);
                $NumRows = mysql_num_rows($result);
                echo "<SensorDataList><NumDataPoints>" . $NumRows . "</NumDataPoints>";
                while ($row = mysql_fetch_row($result))
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

        $query = "SELECT r.ReclineeID, r.FormalName FROM RECLINEE r JOIN RFID rf on rf.ReclineeID = r.ReclineeID WHERE rf.Tag = '" . $Tag . "'";
        $result = $this->query($query);
        if (!$result)
        {
            echo "SQL Error";
            return false;
        }
        $row = mysql_fetch_assoc($result);
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

        $query = "SELECT r.ReclineeID, r.FormalName FROM RECLINEE r JOIN RFID rf on rf.ReclineeID = r.ReclineeID WHERE rf.Tag = '" . $Tag . "'";
        $result = $this->query($query);
        if (!$result)
        {
            echo "SQL Error";
            return false;
        }
        $row = mysql_fetch_assoc($result);
        if (!$row)
        {
            echo "Tag Not Found";
            return false;
        }

        echo $row["FormalName"];

        $query = "INSERT INTO RFID_SWIPE (ReclineeID, TimeStamp, Season) VALUES ('" . $row["ReclineeID"] . "', '" . time() . "', '" . $Season . "')";
        $result = $this->query($query);
        if (!$result)
        {
            echo $query;
            echo "SQL Insert Error";
            return false;
        }

        return true;

    }

    public function __tostring()
    {
        return $this->Title;
    }

}

?>