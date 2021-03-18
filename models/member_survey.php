<?php 
class MemberSurvey{
    private $table = "member_survey";
    private $conn;
    
    public $id;
    public $memberId;
    public $surveyId;
    public $answers;
    public $created_at;
    public $updated_at;

    public function __construct($db){
        $this->conn = $db;
    }

    function create(){
        // query
        $query = "INSERT INTO " . $this->table . " SET memberId =:memberId, surveyId =:surveyId";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->memberId = htmlspecialchars(strip_tags($this->memberId));
        $this->surveyId = htmlspecialchars(strip_tags($this->surveyId));

        // bind data
        $stmt->bindParam(":memberId", $this->memberId);
        $stmt->bindParam(":surveyId", $this->surveyId);

        // execute statment
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function createMultiple($survey_array){
        // query
        $query = "";

        for($i = 0; $i < count($survey_array); $i++){
            $surveyId = $survey_array[$i]['id'];
            $query .= "INSERT INTO " . $this->table . " SET memberId = '{$this->memberId}', 
            surveyId = {$surveyId} ;";
        }
        
        // prepare query
        $stmt = $this->conn->prepare($query);

        // execute statment
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function createForMultipleMembers($member_array, $surveyId){
        // query
        $query = "";

        for($i = 0; $i < count($member_array); $i++){
            $memberId = $member_array[$i]['id'];
            $query .= "INSERT INTO " . $this->table . " SET memberId = '{$memberId}', 
            surveyId = {$surveyId} ;";
        }
        
        // prepare query
        $stmt = $this->conn->prepare($query);

        // execute statment
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function readAllMemberSurvey(){
        // Query 
        $query = "SELECT * FROM " . $this->table. " WHERE memberId =:memberId";
        
        // prepare query
        $stmt = $this->conn->prepare($query);

        $this->memberId = htmlspecialchars(strip_tags($this->memberId));

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

    function answerSurvey($answerArr){
        // query
        $query = "";

        for($i = 0; $i < count($answerArr); $i++){
            $surveyId = $answerArr[$i]['surveyId'];
            $memberId = $answerArr[$i]['memberId'];

            $query .= "UPDATE " . $this->table . " SET answers ='{$answerArr[$i]['answers']}' 
                    WHERE memberId = '{$memberId}' AND surveyId ={$surveyId} ;";
        }

        // prepare query
        $stmt = $this->conn->prepare($query);

        // execute statment
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

    function removeAll(){
        // query
        $query = "DELETE FROM " .$this->table. " WHERE surveyId =:surveyId";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitise
        $this->surveyId = htmlspecialchars(strip_tags($this->surveyId));

        // bind data
        $stmt->bindParam(":surveyId", $this->surveyId);

        // execute
        if($stmt->execute()){
            return true;
        }

        return false;
    }
}

?>