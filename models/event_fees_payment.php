<?php 
class EventFeesPayment{
    private $table = "event_fees_payments";
    private $conn;
    
    public $id;
    public $monthId;
    public $memberId;
    public $eventId;
    public $amount;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db){
        $this->conn = $db;
    }

    function create(){
        // query
        $query = "INSERT INTO " . $this->table . " SET monthId =:monthId, memberId =:memberId,
                   eventId =:eventId, amount =:amount";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->monthId = htmlspecialchars(strip_tags($this->monthId));
        $this->memberId = htmlspecialchars(strip_tags($this->memberId));
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        $this->eventId = htmlspecialchars(strip_tags($this->eventId));

        // bind data
        $stmt->bindParam(":monthId", $this->monthId);
        $stmt->bindParam(":memberId", $this->memberId);
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(":eventId", $this->eventId);

        // execute statment
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function readAll(){
        // Query 
        $query = "SELECT ep.*, m.month FROM " . $this->table. " ep 
                    JOIN months m ON m.id = ep.monthId
                    WHERE memberId =:memberId";
        
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

    function readAllOnEventId(){
        // Query 
        $query = "SELECT ep.*, m.month FROM " . $this->table. " ep 
        JOIN months m ON m.id = ep.monthId
        WHERE eventId =:eventId";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->eventId = htmlspecialchars(strip_tags($this->eventId));

        // bind data
        $stmt->bindParam(":eventId", $this->eventId);

        // execute statment
        $stmt->execute();

        return $stmt;
    }

    function readOnMonthAndMember(){
         // Query 
         $query = "SELECT ep.*, m.month, e.name FROM " . $this->table. " ep 
         JOIN months m ON m.id = ep.monthId 
         JOIN events e ON e.id = ep.eventId 
         WHERE memberId =:memberId AND monthId =:monthId";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // bind data
        $stmt->bindParam(":memberId", $this->memberId);
        $stmt->bindParam(":monthId", $this->monthId);

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