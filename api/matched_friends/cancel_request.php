<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and member_event file
require_once "../../config/database.php";
require_once "../../models/matched_friend.php";

$database = Database::getInstance();
$matchedFriend = new MatchedFriend($database->getConnection());


// check if data exist
if(isset($_GET['id'])){
    $matchedFriend->id = $_GET['id'];

    try{
        if($matchedFriend->cancelRequest()){
            // tell the user
            echo json_encode(array("succ" => true, "msg" => "Friend request cancelled successfully!"));
        }
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to cancel friend request. Something went wrong!", "error" => $e->getMessage()));
    }

}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to cancel friend request. Incomplete Data"));
}
?>