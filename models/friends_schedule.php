<?php 
class FriendsSchedule{
    private $table = "friends_schedules";
    private $conn;
    
    public $id;
    public $scheduleCreaterId;
    public $scheduleReceiverId;
    public $startDateTime;
    public $endDateTime;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db){
        $this->conn = $db;
    }

    function readMemberSchedulesWithFriend(){
        // Query 
        $query = "SELECT * FROM " . $this->table . " 
                    WHERE matchedFriendId =:matchedFriendId";
        
        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitise
        $this->matchedFriendId = htmlspecialchars(strip_tags($this->matchedFriendId));

        // bind param
        $stmt->bindParam(":matchedFriendId", $this->matchedFriendId);

        // execute statment
        $stmt->execute();

        return $stmt;
    }

    function readMemberSchedules($memberId){
        // Query 
        $query = "SELECT DISTINCT * FROM " . $this->table . " 
                    WHERE scheduleCreaterId =:memberId OR scheduleReceiverId =:memberId";
        
        // prepare query
        $stmt = $this->conn->prepare($query);

        // bind param
        $stmt->bindParam(":memberId", $memberId);

        // execute statment
        $stmt->execute();

        return $stmt;
    }

    function readOne(){
        // Query 
        $query = "SELECT * FROM " . $this->table . " WHERE id =:id";
        
        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitise
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind param
        $stmt->bindParam(":id", $this->id);

        // execute statment
        $stmt->execute();

        return $stmt;
    }

    function create(){
        // query
        $query = "INSERT INTO " . $this->table . " SET scheduleCreaterId =:scheduleCreaterId, 
                scheduleReceiverId =:scheduleReceiverId, startDateTime =:startDateTime, endDateTime =:endDateTime";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->scheduleCreaterId = htmlspecialchars(strip_tags($this->scheduleCreaterId));
        $this->scheduleReceiverId = htmlspecialchars(strip_tags($this->scheduleReceiverId));
        $this->startDateTime = htmlspecialchars(strip_tags($this->startDateTime));
        $this->endDateTime = htmlspecialchars(strip_tags($this->endDateTime));

        // bind data
        $stmt->bindParam(":scheduleCreaterId", $this->scheduleCreaterId);
        $stmt->bindParam(":scheduleReceiverId", $this->scheduleReceiverId);
        $stmt->bindParam(":startDateTime", $this->startDateTime);
        $stmt->bindParam(":endDateTime", $this->endDateTime);

        // execute statment
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function update(){
        // query
        $query = "UPDATE " . $this->table . " SET startDateTime =:startDateTime, endDateTime =:endDateTime
                status = 0, WHERE id =:id";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->startDateTime = htmlspecialchars(strip_tags($this->startDateTime));
        $this->endDateTime = htmlspecialchars(strip_tags($this->endDateTime));

        // bind data
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":startDateTime", $this->startDateTime);
        $stmt->bindParam(":endDateTime", $this->endDateTime);

        // execute statment
        if($stmt->execute()){
            return true;
        }

        return false;
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

    function remove(){
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
?>