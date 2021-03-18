<?php 
class Event{
    private $table = "events";
    private $conn;
    
    public $id;
    public $name;
    public $startDateTime;
    public $endDateTime;
    public $categoryId;
    public $location;
    public $fee;
    public $description;
    public $image;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db){
        $this->conn = $db;
    }

    function readAll(){
        // Query 
        $query = "SELECT e.*, c.name AS categoryName FROM " . $this->table . " e 
                    JOIN event_categories c ON c.id = e.categoryId WHERE e.status != 2";
        
        // prepare query
        $stmt = $this->conn->prepare($query);

        // execute statment
        $stmt->execute();

        return $stmt;
    }

    function readUpcomingEvents(){
        // Query 
        $query = "SELECT e.*, c.name AS categoryName FROM " . $this->table . " e 
                    JOIN event_categories c ON c.id = e.categoryId 
                    WHERE e.status = 1 && e.startDateTime >= NOW()";
        
        // prepare query
        $stmt = $this->conn->prepare($query);

        // execute statment
        $stmt->execute();

        return $stmt;
    }

    function searchUpcomingEvents($searchText){
        // Query 
        $query = "SELECT e.*, c.name AS categoryName FROM " . $this->table . " e 
                    JOIN event_categories c ON c.id = e.categoryId 
                    WHERE (e.status = 1 && e.startDateTime >= NOW()) && 
                    (
                        e.name LIKE '%{$searchText}%' OR e.description LIKE '%{$searchText}%' 
                        OR e.location LIKE '%{$searchText}%' OR c.name LIKE '%{$searchText}%'
                    )";
        
        // prepare query
        $stmt = $this->conn->prepare($query);

        // execute statment
        $stmt->execute();

        return $stmt;
    }


    function readActive(){
        // Query 
        $query = "SELECT e.*, c.name AS categoryName FROM " . $this->table . " e 
                    JOIN event_categories c ON c.id = e.categoryId WHERE status = 1";
        
        // prepare query
        $stmt = $this->conn->prepare($query);

        // execute statment
        $stmt->execute();

        return $stmt;
    }

    function readActiveCompletedEvents(){
        // Query 
        $query = "SELECT e.*, c.name AS categoryName FROM " . $this->table . " e 
                    JOIN event_categories c ON c.id = e.categoryId 
                    WHERE e.status = 1 && e.endDateTime <= NOW()";
        
        // prepare query
        $stmt = $this->conn->prepare($query);

        // execute statment
        $stmt->execute();

        return $stmt;
    }

    function readAttendingMembers(){
        // Query 
        $query = "SELECT * FROM member_events WHERE eventId =:id";
        
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

    function readOne(){
        // query
        $query = "SELECT e.*, c.name AS categoryName FROM " . $this->table . " e 
                    JOIN event_categories c ON c.id = e.categoryId WHERE e.id =:id";
                    
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
        $query = "INSERT INTO " . $this->table . "
                    SET name =:name, startDateTime =:startDateTime, endDateTime =:endDateTime, 
                    categoryId =:categoryId, location =:location, fee =:fee, 
                    image =:image, description =:description";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->startDateTime = htmlspecialchars(strip_tags($this->startDateTime));
        $this->endDateTime = htmlspecialchars(strip_tags($this->endDateTime));
        $this->categoryId = htmlspecialchars(strip_tags($this->categoryId));
        $this->location = htmlspecialchars(strip_tags($this->location));
        $this->fee = htmlspecialchars(strip_tags($this->fee));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // bind data
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":startDateTime", $this->startDateTime);
        $stmt->bindParam(":endDateTime", $this->endDateTime);
        $stmt->bindParam(":categoryId", $this->categoryId);
        $stmt->bindParam(":location", $this->location);
        $stmt->bindParam(":fee", $this->fee);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":description", $this->description);

        // execute statment
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function update(){
        // query
        $query = "UPDATE " . $this->table . "
                    SET name =:name, startDateTime =:startDateTime, endDateTime =:endDateTime,
                    location =:location, fee =:fee, image =:image, description =:description
                    WHERE id =:id";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->startDateTime = htmlspecialchars(strip_tags($this->startDateTime));
        $this->endDateTime = htmlspecialchars(strip_tags($this->endDateTime));
        $this->location = htmlspecialchars(strip_tags($this->location));
        $this->fee = htmlspecialchars(strip_tags($this->fee));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // bind data
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":startDateTime", $this->startDateTime);
        $stmt->bindParam(":endDateTime", $this->endDateTime);
        $stmt->bindParam(":location", $this->location);
        $stmt->bindParam(":fee", $this->fee);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":description", $this->description);

        // execute statment
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function remove(){
        if($this->isRemovable()){
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

    function isRemovable(){
        $query = "SELECT id FROM member_events WHERE eventId =:eventId";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":eventId", $this->id);

        $stmt->execute();
        $num = $stmt->rowCount();

        if($num < 1){
            return true;
        }
        
        return false;
    }
}
?>