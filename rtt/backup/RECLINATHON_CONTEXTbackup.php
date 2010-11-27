<?php

include 'RTTHeader.php';

$CURRENT_SEASON = "Winter 2008";
$DEFAULT_LOGO = "images/DefaultLogo.png";

class RECLINATHON_CONTEXT extends RTT_COMMON
{
    protected $ContextID;		       // INT
    protected $TimeStamp; 		       // INT	    	
    protected $EstimatedDuration; 	       // INT
    protected $Captain;		       // RECLINEE
    protected $RecliningState;        	// RECLINING_STATE
    protected $RecliningStateModifier;	// STATE_MODIFIER
    protected $Movie;                  	// MOVIE
    protected $ReclineeList;           	// RECLINEE_LIST
    protected $OptionalInfo;           	// OPTIONAL_CONTEXT
    protected $Season;                    // STRING
    protected $Logo;                      // STRING
   
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
            $this->RecliningState == 'Downtime')
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
                $this->RecliningState == 'Emergency Maintenance' );
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
                

    public function MovieQuery($query)
    {
        return $this->Movie->Query($query);
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

    public function TransitionState($Action)
    {
        $NextMovie = new MOVIE();
        $NextMovie->Load(1);

        $TRANSITION_TABLE["Reclining"]["Pause"] = array("State" => "Emergency Maintenace", "Modifier" => "", "Movie" => $this->Movie, "Duration" => "600");
        $TRANSITION_TABLE["Reclining"]["Stop"] = array("State" => "Downtime", "Modifier" => "", "Movie" => $NextMovie, "Duration" => "300");
        $TRANSITION_TABLE["Downtime"]["Play"] = array("State" => "Reclining", "Modifier" => "", "Movie" => $this->Movie, "Duration" => ($this->Movie->GetRunTime() * 60));
        $TRANSITION_TABLE["Emergency Maintenance"]["Play"] = array("State" => "Reclining", "Modifier" => "", "Movie" => $this->Movie, "Duration" => $this->GetTimeRemaining());

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
        $this->Movie->DisplaySelectList();
        echo "<BR><INPUT TYPE='submit' NAME='InputButton' VALUE='Pick New Movie'><BR><BR></TD></TR>";
        echo "<TR><TD valign='top'>Time Remaining:</TD><TD><input type='text' name='disp2' size='18' style=\"border: 0px; background-color: #C6D9F1; font-size: 125%;\" readonly></INPUT><BR><INPUT TYPE='text' NAME='EstimatedDuration'></INPUT> seconds<BR><INPUT TYPE='submit' NAME='InputButton' VALUE='+1'><INPUT TYPE='submit' NAME='InputButton' VALUE='+5'><INPUT TYPE='submit' NAME='InputButton' VALUE='+10'> minutes<BR><BR></TD></TR>";
        echo "<TR><TD valign='top'>Captain:</TD><TD>";
        $this->Captain->DisplayRocMemberList();
        echo "</TD></TR>";
        $this->OptionalInfo->DisplayOptionalRows();
        echo "</TABLE>";
        echo "<INPUT TYPE='hidden' NAME='class' VALUE='RECLINATHON_CONTEXT'><INPUT TYPE='hidden' NAME='ObjectID' VALUE='" . $this->ContextID . "'><INPUT TYPE='hidden' NAME='TimeStamp' VALUE='" . $this->TimeStamp . "'>";
        echo "</FORM>";
    }

    public function DisplayHistoryModuleInternal($page)
    {
        echo "<TABLE><TR><TH>History</TH><TH CLASS='right'><A HREF = 'http://www.reclinathon.com/rtt'>Go To Current</A></TH></TR>";
        
        $query = "SELECT TimeStamp FROM RECLINATHON_CONTEXT WHERE TimeStamp > '" . $this->TimeStamp . "' AND TimeStamp < UNIX_TIMESTAMP() AND Season = '" . $this->Season . "' ORDER BY TimeStamp LIMIT 2";
        $result = $this->query($query);
        $MaxTimeStamp = $this->TimeStamp;
        while($row = mysql_fetch_assoc($result))
        {
            $MaxTimeStamp = $row["TimeStamp"];
        }

        $query = "SELECT ContextID FROM RECLINATHON_CONTEXT WHERE TimeStamp <= '" . $MaxTimeStamp . "' AND Season = '" . $this->Season . "' ORDER BY TimeStamp DESC LIMIT 5";
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

    public function Insert()
    {
        $query = "INSERT INTO MOVIE (Title, RunTime, TrailerLink, IMDBLink, Freshness, Image) VALUES (";
        $query = $query . "'" . $this->Title . "', ";
        $query = $query . "'" . $this->RunTime . "', ";
        $query = $query . "'" . $this->TrailerLink . "', ";
        $query = $query . "'" . $this->IMDBLink . "', ";
        $query = $query . "'" . $this->Freshness . "', ";
        $query = $query . "'" . $this->Image . "', ";
        $query = $query . "'" . $this->Season . "')";

        echo $query . "<BR>";
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
        
        return $this->Update();
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
            $query = "INSERT INTO STATE_MODIFIER (Modifier) VALUES ('" . $this->RecliningStateModifier . "')";
            echo $query . "<BR>";
        }
        
        $query = "UPDATE RECLINATHON_CONTEXT SET ";
        $query = $query . "CaptainID = '" . $this->Captain->GetID() . "'";
        $query = $query . ", EstimatedDuration = '" . $this->EstimatedDuration . "'";
        $query = $query . ", StateID = '" . $StateID . "'";
        $query = $query . ", ModifierID = '" . $ModifierID . "'";
        $query = $query . ", MovieID = '" . $this->Movie->GetID() . "' WHERE ContextID = '" . $this->ContextID . "'";

        echo $query . "<BR>";
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

        $this->Dump(0, true);

        if ($process == "Update")
        {
            $status = $this->Update();
        }
        else if ($process == "Insert")
        {
            $status = false;
        }
        
        if ($status)
        {
            echo "<meta http-equiv='refresh' content=\"0;url=captain.php?ContextID=" . $this->ContextID . "\" />";
        }

        return $status;
    }

    public function __tostring()
    {
        return $this->Title;
    }

}

?>