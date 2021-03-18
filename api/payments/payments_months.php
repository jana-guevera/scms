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
if(isset($_GET['id'])){
    $monthlyPayment->memberId = $_GET['id'];
    $monthlyPaymentsStmt = $monthlyPayment->readAll();

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
            "monthlyPaymentStatus" => $status,
            "created_at" => $created_at,
            "updated_at" => $updated_at,
            "overallStatus" => getOverallStatus($status, $monthId, $eventFeesPayment),
            "totalAmount" => getTotalAmount($amount, $monthId, $eventFeesPayment),
            "balanceAmount" => getBalanceAmount($status, $amount, $monthId, $eventFeesPayment)
        );

        array_push($monthlyPayment_array['records'], $monthlyPayment_item);
    }

    // send memberEvent array data in json format
    echo json_encode($monthlyPayment_array);
   
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to fetch payments months. Incomplete Data"));
}

function getOverallStatus($monthlyPaymentStatus, $monthId, $eventFeesPayment){
    if($monthlyPaymentStatus == 2){
        return 2;
    }

    $eventFeesPayment->memberId = $_GET['id'];
    $eventFeesPayment->monthId = $monthId;
    $eventFees = $eventFeesPayment->readOnMonthAndMember()->fetchAll();

    $eventFeesStatus = 1;

    for($i = 0; $i < count($eventFees); $i++){
        if($eventFees[$i]['status'] == 0){
            $eventFeesStatus = 0;
        }

        if($eventFees[$i]['status'] == 2){ return 2; }
    }

    if($monthlyPaymentStatus == 1 && $eventFeesStatus == 1){
        return 1;
    }

    return 0;
}

function getTotalAmount($monthlyPaymentAmount, $monthId, $eventFeesPayment){
    $eventFeesPayment->memberId = $_GET['id'];
    $eventFeesPayment->monthId = $monthId;
    $eventFees = $eventFeesPayment->readOnMonthAndMember()->fetchAll();

    $total = $monthlyPaymentAmount;

    for($i = 0; $i < count($eventFees); $i++){
        $total += $eventFees[$i]['amount'];
    }

    return $total;
}

function getBalanceAmount($monthlyPaymentStatus, $monthlyPaymentAmount, $monthId, $eventFeesPayment){
    $eventFeesPayment->memberId = $_GET['id'];
    $eventFeesPayment->monthId = $monthId;
    $eventFees = $eventFeesPayment->readOnMonthAndMember()->fetchAll();

    $total = 0;

    if($monthlyPaymentStatus != 1){
        $total += $monthlyPaymentAmount;
    }

    for($i = 0; $i < count($eventFees); $i++){
        if($eventFees[$i]['status'] != 1){
            $total += $eventFees[$i]['amount'];
        }
    }

    return $total;
}


?>