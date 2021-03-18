<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and memberEvent file
require_once "../../config/database.php";
require_once "../../models/month.php";
require_once "../../models/monthly_payment.php";
require_once "../../models/event_fees_payment.php";
require_once "../../models/member.php";

$database = Database::getInstance();
$month = new Month($database->getConnection());
$member = new Member($database->getConnection());
$monthlyPayment = new MonthlyPayment($database->getConnection());
$eventFeesPayment = new EventFeesPayment($database->getConnection());

// check if id exist
if(isset($_GET['id']) && isset($_GET['monthId'])){
    $monthlyPayment->monthId = $_GET['monthId'];
    $monthlyPayment->memberId = $_GET['id'];
    
    $eventFeesPayment->monthId = $_GET['monthId'];
    $eventFeesPayment->memberId = $_GET['id'];

    $monthlyPaymentsStmt = $monthlyPayment->getMemberPaymentWithMonth();
    $eventFeesStmt = $eventFeesPayment->readOnMonthAndMember();

    $monthlyPayment_array = array();
    $monthlyPayment_array['records'] = array();

    // retrieve data
    while($row = $monthlyPaymentsStmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $monthlyPayment_item = array(
            "id" => $id,
            "memberId" => $memberId,
            "monthId" => $monthId,
            "month" => $month,
            "amount" => $amount,
            "name" => "",
            "status" => $status,
            "created_at" => $created_at,
            "updated_at" => $updated_at,
            "paymentFor" => "Monthly Payment"
        );

        array_push($monthlyPayment_array['records'], $monthlyPayment_item);
    }

    // retrieve data
    while($row = $eventFeesStmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $monthlyPayment_item = array(
            "id" => $id,
            "memberId" => $memberId,
            "monthId" => $monthId,
            "month" => $month,
            "amount" => $amount,
            "name" => $name,
            "status" => $status,
            "created_at" => $created_at,
            "updated_at" => $updated_at,
            "paymentFor" => "Event Fee Payment"
        );

        array_push($monthlyPayment_array['records'], $monthlyPayment_item);
    }

    // send memberEvent array data in json format
    echo json_encode($monthlyPayment_array);
   
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to fetch payments months. Incomplete Data"));
}

?>