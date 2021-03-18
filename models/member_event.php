<?php 
class MemberEvent{
    private $table = "member_events";
    private $conn;
    
    public $id;
    public $memberId;
    public $eventId;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db){
        $this->conn = $db;
    }

    function readMemberEvents(){
        // Query 
        $query = "SELECT * FROM " . $this->table . " WHERE memberId =:memberId";
        
        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitise
        $this->memberId = htmlspecialchars(strip_tags($this->memberId));

        // bind param
        $stmt->bindParam(":memberId", $this->memberId);

        // execute statment
        $stmt->execute();

        return $stmt;
    }

    function readOne(){
        // query
        $query = "SELECT me.*, e.fee FROM " . $this->table . " me 
                    JOIN events e ON e.id = me.eventId 
                    WHERE me.id =:id";
                    
        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitise
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind param
        $stmt->bindParam(":id", $this->id);

        $stmt->execute();

        return $stmt;
    }

    function readAllAttedingEvent(){
        // Query 
        $query = "SELECT me.* FROM " . $this->table . " me 
                    JOIN events e ON e.id = me.eventId 
                    WHERE me.status = 0 AND e.endDateTime <= NOW()";
    
        // prepare query
        $stmt = $this->conn->prepare($query);

        // execute statment
        $stmt->execute();

        return $stmt;
    }

    function create(){
        // query
        $query = "INSERT INTO " . $this->table . " SET memberId =:memberId, eventId =:eventId";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->memberId = htmlspecialchars(strip_tags($this->memberId));
        $this->eventId = htmlspecialchars(strip_tags($this->eventId));

        // bind data
        $stmt->bindParam(":memberId", $this->memberId);
        $stmt->bindParam(":eventId", $this->eventId);

        // execute statment
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function remove(){
        $event = $this->readOne()->fetchAll()[0];

        if($event['status'] == 0){
            // query
            $query = "DELETE FROM " .$this->table. " WHERE id =:id";

            // prepare query
            $stmt = $this->conn->prepare($query);

            // sanitise
            $this->id = htmlspecialchars(strip_tags($this->id));

            // bind data
            $stmt->bindParam(":id", $this->id);

            // execute
            if($stmt->execute()){
                return true;
            }

            return false;
        }
        
    }

    function readAttendingMembers(){
         // query
         $query = "SELECT * FROM " . $this->table . " WHERE eventId =:eventId";
                    
         // prepare query
         $stmt = $this->conn->prepare($query);
 
         // sanitise
         $this->eventId = htmlspecialchars(strip_tags($this->eventId));
 
         // bind param
         $stmt->bindParam(":eventId", $this->eventId);
 
         $stmt->execute();
 
         return $stmt;
    }

    function changeStatus(){
        // query
        $query = "UPDATE " . $this->table . " SET status=:status WHERE id=:id";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitise
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // bind data
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);

        // execute
        if($stmt->execute()){
            return true;
        }

        return false;
    }
}

?>