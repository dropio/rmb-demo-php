<?php
/**
 * This is a configuration class that is used when you use an empty constructor.
 */
Class Config {

    public static $user;
    public static $pass;
    public static $dbname;
	public static $host;
	public static $port;

}

# This is a simple class that extends PDO
Class DB {

    /**
     *
     * @var <type> The PDO database handle
     */
    private $dbh;

    /**
     *
     * @var <type> The PDO statement handle
     */
    private $stmt;

    /**
     *
     * @var <type> The PDO result handle
     */
    private $result;

    /**
     *
     * @param <type> $dsn
     * @param <type> $user
     * @param <type> $pass
     */
    public function __construct($dsn,$user,$pass) {
        try {
            $this->dbh = new PDO($dsn, $user, $pass);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e)
        {
            echo $e->getMessage() . "$user -  $pass";exit;
        }
    }

    /**
     *
     * @param <type> $user
     * @param <type> $pass
     * @param <type> $dbname
     * @param <type> $host
     * @param <type> $port
     * @return DB
     */
    public static function getInstance($user=null,$pass=null,$dbname=null,$host='127.0.0.1',$port=3306)
    {
        if ($user === null)
        {
            $user = Config::$user;
            $pass = Config::$pass;
            $dbname= Config::$dbname;
			$host= Config::$host;
			$post= Config::$port;
        }
        
        $dsn = "mysql:host=$host;dbname=$dbname;port=$port";

        return new DB($dsn,$user,$pass);
    }

    /**
     *
     * @param <type> $sql
     * @return <type>
     */
    public function prepare($sql)
    {
        $this->stmt = $this->dbh->prepare($sql);
        return $this;
    }

    /**
     *
     */
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

    /**
     *
     * @param <type> $mode
     * @return <type>
     */
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
        try {
            $this->stmt = $this->dbh->query($sql);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $this;
    }

	/**
     * Exec a SQL Statement on the database.
     *
     * @param string $sql
     * @return <type>
     */
    public function exec($sql)
    {
        try {
            //$this->stmt = $this->dbh->exec($sql);
			$this->dbh->exec($sql) or die("Error executing the sql. errorInfo:" . print_r($this->dbh->errorInfo(), true) . "\nSQL statement: " . $sql);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $this;
    }

    /**
     *
     * @return <type>
     */
    public function getError()
    {
        return $this->stmt->errorInfo();
    }

    /**
     *
     * @return <type> 
     */
    public function getStatement()
    {
        return $this->stmt;
    }
}
