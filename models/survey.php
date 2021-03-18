<?php 
class Survey{
    private $table = "survey";
    private $conn;
    
    public $id;
    public $question;
    public $categoryId;
    public $inputType;
    public $answers;
    public $created_at;
    public $updated_at;

    public function __construct($db){
        $this->conn = $db;
    }

    function create(){
        // query
        $query = "INSERT INTO " . $this->table . " SET inputType =:inputType, question =:question, 
                    answers =:answers, categoryId =:categoryId";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->inputType = htmlspecialchars(strip_tags($this->inputType));
        $this->question = htmlspecialchars(strip_tags($this->question));
        $this->answers = htmlspecialchars(strip_tags($this->answers));
        $this->categoryId = htmlspecialchars(strip_tags($this->categoryId));

        // bind data
        $stmt->bindParam(":inputType", $this->inputType);
        $stmt->bindParam(":question", $this->question);
        $stmt->bindParam(":answers", $this->answers);
        $stmt->bindParam(":categoryId", $this->categoryId);

        // execute statment
        if($stmt->execute()){
            return $this->conn->lastInsertId();
        }

        return 0;
    }

    function readAll(){
        // Query 
        $query = "SELECT s.*, sc.name as categoryName FROM " . $this->table. " s
                    JOIN survey_categories sc ON sc.id = s.categoryId";
        
        // prepare query
        $stmt = $this->conn->prepare($query);

        // execute statment
        $stmt->execute();

        return $stmt;
    }

    function readOne(){
        // query
        $query = "SELECT s.*, sc.name as categoryName FROM " . $this->table. " s
                    JOIN survey_categories sc ON sc.id = s.categoryId 
                    WHERE s.id =:id";
                    
        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitise
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind param
        $stmt->bindParam(":id", $this->id);

        $stmt->execute();

        return $stmt;
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