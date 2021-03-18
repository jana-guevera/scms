<?php 

class Member{
    private $table = "members";
    private $conn;
    
    public $id;
    public $name;
    public $NIC;
    public $gender;
    public $email;
    public $mobileNo;
    public $address;
    public $password;
    public $memberType;
    public $image;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db){
        $this->conn = $db;
    }

    function login(){
        // query
        $query = "SELECT * FROM " . $this->table . " WHERE LOWER(email)=LOWER(:email) && password =:password";
                    
        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitise
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));

        // bind param
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);

        $stmt->execute();

        return $stmt;
    }

    function updateUserProfile(){
        // query
        $query = "";

        if($this->image != null){
            $query = "UPDATE " . $this->table . "
                    SET name ='{$this->name}', mobileNo = '{$this->mobileNo}', image = '{$this->image}',
                    address = '{$this->address}', gender = '{$this->gender}'
                    WHERE id = '{$this->id}'";
        }else{
            $query = "UPDATE " . $this->table . "
                    SET name ='{$this->name}', mobileNo = '{$this->mobileNo}', 
                    address = '{$this->address}', gender = '{$this->gender}'
                    WHERE id = '{$this->id}'";
        }

        // prepare query
        $stmt = $this->conn->prepare($query);

        // execute statment
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function readOfflineMembers(){
        // Query 
        $query = "SELECT * FROM " . $this->table . " WHERE memberType = 0 && status != 3";
        
        // prepare query
        $stmt = $this->conn->prepare($query);

        // execute statment
        $stmt->execute();

        return $stmt;
    }

    function readOnlineMembers(){
        // Query 
        $query = "SELECT * FROM " . $this->table . " WHERE memberType = 1 && status != 3";
        
        // prepare query
        $stmt = $this->conn->prepare($query);

        // execute statment
        $stmt->execute();

        return $stmt;
    }

    function searchOfflineMembers($searchText){
        // Query 
        $query = "SELECT DISTINCT m.* FROM " . $this->table . " m 
                    JOIN member_survey ms ON ms.memberId = m.id 
                    WHERE m.id != :id && status = 1 && memberType = 0 && 
                    (
                        m.name LIKE '%{$searchText}%' OR m.gender LIKE '%{$searchText}%' 
                        OR ms.answers LIKE '%{$searchText}%'
                    )";
        
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

    function searchOnlineMembers($searchText){
        // Query 
        $query = "SELECT DISTINCT m.* FROM " . $this->table . " m 
                    JOIN member_survey ms ON ms.memberId = m.id 
                    WHERE m.id != :id && status = 1 && memberType = 1 && 
                    (
                        m.name LIKE '%{$searchText}%' OR m.gender LIKE '%{$searchText}%' 
                        OR ms.answers LIKE '%{$searchText}%'
                    )";
        
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

    function readAll(){
        // Query 
        $query = "SELECT * FROM " . $this->table. " WHERE status = 1";
        
        // prepare query
        $stmt = $this->conn->prepare($query);

        // execute statment
        $stmt->execute();

        return $stmt;
    }

    function readOne(){
        // query
        $query = "SELECT * FROM " . $this->table . " WHERE LOWER(id)=LOWER(:id)";
                    
        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitise
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind param
        $stmt->bindParam(":id", $this->id);

        $stmt->execute();

        return $stmt;
    }

    function getSingleRecord($memberId){
        // query
        $query = "SELECT * FROM " . $this->table . " WHERE LOWER(id)=LOWER(:id)";
                    
        // prepare query
        $stmt = $this->conn->prepare($query);

        // bind param
        $stmt->bindParam(":id", $memberId);

        $stmt->execute();

        return $stmt;
    }

    function create(){
        // query
        $query = "INSERT INTO " . $this->table . "
                    SET id =:id, name =:name, NIC =:NIC, email =:email, 
                    mobileNo =:mobileNo, address =:address, gender =:gender, 
                    memberType =:memberType, image =:image";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->NIC = htmlspecialchars(strip_tags($this->NIC));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->mobileNo = htmlspecialchars(strip_tags($this->mobileNo));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->memberType = htmlspecialchars(strip_tags($this->memberType));
        $this->image = htmlspecialchars(strip_tags($this->image));

        // bind data
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":NIC", $this->NIC);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":mobileNo", $this->mobileNo);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":memberType", $this->memberType);
        $stmt->bindParam(":image", $this->image);

        // execute statment
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function createOnlineMember(){
        // query
        $query = "INSERT INTO " . $this->table . "
                    SET id =:id, name =:name, NIC =:NIC, email =:email, 
                    mobileNo =:mobileNo, address =:address, gender =:gender, 
                    memberType =:memberType, image =:image, password =:password";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->NIC = htmlspecialchars(strip_tags($this->NIC));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->mobileNo = htmlspecialchars(strip_tags($this->mobileNo));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->memberType = htmlspecialchars(strip_tags($this->memberType));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->password = htmlspecialchars(strip_tags($this->password));

        // bind data
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":NIC", $this->NIC);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":mobileNo", $this->mobileNo);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":memberType", $this->memberType);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":password", $this->password);

        // execute statment
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function update(){
        // query
        $query = "UPDATE " . $this->table . "
                    SET name =:name, NIC =:NIC, email =:email, 
                    mobileNo =:mobileNo, address =:address, gender =:gender,
                    memberType =:memberType, image =:image, status =:status 
                    WHERE id =:id";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->NIC = htmlspecialchars(strip_tags($this->NIC));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->mobileNo = htmlspecialchars(strip_tags($this->mobileNo));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->memberType = htmlspecialchars(strip_tags($this->memberType));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // bind data
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":NIC", $this->NIC);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":mobileNo", $this->mobileNo);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":memberType", $this->memberType);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":status", $this->status);

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

    function isNICUnique(){
        // query 
        $query = "SELECT id FROM " . $this->table . " WHERE LOWER(NIC)=LOWER(:nic)";

        // prepare statement
        $stmt = $this->conn->prepare($query);

        // clean data
        $this->NIC = htmlspecialchars(strip_tags($this->NIC));

        // bind data
        $stmt->bindParam(":nic", $this->NIC);

        // execute stament
        $stmt->execute();
        $num = $stmt->rowCount();
     
        if($num < 1){
            return true;
        }

        return false;
    }

    function isEmailUnique(){
        // query 
        $query = "SELECT id FROM " . $this->table . " WHERE LOWER(email)=LOWER(:email)";

        // prepare statement
        $stmt = $this->conn->prepare($query);

        // clean data
        $this->email = htmlspecialchars(strip_tags($this->email));

        // bind data
        $stmt->bindParam(":email", $this->email);

        // execute stament
        $stmt->execute();
        $num = $stmt->rowCount();

        if($num < 1){
            return true;
        }
        
        return false;
    }

    function isNICUniqueOnUpdate(){
        // query 
        $query = "SELECT id FROM " . $this->table . " WHERE LOWER(NIC)=LOWER(:NIC) && id !=:id";

        // prepare statement
        $stmt = $this->conn->prepare($query);

        // clean data
        $this->NIC = htmlspecialchars(strip_tags($this->NIC));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind data
        $stmt->bindParam(":NIC", $this->NIC);
        $stmt->bindParam(":id", $this->id);

        // execute stament
        $stmt->execute();
        $num = $stmt->rowCount();

        if($num < 1){
            return true;
        }

        return false;
    }

    function isEmailUniqueOnUpdate(){
        // query 
        $query = "SELECT id FROM " . $this->table . " WHERE LOWER(email)=LOWER(:email) && id !=:id";

        // prepare statement
        $stmt = $this->conn->prepare($query);

        // clean data
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind data
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":id", $this->id);

        // execute stament
        $stmt->execute();
        $num = $stmt->rowCount();

        if($num < 1){
            return true;
        }

        return false;
    }
}

?>