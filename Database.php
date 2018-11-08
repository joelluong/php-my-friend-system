<?php
class Database{
	private $host	= 'localhost';
	private $user	= 'root';
	private $pass	= '200995';
	private $dbname	= 's101051766_db';

	private $dbh; // database handler
	private $error; // property for error
	private $stmt; // for statement property

    public function __construct(){
        // Set DSN: Data Source Name to connect to database
        $dsn = 'mysql:host='. $this->host . ';dbname='. $this->dbname;
        // Set Options
        $options = array(
            PDO::ATTR_PERSISTENT		=> true,
            PDO::ATTR_ERRMODE		=> PDO::ERRMODE_EXCEPTION
        );
        // Create new PDO
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch(PDOEception $e){
            $this->error = $e->getMessage();
        }
    }

    public function query($query){
        $this->stmt = $this->dbh->prepare($query);
    }

    public function bind($param, $value, $type = null){
        if(is_null($type)){
            switch(true){
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute(){
        return $this->stmt->execute();
    }

    public function lastInsertId(){
        $this->dbh->lastInsertId();
    }

    public function resultset(){
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if a table exists in the current database.
     * @param string $table Table to search for.
     * @return bool TRUE if table exists, FALSE if no table found.
     */
    public function tableExists($table) {
        // Try a select statement against the table
        // Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
        try {
            $this->query("SELECT 1 FROM $table LIMIT 1");
            $rows = $this->resultset();
        } catch (Exception $e) {
            // We got an exception == table not found
            return false;
        } 
        // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
		return $rows[0][1];
    }
}