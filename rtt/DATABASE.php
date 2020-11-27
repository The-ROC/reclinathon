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
        $TEST_SERVER = 
            $_SERVER['SERVER_NAME'] == 'localhost' ||
            file_exists($_SERVER['DOCUMENT_ROOT']."\include\localdb") || 
            file_exists($_SERVER['DOCUMENT_ROOT']."/include/localdb");
        if ($TEST_SERVER)
        {
            $this->server = 'localhost';
            $this->username = 'ROC_USER';
        }

        if ($this->connection)
        {
            return TRUE;
        }

        $this->connection = mysqli_connect($this->server, $this->username, $this->password, $this->db);
        if (!$this->connection)
        {
            $error = 'Error connecting to database server: ' . mysqli_connect_error();
            return FALSE;
        }

        return TRUE;
    } 

    public function Disconnect()
    {
        if ($this->connection)
        {
            mysqli_close($this->connection);
            $this->connection = FALSE;
        }
    }

    public function GetConnection()
    {
        if (!$this->Connect())
        {
            return FALSE;
        }

        return $this->connection;
    }

    public function Query($query)
    {
        if (!$this->Connect())
        {
            return FALSE;
        }
        
        $result = $query->execute();
        if (!$result)
        {
            $error = 'Error executing query: ' . $this->connection->error;
        }
        else
        {
            $selectResult = $query->get_result();
            if ($selectResult) {
                $result = $selectResult;
            } 
        }

        return $result;
    }

    public function GetEscapeString($str)
    {
        if(!$this->Connect())
        {
            return FALSE;
        }

        return mysqli_real_escape_string($this->connection, $str);
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
