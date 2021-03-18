<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and event file
require_once "../../config/database.php";
require_once "../../models/event.php";

$database = Database::getInstance();
$event = new Event($database->getConnection());

// check if id exist
if(isset($_GET['id'])){
    $event->id = $_GET['id'];
    $event->status = 0;

    try{
        $event->changeStatus();
        echo json_encode(array("succ" => true, "msg" => "Event cancelled successfully!"));
        
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to cancel event. Something went wrong!", "error" => $e->getMessage()));
    }

}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to cancel event. Incomplete Data"));
}
?>