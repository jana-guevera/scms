<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and event file
require_once "../../config/database.php";
require_once "../../models/event.php";
require_once "../../models/event_fees_payment.php";

$database = Database::getInstance();
$event = new Event($database->getConnection());
$eventFeesPayment = new EventFeesPayment($database->getConnection());

$stmt = $event->readAll();
$num = $stmt->rowCount();

$totalIncome = 0;

// cehck if there are more than 0 rows
if($num > 0){
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        if($status == 3){
            $eventFeesPayment->eventId = $id;
            $eventFess = $eventFeesPayment->readAllOnEventId()->fetchAll();
            $eventIncome = count($eventFess) * $fee;
            $totalIncome += $eventIncome;
        }
    } 
}

// send event array data in json format
echo json_encode($totalIncome);
?>