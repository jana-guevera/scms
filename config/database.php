<?php 
class Database{
    // database credentials
    private $host = "localhost";
    private $db_name = "friends_finder_db";
    private $username = "root";
    private $password = "";

    private static $instance;
    private $conn;

    private function __construct(){
        try{
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
            echo "Connection Error: " . $e->getMessage();
        }
    }

    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function getConnection(){
        return $this->conn;
    }
}
?>