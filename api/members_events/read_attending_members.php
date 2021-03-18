<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and memberEvent file
require_once "../../config/database.php";
require_once "../../models/member_event.php";
require_once "../../models/member.php";

$database = Database::getInstance();
$memberEvent = new MemberEvent($database->getConnection());
$member = new Member($database->getConnection());


// check if id exist
if(isset($_GET['id'])){
    $memberEvent->eventId = $_GET['id'];

    $stmt = $memberEvent->readAttendingMembers();
    $num = $stmt->rowCount();
    
    // cehck if there are more than 0 rows
    if($num > 0){
        // memberEvents array
        $memberEvent_array = array();
        $memberEvent_array['records'] = array();

        // retrieve data
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);

            $member->id = $memberId;

            $memberEvent_item = array(
                "id" => $id,
                "memberId" => $memberId,
                "eventId" => $eventId,
                "status" => $status,
                "created_at" => $created_at,
                "updated_at" => $updated_at,
                "member" => $member->readOne()->fetchAll()[0]
            );

            array_push($memberEvent_array['records'], $memberEvent_item);
        }

        // send memberEvent array data in json format
        echo json_encode($memberEvent_array);
    }else{
        // tell the user no memberEvents were found
        echo json_encode(array('msg' => "No events were found", 'records' => array()));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to fetch member events. Incomplete Data"));
}


?>