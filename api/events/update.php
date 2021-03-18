<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods, Content-Type, Authorization, X-Requested-With");

// get database and member file
require_once "../../config/database.php";
require_once "../../models/event.php";
require_once "../../models/notification.php";
require_once "../../models/member_event.php";

$database = Database::getInstance();
$event = new Event($database->getConnection());
$notification = new Notification($database->getConnection());
$memberEvent = new MemberEvent($database->getConnection());

// get send data
$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
if(
    !empty($data->id) && 
    !empty($data->name) && 
    !empty($data->startDateTime) && 
    !empty($data->endDateTime) &&
    !empty($data->location) &&
    !empty($data->fee) &&
    !empty($data->image) &&
    !empty($data->description) && 
    !empty($data->oldImage)
){
    // set member property values
    $event->id = $data->id;
    $event->name = $data->name;
    $event->startDateTime = $data->startDateTime;
    $event->endDateTime = $data->endDateTime;
    $event->location = $data->location;
    $event->fee = $data->fee;
    $event->image = $data->image;
    $event->description = $data->description;

    try{
        $event->update();

        if($data->oldImage != "event.jpg" && $data->image != $data->oldImage){
            unlink("../../resources/images/uploads/events/". $data->oldImage);
        }

        $memberEvent->eventId = $event->id;
        $attendingMembers = $memberEvent->readAttendingMembers()->fetchAll();
        $notification->notification = "The event (" . $event->name . ") which you attending has been updated.";

        for($i = 0; $i < count($attendingMembers); $i++){
            $notification->memberId = $attendingMembers[$i]['memberId'];
            $notification->create();
        }

        // tell the user
        echo json_encode(array("succ" => true, "msg" => "Event updated successfully"));

    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to update Event. Something went wrong!", "error" => $e->getMessage()));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to update event. Incomplete Data"));
}

?>