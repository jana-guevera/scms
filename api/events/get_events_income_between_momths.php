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

// get send data
$data = json_decode(file_get_contents("php://input"));

$stmt = $event->readAll();
$num = $stmt->rowCount();

$totalIncome = 0;

// cehck if there are more than 0 rows
if($num > 0){
    // events array
    $event_array = array();

    $startMonth = strtotime($data->startMonth);
    $endMonth = strtotime(date("Y-m-t", strtotime($data->endMonth)));

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        if($status == 3){
            $startDate = strtotime(date("Y-m-d", strtotime($startDateTime)));

            if($startDate >= $startMonth && $startDate <= $endMonth){
                $eventFeesPayment->eventId = $id;
                $eventFess = $eventFeesPayment->readAllOnEventId()->fetchAll();
                $eventIncome = count($eventFess) * $fee;
                $totalIncome += $eventIncome;

                $event_item = array(
                    "id" => $id,
                    "name" => $name,
                    "startDateTime" => $startDateTime,
                    "endDateTime" => $endDateTime,
                    "categoryId" => $categoryId,
                    "categoryName" => $categoryName,
                    "location" => $location,
                    "fee" => $fee,
                    "image" => $image,
                    "description" => $description,
                    "eventIncome" => $eventIncome,
                    "status" => $status,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                );
        
                array_push($event_array, $event_item);
            }
        }
    } 

    // send event array data in json format
    echo json_encode(array("records" => $event_array, "totalIncome" => $totalIncome, 
                    "startMonth" => date("Y-m-d", $startMonth), "endMonth" => date("Y-m-d", $endMonth)));
}else{
    // tell the user no events were found
    echo json_encode(array('msg' => "No events were scheduled during this period", 'records' => array()));
}
?>