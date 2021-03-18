<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and eventCategory file
require_once "../../config/database.php";
require_once "../../models/event_category.php";

$database = Database::getInstance();
$eventCategory = new EventCategory($database->getConnection());

// check if id exist
if(isset($_GET['id'])){
    $eventCategory->id = $_GET['id'];

    $stmt = $eventCategory->readOne();
    $num = $stmt->rowCount();

    // cehck if a eventCategory exist
    if($num == 1){
        // retrieve data
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        extract($row);

        $category_item = array(
            "id" => $id,
            "name" => $name,
            "description" => $description,
            "status" => $status,
            "created_at" => $created_at,
            "updated_at" => $updated_at
        );

        // send eventCategory data in json format
        echo json_encode(array("succ" => true, "record" => $category_item));
    }else{
        // tell the user no members were found
        echo json_encode(array("succ" => true, 'msg' => "No event category were found"));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to fetch event category. Incomplete Data"));
}
