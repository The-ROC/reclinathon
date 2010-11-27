<?php

#include RTTHeader.php";

class OPTIONAL_CONTEXT extends RTT_COMMON
{
    protected $NumEntries = 0;	// INT
    protected $OptionalInfoID;  // ARRAY
    protected $Key; 		// ARRAY
    protected $Value;		// ARRAY
   
    function __construct() 
    {
        $this->Key = array();
        $this->Value = array();
    }

    public function Load($ContextID)
    {
        $query = "SELECT * FROM OPTIONAL_CONTEXT WHERE ContextID = " . $ContextID;
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        $this->NumEntries = mysql_num_rows($result);
        
        while ($row = mysql_fetch_assoc($result))
        {
            $this->OptionalInfoID[] = $row["OptionalInfoID"];
            $this->Key[] = $row["Key"];
            $this->Value[] = $row["Value"];
        }

        return TRUE;
    }

    public function DisplayOptionalRows()
    {
        if ($this->NumEntries > 0)
        {
            echo "<TR><TD>&nbsp;</TD><TD>&nbsp;</TD></TR>";
        }

        for($i = 0; $i < $this->NumEntries; $i++)
        {
            echo "<TR><TD valign='top'>" . $this->Key[$i] . "</TD><TD valign='top'>" . $this->Value[$i] . "</TD></TR>";
        }
        
        return true;
    }

}

?>