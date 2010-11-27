<?php 

class DATABASE
{
    private $server = 'db1530.perfora.net';
    private $username = 'dbo248802449';
    private $password = 'Dr.Bundy';
    private $db = 'db248802449';
    private $connection = FALSE;
    private $error = 'ERROR_SUCCESS';

    private function Connect()
    {
        if ($this->connection)
        {
            return TRUE;
        }

        $this->connection = mysql_connect($this->server, $this->username, $this->password);
        if (!$this->connection)
        {
            $error = 'Error connecting to database server: ' . mysql_error();
            return FALSE;
        }

        if (!mysql_select_db($this->db, $this->connection))
        {
            $error = 'Error selecting database: ' . mysql_error();
            return FALSE;
        }

        return TRUE;
    } 

    public function Disconnect()
    {
        if ($this->connection)
        {
            mysql_close($this->connection);
            $this->connection = FALSE;
        }
    }

    public function Query($query)
    {
        if (!$this->Connect())
        {
            return FALSE;
        }
        
        $result = mysql_query($query, $this->connection);
        if (!result)
        {
            $error = 'Error executing query: ' . mysql_error();
        }

        return $result;
    }

    public function Error()
    {
        return $this->error;
    }

    public function Status()
    {
        return $this->connection;
    }

}

?>
