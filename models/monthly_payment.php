<?php 
class MonthlyPayment{
    private $table = "monthly_payments";
    private $conn;
    
    public $id;
    public $monthId;
    public $month;
    public $memberId;
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
                    amount =:amount";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->monthId = htmlspecialchars(strip_tags($this->monthId));
        $this->memberId = htmlspecialchars(strip_tags($this->memberId));
        $this->amount = htmlspecialchars(strip_tags($this->amount));

        // bind data
        $stmt->bindParam(":monthId", $this->monthId);
        $stmt->bindParam(":memberId", $this->memberId);
        $stmt->bindParam(":amount", $this->amount);

        // execute statment
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function readAll(){
        // Query 
        $query = "SELECT mp.*, month FROM " . $this->table. " mp 
                    JOIN months m ON m.id = mp.monthId 
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

    function getMemberPaymentWithMonth(){
        // query
        $query = "SELECT mp.*, m.month FROM " . $this->table . " mp 
                    JOIN months m ON m.id = mp.monthId
                    WHERE mp.memberId =:memberId AND mp.monthId =:monthId";
                    
        // prepare query
        $stmt = $this->conn->prepare($query);

        // bind param
        $stmt->bindParam(":memberId", $this->memberId);
        $stmt->bindParam(":monthId", $this->monthId);

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