<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and member_event file
require_once "../../config/database.php";
require_once "../../models/member_event.php";

$database = Database::getInstance();
$memberEvent = new MemberEvent($database->getConnection());

// get send data
$data = json_decode(file_get_contents("php://input"));

// check if data exist
if(isset($_GET['id'])){
    $memberEvent->id = $_GET['id'];

    try{
        if($memberEvent->remove()){
            // tell the user
            echo json_encode(array("succ" => true, "msg" => "Event cancelled successfully."));
        }
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to cancel the event. Something went wrong!", "error" => $e->getMessage()));
    }

}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to cancel the event. Incomplete Data"));
}
?>