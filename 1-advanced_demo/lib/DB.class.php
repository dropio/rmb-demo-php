<?php
Class Config {

    public static $user = 'dropio';
    public static $pass = 'abc123';
    public static $dbname = 'dropio';

}

# This is a simple class that extends PDO
Class DB {

    private $dbh;
    private $stmt;
    private $result;

    public function __construct($dsn,$user,$pass) {
     try {
       $this->dbh = new PDO($dsn, $user, $pass);
     } catch (PDOException $e)
     {
       echo $e->getMessage() . "$user -  $pass";exit;
     }

     
    }

    public static function getInstance($user=null,$pass=null,$dbname=null,$host='localhost',$port=3306)
    {
        if ($user === null)
        {
            $user = Config::$user;
            $pass = Config::$pass;
            $dbname= Config::$dbname;
        }
        
        $dsn = "mysql:host=$host;dbname=$dbname;port=$port";

        return new DB($dsn,$user,$pass);
    }

    public function prepare($sql)
    {
        $this->stmt = $this->dbh->prepare($sql);
        return $this;
    }

    public function execute($arr=array())
    {
        $this->result = $this->stmt->execute($arr);
        return $this;
    }

    /**
     * Get the status / result of the last query
     * @return <type>
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Fetch the data that came back from the last query
     * 
     * @param <type> $mode
     * @return <type>
     */
    public function fetch($mode=PDO::FETCH_ASSOC)
    {
        return $this->stmt->fetch($mode);
    }

    public function fetchAll($mode=PDO::FETCH_ASSOC)
    {
        return $this->stmt->fetchAll($mode);
    }


    /**
     * Send a SQL statement to the database.
     *
     * @param string $sql
     * @return <type>
     */
    public function query($sql)
    {
        $this->stmt = $this->dbh->query($sql);
        return $this;
    }

    public function getError()
    {
        return $this->stmt->errorInfo();
    }
}
