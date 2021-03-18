<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and memberEvent file
require_once "../../config/database.php";
require_once "../../models/event.php";
require_once "../../models/member_event.php";

$database = Database::getInstance();
$memberEvent = new MemberEvent($database->getConnection());
$event = new Event($database->getConnection());

// check if id exist
if(isset($_GET['id'])){
    $memberEvent->id = $_GET['id'];

    $stmt = $memberEvent->readOne();
    $num = $stmt->rowCount();

    // cehck if a memberEvent exist
    if($num == 1){
        // retrieve data
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        extract($row);

        $event->id = $eventId;

        $item = array(
            "id" => $id,
            "memberId" => $memberId,
            "eventId" => $eventId,
            "status" => $status,
            "created_at" => $created_at,
            "updated_at" => $updated_at,
            "event" => $event->readOne()->fetchAll()[0]
        );

        // send memberEvent data in json format
        echo json_encode(array("succ" => true, "record" => $item));
    }else{
        // tell the user no members were found
        echo json_encode(array("succ" => true, 'msg' => "No events were found"));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to fetch member event. Incomplete Data"));
}
