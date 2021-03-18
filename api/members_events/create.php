<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods, Content-Type, Authorization, X-Requested-With");

// get database and member_event file
require_once "../../config/database.php";
require_once "../../models/member_event.php";

$database = Database::getInstance();
$memberEvent = new MemberEvent($database->getConnection());

// get send data
$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
if(
    !empty($data->memberId) && 
    !empty($data->eventId)
){
    // set member event property values
    $memberEvent->memberId = $data->memberId;
    $memberEvent->eventId = $data->eventId;

    // create memberEvent
    try{
        $memberEvent->create();
        // tell the user
        echo json_encode(array("succ" => true, "msg" => "Event attended successfully!"));
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to create member event. Something went wrong!", "error" => $e->getMessage()));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to create member event. Incomplete Data"));
}

?>