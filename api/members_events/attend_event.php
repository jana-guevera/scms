<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and member_event file
require_once "../../config/database.php";
require_once "../../models/member_event.php";
require_once "../../models/event_fees_payment.php";
require_once "../../models/month.php";

$database = Database::getInstance();
$memberEvent = new MemberEvent($database->getConnection());
$eventFees = new EventFeesPayment($database->getConnection());
$month = new Month($database->getConnection());

// get send data
$data = json_decode(file_get_contents("php://input"));

// check if data exist
if(isset($_GET['id'])){
    $memberEvent->id = $_GET['id'];
    $memberEvent->status = 1;

    $month->month = date('01-m-Y');
    $fetchMonth = $month->readOne()->fetchAll()[0];

    $fetchedMemberEvent = $memberEvent->readOne()->fetchAll()[0];
    $eventFees->memberId = $fetchedMemberEvent['memberId'];
    $eventFees->eventId = $fetchedMemberEvent['eventId'];
    $eventFees->amount = $fetchedMemberEvent['fee'];
    $eventFees->monthId = $fetchMonth['id'];

    try{
        if($eventFees->create()){
            $memberEvent->changeStatus();
            // tell the user
            echo json_encode(array("succ" => true, "msg" => "Event attended successfully."));
        }
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to attend the event. Something went wrong!", "error" => $e->getMessage()));
    }

}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to attend the event. Incomplete Data"));
}
?>