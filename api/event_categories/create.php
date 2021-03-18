<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods, Content-Type, Authorization, X-Requested-With");

// get database and event_category file
require_once "../../config/database.php";
require_once "../../models/event_category.php";

$database = Database::getInstance();
$eventCategory = new EventCategory($database->getConnection());

// get send data
$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
if(
    !empty($data->name) && 
    !empty($data->description)
){
    // set category property values
    $eventCategory->name = $data->name;
    $eventCategory->description = $data->description;

    //check if member already exist 
    try{
        if($eventCategory->isUnique()){
            $eventCategory->create();
            // tell the user
            echo json_encode(array("succ" => true, "msg" => "Event category created successfully!"));
        }else{
            // tell the user category already exist
            echo json_encode(array("succ" => false, "msg" => "Unable to create category. Category already exist"));
        }
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to create category. Something went wrong!", "error" => $e->getMessage()));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to create category. Incomplete Data"));
}

?>