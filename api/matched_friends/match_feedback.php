
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
if(!empty($data->id) && !empty($data->memberId) && isset($data->feedback)){
    $matchedFriend->id = $data->id;
    $matchedFriend->memberId = $data->memberId;

    try{
        $matchedFriend->provideFeedback($data->memberId, $data->feedback);
        // tell the user
        echo json_encode(array("succ" => true, "msg" => "Feedback updated successfully!"));
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to update the feedback. Something went wrong!", "error" => $e->getMessage()));
    }

}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to update the feedback. Incomplete Data"));
}
?>