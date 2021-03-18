<?php 
class Notification{
    private $table = "notifications";
    private $conn;
    
    public $id;
    public $memberId;
    public $notification;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db){
        $this->conn = $db;
    }

    function readAllMemberNotification(){
        // Query 
        $query = "SELECT * FROM " . $this->table . " WHERE memberId =:memberId ORDER BY status ASC";
        
        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->memberId = htmlspecialchars(strip_tags($this->memberId));

        // bind data
        $stmt->bindParam(":memberId", $this->memberId);

        // execute statment
        $stmt->execute();

        return $stmt;
    }

    function readUnreadNotification(){
        // Query 
        $query = "SELECT * FROM " . $this->table . " WHERE memberId =:memberId AND status = 0";
        
        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->memberId = htmlspecialchars(strip_tags($this->memberId));

        // bind data
        $stmt->bindParam(":memberId", $this->memberId);

        // execute statment
        $stmt->execute();

        return $stmt;
    }

    function create(){
        // query
        $query = "INSERT INTO " . $this->table . " SET memberId =:memberId, notification =:notification";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->memberId = htmlspecialchars(strip_tags($this->memberId));
        $this->notification = htmlspecialchars(strip_tags($this->notification));

        // bind data
        $stmt->bindParam(":memberId", $this->memberId);
        $stmt->bindParam(":notification", $this->notification);

        // execute statment
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function changeStatus(){
        // query
        $query = "UPDATE " . $this->table . " SET status= 1 WHERE memberId=:memberId";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitise
        $this->memberId = htmlspecialchars(strip_tags($this->memberId));

        // bind data
        $stmt->bindParam(":memberId", $this->memberId);

        // execute
        if($stmt->execute()){
            return true;
        }

        return false;
    }
}

?>