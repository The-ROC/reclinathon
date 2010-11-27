<?php

#include RTTHeader.php";

class RECLINEE_LIST extends RTT_COMMON
{
    protected $NumEntries = 0;	// INT
    protected $ReclineeListID;  // ARRAY
    protected $Reclinee; 	// ARRAY
    function __construct() 
    {
        $this->NumEntries = 0;
        $this->ReclineeListID = array();
        $this->Reclinee = array();
    }

    public function Load($ContextID)
    {
        $query = "SELECT * FROM RECLINEE_LIST WHERE ContextID = " . $ContextID;
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        $this->NumEntries = mysql_num_rows($result);
        
        while ($row = mysql_fetch_assoc($result))
        {
            $this->ReclineeListID[] = $row["ReclineeListID"];
            //$NewReclinee = new RECLINEE();
            //$NewReclinee->Load($row["ReclineeID"]);
            $this->Reclinee[] = new RECLINEE();
            end($this->Reclinee)->Load($row["ReclineeID"]);
        }

        return TRUE;
    }

}

?>