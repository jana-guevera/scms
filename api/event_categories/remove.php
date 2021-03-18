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

    try{
        if($eventCategory->remove()){
            // tell the user
            echo json_encode(array("succ" => true, "msg" => "Event category removed successfully!"));
        }
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to remove category. Something went wrong!", "error" => $e->getMessage()));
    }

}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to remove category. Incomplete Data"));
}
?>