<?php 
class EventCategory{
    private $table = "event_categories";
    private $conn;
    
    public $id;
    public $name;
    public $description;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db){
        $this->conn = $db;
    }

    function readAll(){
        // Query 
        $query = "SELECT * FROM " . $this->table . " WHERE status != 2";
        
        // prepare query
        $stmt = $this->conn->prepare($query);

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
        $query = "INSERT INTO " . $this->table . " SET name =:name, description =:description";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // bind data
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);

        // execute statment
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function update(){
        // query
        $query = "UPDATE " . $this->table . " SET name =:name, description =:description,  
                   status =:status WHERE id =:id";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // bind data
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":status", $this->status);

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

    function isUnique(){
         // query 
         $query = "SELECT id FROM " . $this->table . " WHERE LOWER(name)=LOWER(:name)";

         // prepare statement
         $stmt = $this->conn->prepare($query);
 
         // clean data
         $this->name = htmlspecialchars(strip_tags($this->name));
 
         // bind data
         $stmt->bindParam(":name", $this->name);
 
         // execute stament
         $stmt->execute();
         $num = $stmt->rowCount();
      
         if($num < 1){
             return true;
         }
 
         return false;
    }

    function isUniqueOnUpdate(){
         // query 
         $query = "SELECT id FROM " . $this->table . " WHERE LOWER(name)=LOWER(:name) && id !=:id";

         // prepare statement
         $stmt = $this->conn->prepare($query);
 
         // clean data
         $this->name = htmlspecialchars(strip_tags($this->name));
         $this->id = htmlspecialchars(strip_tags($this->id));
 
         // bind data
         $stmt->bindParam(":name", $this->name);
         $stmt->bindParam(":id", $this->id);
 
         // execute stament
         $stmt->execute();
         $num = $stmt->rowCount();
 
         if($num < 1){
             return true;
         }
 
         return false;
    }

    function isRemovable(){
        $query = "SELECT id FROM events WHERE categoryId =:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);

        $stmt->execute();
        $num = $stmt->rowCount();

        if($num < 1){
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
}
?>