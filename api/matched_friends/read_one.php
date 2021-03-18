<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and memberEvent file
require_once "../../config/database.php";
require_once "../../models/matched_friend.php";
require_once "../../models/member.php";

$database = Database::getInstance();
$matchedFriend = new MatchedFriend($database->getConnection());
$member = new Member($database->getConnection());

// check if id exist
if(isset($_GET['id'])){
    $matchedFriend->id = $_GET['id'];

    $stmt = $matchedFriend->readOne();
    $num = $stmt->rowCount();

    // cehck if a matchedFriend exist
    if($num == 1){
        // retrieve data
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        extract($row);

        $member->id = $memberId;

        $item = array(
            "id" => $id,
            "memberId" => $memberId,
            "friendId" => $friendId,
            "isGoodMatchRequester" => $isGoodMatchRequester,
            "isGoodMatchRecevier" => $isGoodMatchRecevier,
            "status" => $status,
            "created_at" => $created_at,
            "updated_at" => $updated_at,
            "friend" => $member->readOne()->fetchAll()[0]
        );

        // send matchedFriend data in json format
        echo json_encode(array("succ" => true, "record" => $item));
    }else{
        // tell the user no matchedFriend were found
        echo json_encode(array("succ" => true, 'msg' => "No friends were found"));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to fetch member friends. Incomplete Data"));
}
