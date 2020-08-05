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
        $query = $this->GetConnection()->prepare("SELECT * FROM RECLINEE_LIST WHERE ContextID = ?");
        $query->bind_param('i', $ContextID);
        $result = $this->Query($query);
        if (!$result)
        {
            return FALSE;
        }

        $this->NumEntries = $result->num_rows;
        
        while ($row = $result->fetch_assoc())
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