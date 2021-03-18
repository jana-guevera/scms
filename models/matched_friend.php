<?php 
class MatchedFriend{
    private $table = "matched_friends";
    private $conn;
    
    public $id;
    public $memberId; //is the one sending friend request
    public $friendId; // is the one receiving friend request
    public $isGoodMatchRequester;
    public $isGoodMatchRecevier;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db){
        $this->conn = $db;
    }

    function readMemberFriends(){
        // Query 
        $query = "SELECT * FROM " . $this->table . " WHERE memberId =:memberId OR friendId =:memberId";
        
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

    function readUnsuccessfulMatches(){
        // Query 
        $query = "SELECT * FROM " . $this->table. " WHERE isGoodMatchRequester = 0 OR isGoodMatchRecevier = 0";
        
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

    function readSuccessfulMatches(){
        // Query 
        $query = "SELECT * FROM " . $this->table. " WHERE isGoodMatchRequester = 1 OR isGoodMatchRecevier = 1";
        
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
        $query = "SELECT * FROM " . $this->table . " WHERE id =:id";
                    
        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitise
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind param
        $stmt->bindParam(":id", $this->id);

        $stmt->execute();

        return $stmt;
    }

    function create(){
        // query
        $query = "INSERT INTO " . $this->table . " SET memberId =:memberId, friendId =:friendId";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // bind data
        $stmt->bindParam(":memberId", $this->memberId);
        $stmt->bindParam(":friendId", $this->friendId);

        // execute statment
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function provideFeedback($memberId, $feedback){
        $friendship = $this->readOne()->fetchAll()[0];
        $query = "";

        if($friendship['memberId'] == $memberId){
            $query = "UPDATE " . $this->table . " SET isGoodMatchRequester=:feedback WHERE id=:id";
        }else{
            $query = "UPDATE " . $this->table . " SET isGoodMatchRecevier=:feedback WHERE id=:id";
        }

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitise
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind data
        $stmt->bindParam(":feedback", $feedback);
        $stmt->bindParam(":id", $this->id);

        // execute
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

    function isFriendBefore(){
        $query = "SELECT id FROM ".$this->table." WHERE (memberId =:memberId && friendId =:friendId) OR 
                            (memberId =:friendId && friendId =:memberId)";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->memberId = htmlspecialchars(strip_tags($this->memberId));
        $this->friendId = htmlspecialchars(strip_tags($this->friendId));

        // bind data
        $stmt->bindParam(":memberId", $this->memberId);
        $stmt->bindParam(":friendId", $this->friendId);

        $stmt->execute();
        $num = $stmt->rowCount();

        if($num == 1){
            return true;
        }
        
        return false;
    }

    function cancelRequest(){
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