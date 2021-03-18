<?php 
class Chat{
    private $table = "chats";
    private $conn;
    
    public $id;
    public $matchedFriendId;
    public $sender;
    public $receiver;
    public $message;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db){
        $this->conn = $db;
    }

    function readMatchedFriendsChat(){
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

    function readMemberChats(){
        // Query 
        $query = "SELECT DISTINCT * FROM " . $this->table . " 
                    WHERE sender =:memberId OR receiver =:memberId";
        
        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitise
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind param
        $stmt->bindParam(":memberId", $this->id);

        // execute statment
        $stmt->execute();

        return $stmt;
    }

    function readOne(){
        // Query 
        $query = "SELECT FROM " . $this->table . " WHERE id =:id";
        
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
        $query = "INSERT INTO " . $this->table . " SET matchedFriendId =:matchedFriendId, 
                sender =:sender, receiver =:receiver, message =:message";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->matchedFriendId = htmlspecialchars(strip_tags($this->matchedFriendId));
        $this->sender = htmlspecialchars(strip_tags($this->sender));
        $this->receiver = htmlspecialchars(strip_tags($this->receiver));
        $this->message = htmlspecialchars(strip_tags($this->message));

        // bind data
        $stmt->bindParam(":matchedFriendId", $this->matchedFriendId);
        $stmt->bindParam(":sender", $this->sender);
        $stmt->bindParam(":receiver", $this->receiver);
        $stmt->bindParam(":message", $this->message);

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
        $chat = $this->readOne()->fetchAll()[0];

        if($chat['status'] == 0){
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
        }else{
            $this->status = 2;
            return $this->changeStatus();
        }
    }
}

?>