<?php

#include RTTHeader.php";

class RECLINEE extends RTT_COMMON
{
    protected $ReclineeID;		// INT
    protected $FormalName; 	    	// STRING
    protected $DisplayName; 		// STRING
    protected $RecliningLevel;		// INT
    protected $CommitmentLevel;         // INT
    protected $RecliningTime;		// INT
    protected $Bio;                  	// STRING
   
    function __construct() 
    {
        $this->ReclineeID = 0;
        $this->FormalName = 'Unknown';
        $this->DisplayName = 'Unknown';
        $this->RecliningLevel = 0;
        $this->CommitmentLevel = 0;
        $this->RecliningTime = 0;
        $this->Bio = 'Empty';
    }

    public function Load($ReclineeID)
    {
        $query = "SELECT * FROM RECLINEE WHERE ReclineeID = " . $ReclineeID;
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

        $this->ReclineeID = $row["ReclineeID"];
        $this->FormalName = $row["FormalName"];
        $this->DisplayName = $row["DisplayName"];
        $this->RecliningLevel = $row["RecliningLevel"];
        $this->CommitmentLevel = $row["CommitmentLevel"];
        $this->RecliningTime = $row["RecliningTime"];
        $this->Bio = $row["Bio"];

        return TRUE;
    }

    public function DisplayReclineeList($ShowRocMembers)
    {
        $query = "SELECT ReclineeID, DisplayName FROM RECLINEE";
        if ($ShowRocMembers)
        {
            $query .= " WHERE RocMember = 1";
        }
        $query .= " ORDER BY DisplayName";
        $result = $this->query($query);
        if (!$result)
        {
            return false;
        }

        echo "<SELECT NAME='ReclineeID'>";

        while($row = mysql_fetch_assoc($result))
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

    public function __tostring()
    {
        return $this->DisplayName;
    }

}

?>