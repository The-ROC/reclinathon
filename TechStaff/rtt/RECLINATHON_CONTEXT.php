<?php

include 'RTTHeader.php';

class RECLINATHON_CONTEXT extends RTT_COMMON
{
    protected $ContextID;		// INT
    protected $TimeStamp; 		// INT	    	
    protected $EstimatedDuration; 	// INT
    protected $Captain;		// RECLINEE
    protected $RecliningState;        	// RECLINING_STATE
    protected $RecliningStateModifier;	// STATE_MODIFIER
    protected $Movie;                  	// MOVIE
    protected $ReclineeList;           	// RECLINEE_LIST
    protected $OptionalInfo;           	// OPTIONAL_CONTEXT
   
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

    private function DisplayTime()
    {
        return date("m/d/Y g:i A", $this->TimeStamp);
    }
                

    public function MovieQuery($query)
    {
        return $this->Movie->Query($query);
    }

    public function GetTimeRemaining()
    {
        $currenttime = time();
        $duration = $this->TimeStamp + $this->EstimatedDuration - $currenttime;  

        if($duration < 0) 
        {
            //$duration = 0;
        } 

        //echo $currenttime . '<BR>';
        return $duration;
    }

    public function Load($ContextID)
    {
        $query = $this->GetConnection()->prepare(
            "SELECT * FROM RECLINATHON_CONTEXT rc LEFT JOIN RECLINING_STATE rs ON rc.StateID = rs.StateID LEFT JOIN STATE_MODIFIER sm ON rc.ModifierID = sm.ModifierID WHERE ContextID = ?"
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

        return TRUE;
    }

    public function LoadFromForm()
    {
        $this->ContextID = $_POST["ObjectID"];
        $this->TimeStamp = $_POST["TimeStamp"];
        $this->EstimatedDuration = $_POST["EstimatedDuration"];
        $this->Captain->Load($_POST["ReclineeID"]);
        $this->RecliningState = $_POST["RecliningState"];
        $this->RecliningStateModifier = $_POST["RecliningStateModifier"];
        $this->Movie->Load($_POST["MovieID"]);
        $this->ReclineeList->Load($_POST["ContextID"]);
        $this->OptionalInfo->Load($_POST["ContextID"]);

        return TRUE;
    }

    public function DisplayModule()
    {
        echo "<FORM NAME='sw'>";
        echo "<TABLE>";
        echo "<TR cellspacing='0'><TH><A HREF='insert.php?class=RECLINATHON_CONTEXT&ObjectID=" . $this->ContextID . "'>" . $this->DisplayState() . "</A></TH><TH CLASS='right'>" . $this->DisplayTime() . "</TH></TR>";
        if ($this->HasMovie())
        {
            echo "<TR><TD>Movie:</TD><TD>" . $this->Movie . "</TD></TR>";
        }
        echo "<TR><TD>Time Remaining:*</TD><TD><input type='text' name='disp2' size='20' value='" . $this->GetTimeRemaining() . "' style=\"border: 0px; background-color: #C6D9F1;\" readonly></INPUT></TD></TR>";
        echo "<TR><TD>Captain:</TD><TD>" . $this->Captain . "</TD></TR>";
        $this->OptionalInfo->DisplayOptionalRows();
        echo "</TABLE>";
        echo "</FORM>";
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
        echo"<TR><TD> </TD><TD><INPUT TYPE='hidden' NAME='class' VALUE='RECLINATHON_CONTEXT'><INPUT TYPE='hidden' NAME='ObjectID' VALUE='" . $this->ContextID . "'><INPUT  TYPE='submit' VALUE='Submit'></TD></TR>";
        echo "</TABLE>";
        echo "</FORM>";
    }

    public function DisplayAll()
    {
        $query = $this->GetConnection()->prepare(
            "SELECT * FROM RECLINATHON_CONTEXT ORDER BY TimeStamp DESC"
        );
        $result = $this->query($query);

        while($row = $result->fetch_assoc())
        {
            $context = new RECLINATHON_CONTEXT();
            $context->Load($row["ContextID"]);
            $context->DisplayModule();
        }
    }  

    public function Insert()
    {
        $query = $this->GetConnection()->prepare(
            "INSERT INTO MOVIE (Title, RunTime, TrailerLink, IMDBLink, Freshness, Image) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $query->bind_param(
            'sissis',
            $this->Title, $this->RunTime, $this->TrailerLink, $this->IMDBLink,
            $this->Freshness, $this->Image
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
            $this->Title, $this->RunTime, $this->TrailerLink, $this->IMDBLink,
            $this->Freshness, $this->Image, $this->MovieID
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
        $this->DisplayModule();
        /*if ($this->ContextID == 0)
        {
            return $this->Insert();
        }
        else
        {
            return $this->Update();
        }*/
        return false;
    }

    public function __tostring()
    {
        return $this->Title;
    }

}

?>