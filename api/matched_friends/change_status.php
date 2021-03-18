<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and member_event file
require_once "../../config/database.php";
require_once "../../models/matched_friend.php";

$database = Database::getInstance();
$matchedFriend = new MatchedFriend($database->getConnection());

// get send data
$data = json_decode(file_get_contents("php://input"));

// check if data exist
if(!empty($data->id) && !empty($data->status)){
    $matchedFriend->id = $data->id;
    $matchedFriend->status = $data->status;

    $succMsg = "";
    $errorMsg = "";

    if($data->status == 1){
        $succMsg = "Friend added successfully!";
        $errorMsg = "Unable to add the friend.";
    }else if($data->status == 0){
        $succMsg = "Unfriended successfully!";
        $errorMsg = "Unable to unfriend!";
    }

    try{
        if($matchedFriend->changeStatus()){
            // tell the user
            echo json_encode(array("succ" => true, "msg" => $succMsg));
        }
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => $errorMsg." Something went wrong!", "error" => $e->getMessage()));
    }

}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => $errorMsg." Incomplete Data"));
}
?>