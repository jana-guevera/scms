<?php 
class Month{
    private $table = "months";
    private $conn;
    
    public $id;
    public $month;
    public $created_at;
    public $updated_at;

    public function __construct($db){
        $this->conn = $db;
    }

    function create(){
        // query
        $query = "INSERT INTO " . $this->table . " SET month =:month";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->month = htmlspecialchars(strip_tags($this->month));

        // bind data
        $stmt->bindParam(":month", $this->month);

        // execute statment
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function readAll(){
        // Query 
        $query = "SELECT * FROM " . $this->table;
        
        // prepare query
        $stmt = $this->conn->prepare($query);

        // execute statment
        $stmt->execute();

        return $stmt;
    }

    function readOne(){
        // query
        $query = "SELECT * FROM " . $this->table . " WHERE month =:month";
                    
        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitise
        $this->month = htmlspecialchars(strip_tags($this->month));

        // bind param
        $stmt->bindParam(":month", $this->month);

        $stmt->execute();

        return $stmt;
    }
}

?>