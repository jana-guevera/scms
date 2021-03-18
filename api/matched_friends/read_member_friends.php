<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and matchedFriend file
require_once "../../config/database.php";
require_once "../../models/matched_friend.php";
require_once "../../models/member.php";

$database = Database::getInstance();
$matchedFriend = new MatchedFriend($database->getConnection());
$member = new Member($database->getConnection());


// check if id exist
if(isset($_GET['id'])){
    $matchedFriend->memberId = $_GET['id'];

    $stmt = $matchedFriend->readMemberFriends();
    $num = $stmt->rowCount();
    
    // cehck if there are more than 0 rows
    if($num > 0){
        // matchedFriends array
        $matchedFriend_array = array();
        $matchedFriend_array['records'] = array();

        // retrieve data
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);

            $member->id = $friendId;

            $matchedFriend_item = array(
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

            array_push($matchedFriend_array['records'], $matchedFriend_item);
        }

        // send matchedFriend array data in json format
        echo json_encode($matchedFriend_array);
    }else{
        // tell the user no matchedFriends were found
        echo json_encode(array('msg' => "No friends were found", 'records' => array()));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to fetch member friends. Incomplete Data", 'records' => array()));
}
?>