<?php 

abstract class RTT_COMMON
{   
    protected static $Database = NULL;
	
	public function IsDevMode()
	{
		return file_exists($_SERVER['DOCUMENT_ROOT']."\include\localdb") || file_exists($_SERVER['DOCUMENT_ROOT']."/include/localdb");
	}

    private function DumpLine($Indent, $String)
    {
        for($i=0; $i < $Indent; $i++)
        {
            echo "&nbsp;";
        }
        print "$String<BR>";
    }

    public function Dump($Indent, $DumpHeader)
    {
        if ($DumpHeader)
        {
            $this->DumpLine($Indent, get_class($this).':');
            $this->DumpLine($Indent, "{");
        }
  
        foreach($this as $key => $value) {
            if ($value instanceof RTT_COMMON)
            {
                $this->DumpLine($Indent+8, "$key (".get_class($value)."):");
                $value->Dump($Indent+8, FALSE);
            }
            else if ($value instanceof DATABASE)
            {
                $this->Dumpline($Indent+8, "$key connection = $value->Status()");
            }
            else
            {
                $this->DumpLine($Indent+8, "$key = $value");
            }
        }

        if ($DumpHeader)
        {
            $this->DumpLine($Indent, "}");
        }
    }

    public function DumpXml()
    {
        print "<" . get_class($this) . ">\n";

        foreach($this as $key => $value) 
        {
            print "<" . $key . ">" . str_replace('&', '&amp;', $value) . "</" . $key . ">\n";
        }

        print "</" . get_class($this) . ">\n";
    }

    public function GetDatabase()
    {
        if (self::$Database == NULL)
        {
            self::$Database = new DATABASE();
        }
        return self::$Database;
    }

    public function GetConnection()
    {
        return $this->GetDatabase()->GetConnection();
    }

    public function Query($query)
    {
        return $this->GetDatabase()->Query($query);
    }

    public function GetCurrentSeason()
    {
        $query = $this->GetConnection()->prepare("SELECT * from CURRENT_SEASON");
        $result = $this->Query($query);

	    if ($result)
        {
            $row = $result->fetch_assoc();

            if ($row)
            {
                return $row["Season"];
            }
        }

        return "default";
    }        

    public function __tostring()
    {
        return get_class($this);
    }

}

?>
