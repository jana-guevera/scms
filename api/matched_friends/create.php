<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods, Content-Type, Authorization, X-Requested-With");

// get database and matched_friend file
require_once "../../config/database.php";
require_once "../../models/matched_friend.php";

$database = Database::getInstance();
$matchedFriend = new MatchedFriend($database->getConnection());

// get send data
$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
if(
    !empty($data->memberId) && 
    !empty($data->friendId)
){
    // set matchedFriend property values
    $matchedFriend->memberId = $data->memberId;
    $matchedFriend->friendId = $data->friendId;

    // create matchedFriend
    try{
        $matchedFriend->create();
        // tell the user
        echo json_encode(array("succ" => true, "msg" => "Friend request send successfully!"));
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to send friend request. Something went wrong!", "error" => $e->getMessage()));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to send friend request. Incomplete Data"));
}

?>