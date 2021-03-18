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
    !empty($data->id) && 
    !empty($data->name) && 
    !empty($data->description) && 
    isset($data->status)
){
    // set category property values
    $eventCategory->id = $data->id;
    $eventCategory->name = $data->name;
    $eventCategory->description = $data->description;
    $eventCategory->status = $data->status;

    //check if category already exist 
    try{
        if($eventCategory->isUniqueOnUpdate()){
            $eventCategory->update();
            // tell the user
            echo json_encode(array("succ" => true, "msg" => "Event category updated successfully!"));
        }else{
            // tell the user category already exist
            echo json_encode(array("succ" => false, "msg" => "Unable to update category. Category already exist"));
        }
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to update category. Something went wrong!", "error" => $e->getMessage()));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to update category. Incomplete Data"));
}

?>