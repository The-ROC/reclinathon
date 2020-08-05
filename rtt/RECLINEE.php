<?php

#include RTTHeader.php";

class RECLINEE extends RTT_COMMON
{
    protected $ReclineeID;		// INT
    protected $FirstName; 	    	// STRING
    protected $LastName; 	    	// STRING
    protected $DisplayName; 		// STRING
    protected $Bio;                  	// STRING
    protected $RocMember;      		// INT (BOOL)
    protected $Email;			// STRING
    protected $UserName;		// STRING
    protected $PasswordHash;		// STRING
   
    function __construct() 
    {
        $this->ReclineeID = 0;
        $this->FirstName = "";
        $this->LastName = "";
        $this->DisplayName = "";
        $this->Bio = "";
        $this->RocMember = 0;
        $this->Email = "";
        $this->UserName = "";
        $this->PasswordHash = "";
    }

    public function Load($ReclineeID)
    {
        $query = $this->GetConnection()->prepare("SELECT * FROM RECLINEE WHERE ReclineeID = ?");
        $query->bind_param('i', $ReclineeID);
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

        $this->ReclineeID = $row["ReclineeID"];
        $this->FirstName = $row["FirstName"];
        $this->LastName = $row["LastName"];
        $this->DisplayName = $row["DisplayName"];
        $this->Bio = $row["Bio"];
        $this->RocMember = $row["RocMember"];
        $this->Email = $row["Email"];
        $this->UserName = $row["UserName"];
        $this->PasswordHash = $row["PasswordHash"];       

        return TRUE;
    }

    public function LoadFromForm()
    {
        $this->ReclineeID = $_POST["ObjectID"];
        $this->FirstName = $_POST["FirstName"];
        $this->LastName = $_POST["LastName"];
        $this->DisplayName = $_POST["DisplayName"];
        $this->Bio = $_POST["Bio"];
        $this->Email = $_POST["Email"];
        $this->UserName = $_POST["UserName"];

        if ($this->ReclineeID == "" ||
            $this->FirstName == "" ||
            $this->LastName == "" ||
            $this->DisplayName == "" ||
            $this->Email == "" ||
            $this->UserName == "" ||
            $_POST["Password"] != $_POST["VerifyPassword"])
        {
            return FALSE;
        }

        if ($_POST["Password"] != "")
        {
            $this->PasswordHash = sha1($_POST["Password"]);
        }

        return TRUE;
    }

    public function Update()
    {
        $query = "UPDATE RECLINEE SET ";
        $query = $query . "FirstName = '" . $this->FirstName . "'";
        $query = $query . ", LastName = '" . $this->LastName . "'";
        $query = $query . ", DisplayName = '" . $this->DisplayName . "'";
        $query = $query . ", Bio = '" . $this->Bio . "'";
        $query = $query . ", Email = '" . $this->Email . "'";
        $query = $query . ", UserName = '" . $this->UserName . "'";

        $queryPrefix = "UPDATE RECLINEE SET FirstName = ?, LastName = ?, DisplayName = ?, Bio = ?, Email = ?, UserName = ?";
        $queryPass = ", PasswordHash = ?";
        $queryWhere = "WHERE ReclineeID = ?";

        $queryString= $queryPrefix;
        if ($this->PasswordHash != "")
        {
            $queryString .= $queryPass . $queryWhere;
            $query = $this->GetConnection()->prepare($queryString);
            $query->bind_param(
                'sssssssi',
                $this->FirstName, $this->LastName, $this->DisplayName, $this->Bio,
                $this->Email, $this->UserName, $this->PasswordHash, $this->ReclineeID
            );
        }
        else
        {
            $queryString .= $queryWhere;
            $query = $this->GetConnection()->prepare($queryString);
            $query->bind_param(
                'ssssssi',
                $this->FirstName, $this->LastName, $this->DisplayName, $this->Bio,
                $this->Email, $this->UserName, $this->ReclineeID
            );
        }

        $result = $this->Query($query);

        if (!$result)
        {
            return FALSE;
        }     

        return TRUE;
    }

    public function ProcessForm()
    {
        if ($this->LoadFromForm())
        {
            return $this->Update();
        }

        return FALSE;
    }

    public function DisplayForm()
    {
        echo "<FORM ACTION='ProcessReclineeForm.php' METHOD='post'>";
        echo "<TABLE>";
        echo "<TR><TD>FirstName</TD><TD><INPUT TYPE='text' NAME='FirstName' VALUE='" . $this->FirstName ."'></TD></TR>";
        echo "<TR><TD>LastName</TD><TD><INPUT TYPE='text' NAME='LastName' VALUE='" . $this->LastName ."'></TD></TR>";
        echo "<TR><TD>DisplayName</TD><TD><INPUT TYPE='text' NAME='DisplayName' VALUE='" . $this->DisplayName ."'></TD></TR>";
        echo "<TR><TD>Bio</TD><TD><INPUT TYPE='text' NAME='Bio' VALUE='" . $this->Bio ."'></TD></TR>";
        echo "<TR><TD>Email</TD><TD><INPUT TYPE='text' NAME='Email' VALUE='" . $this->Email ."'></TD></TR>";
        echo "<TR><TD>UserName</TD><TD><INPUT TYPE='text' NAME='UserName' VALUE='" . $this->UserName ."'></TD></TR>";
        echo "<TR><TD>Password (Leave blank to keep your current password)</TD><TD><INPUT TYPE='password' NAME='Password'></TD></TR>";
        echo "<TR><TD>Verify Password</TD><TD><INPUT TYPE='password' NAME='VerifyPassword'></TD></TR>";
        echo"<TR><TD> </TD><TD><INPUT TYPE='hidden' NAME='class' VALUE='RECLINEE'><INPUT TYPE='hidden' NAME='ObjectID' VALUE='" . $this->ReclineeID . "'><INPUT  TYPE='submit' VALUE='Submit'></TD></TR>";
        echo "</TABLE>";
        echo "</FORM>";
    }

    public function DisplayReclineeList($ShowRocMembers)
    {
        $queryString = "SELECT ReclineeID, DisplayName FROM RECLINEE";
        if ($ShowRocMembers)
        {
            $queryString .= " WHERE RocMember = 1";
        }
        $queryString .= " ORDER BY DisplayName";
        $query = $this->GetConnection()->prepare($queryString);
        $result = $this->query($query);
        if (!$result)
        {
            return false;
        }

        echo "<SELECT NAME='ReclineeID'>";

        while($row = $result->fetch_assoc())
        {
            echo "<OPTION VALUE='" . $row["ReclineeID"] . "'";
            if ($this->ReclineeID == $row["ReclineeID"])
           {
                echo " SELECTED";
            }
            echo ">" . $row["DisplayName"] . "</OPTION>";
        }

        echo "</SELECT>";

        return true;
    }

    public function DisplaySelectList()
    {
        return $this->DisplayReclineeList(false);
    }

    public function DisplayRocMemberList()
    {
        return $this->DisplayReclineeList(true);
    }

    public function GetID()
    {
        return $this->ReclineeID;
    }

    public function GetDisplayName()
    {
        return $this->DisplayName;
    }

    public function HasVoted()
    {
        $CurrentSeason = $this->GetCurrentSeason();

        $query = $this->GetConnection()->prepare("SELECT * FROM VOTE WHERE Season = ? AND ReclineeID = ?");
        $query->bind_param('si', $CurrentSeason, $this->ReclineeID);
        $result = $this->query($query);

        if (!$result || $result->num_rows == 0)
        {
            return false;
        }

        return true;
    }

    public function HasAnsweredQuiz($quizName)
    {
        $LatestQuestionOrder = 0;
        $LastQuestionOrder = 0;

        $query = $this->GetConnection()->prepare(
            "SELECT MAX(q.Ordering) AS LatestQuestionOrder FROM QUIZ_ANSWERS a JOIN QUIZ_QUESTION q ON q.QuestionID = a.QuestionID WHERE q.Season = ? AND a.ReclineeID = ?"
        );
        $query->bind_param('si', $quizName, $this->ReclineeID);
        $result = $this->query($query);

        if (!$result)
        {
            return false;
        }

        $row = $result->fetch_assoc();

        if ($row["LatestQuestionOrder"] != "")
        {
            $LatestQuestionOrder = $row["LatestQuestionOrder"]; 
        }

        $query = $this->GetConnection()->prepare(
            "SELECT MAX(Ordering) AS LastQuestionOrder FROM QUIZ_QUESTION WHERE Season = ?"
        );
        $query->bind_param('s', $quizName);
        $result = $this->query($query);

        if (!$result)
        {
            return false;
        }

        $row = $result->fetch_assoc();

        if (!$row || $row["LastQuestionOrder"] == "")
        {
            return false;
        }

        $LastQuestionOrder = $row["LastQuestionOrder"];

        if ($LatestQuestionOrder == $LastQuestionOrder)
        {
            return true;
        }

        return false;
    }

    public function __tostring()
    {
        return $this->DisplayName;
    }

}

?>