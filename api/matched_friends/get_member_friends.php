<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and matchedFriend file
require_once "../../config/database.php";
require_once "../../models/matched_friend.php";
require_once "../../models/member_survey.php";
require_once "../../models/member.php";

$database = Database::getInstance();
$matchedFriend = new MatchedFriend($database->getConnection());
$member = new Member($database->getConnection());
$memberSurvey = new MemberSurvey($database->getConnection());

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

            if($status != 2){
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
                    "friend" => getMemberDetails($memberId, $friendId, $member, $memberSurvey)
                );

                array_push($matchedFriend_array['records'], $matchedFriend_item);
            }
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

function getMemberDetails($observerId, $friendId, $member, $memberSurvey){
    $memberId = $_GET['id'];

    if($observerId == $memberId){
        // fetch member for friendId
        $member->id = $friendId;
    }else{
        // fetch member for observerId
        $member->id = $observerId;
    }

    $stmt = $member->readOne();
    $row = $stmt->fetchALL()[0];
    extract($row);

    $memberSurvey->memberId = $id;

    return array(
        "id" => $id,
        "name" => $name,
        "NIC" => $NIC,
        "gender" => $gender,
        "email" => $email,
        "mobileNo" => $mobileNo,
        "address" => $address,
        "memberType" => $memberType,
        "image" => $image,
        "memberSurvey" => $memberSurvey->readAllMemberSurvey()->fetchALL(),
        "status" => $status,
        "created_at" => $created_at,
        "updated_at" => $updated_at
    );
}

?>