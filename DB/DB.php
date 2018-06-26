<?php

require_once ('constants.php');

class Db {

	static private $instance = null;

	private $engine;
	private $host;
	private $database;
	private $user;
	private $pass;
	private $charset;

    public static function getInstance($recreate = false) {
        if (is_null(self::$instance) || $recreate) {
            $class = __CLASS__;
            self::$instance = new $class();
            self::$instance->getConnection();
            //echo "connect<br>";
        }else{
            //echo "NOT<br>";
        }
        //print_r(self::$instance);
        return self::$instance;
    }

    static function closeInstance() {
        self::$instance = null;
    }

    protected function connect($host, $dbname, $username, $password) {

        try {
            $this->dbh = new PDO('mysql:host=' . $host . ';dbname=' . $dbname . '', $username, $password);
            $this->dbh->exec("set names utf8");
        } catch (PDOException $ex) {
            echo "Failed to get DB handle: " . $ex->getMessage() . "\n";
            throw $ex;
        }
    }

    protected function getConnection() {
        $this->engine = 'mysql';
        $this->host = MYSQL_HOST_NAME;
        $this->database = MYSQL_DB_NAME;
        $this->user = MYSQL_USER_NAME;
        $this->pass = MYSQL_PASSWORD;
        try {
            if (!isset($this->dbh) || is_null($this->dbh)) {
                $this->connect($this->host, $this->database, $this->user, $this->pass);
            }
           
            return $this->dbh;
        } catch (DBException $ex) {
            throw $ex;
        }
    }

}
