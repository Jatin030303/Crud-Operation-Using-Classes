<?php
class Database
{
    private $serverName, $userName, $password, $database;
    public $con;   //Properties

    public function __construct($serverName, $userName, $password, $database) //Construct
    {
        $this->serverName = $serverName;
        $this->userName = $userName;
        $this->password = $password;
        $this->database = $database;
        $this->connect();
    }
    private function connect()
    {
        $this->con = new mysqli($this->serverName, $this->userName, $this->password, $this->database);
        if ($this->con->connect_error) {
            die('Failed' . $this->con->connect_error);
        }
    }
}
$db = new Database('localhost', 'root', '', 'reg'); //Object
