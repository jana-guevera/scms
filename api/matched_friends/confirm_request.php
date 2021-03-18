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
    $matchedFriend->status = 1;

    try{
        if($matchedFriend->changeStatus()){
            // tell the user
            echo json_encode(array("succ" => true, "msg" => "Friend added successfully!"));
        }
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to add the friend. Something went wrong!", "error" => $e->getMessage()));
    }

}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to add the friend. Incomplete Data"));
}
?>