<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods, Content-Type, Authorization, X-Requested-With");

// get database and member file
require_once "../../config/database.php";
require_once "../../models/event.php";

$database = Database::getInstance();
$event = new Event($database->getConnection());

// get send data
$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
if(
    !empty($data->name) && 
    !empty($data->startDateTime) && 
    !empty($data->endDateTime) &&
    !empty($data->categoryId) &&
    !empty($data->location) &&
    !empty($data->fee) &&
    !empty($data->description)
){
    // set member property values
    $event->name = $data->name;
    $event->startDateTime = $data->startDateTime;
    $event->endDateTime = $data->endDateTime;
    $event->categoryId = $data->categoryId;
    $event->location = $data->location;
    $event->fee = $data->fee;
    $event->description = $data->description;

    if(!empty($data->image)){
        $event->image = $data->image;
    }else{
        $event->image = "event.jpg";
    }

    try{
        $event->create();
        // tell the user
        echo json_encode(array("succ" => true, "msg" => "Event created successfully"));

    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to create Event. Something went wrong!", "error" => $e->getMessage()));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to create event. Incomplete Data"));
}

?>