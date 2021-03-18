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
    try{
        $monthlyPayment->monthId = $_GET['monthId'];
        $monthlyPayment->memberId = $_GET['id'];
        
        $eventFeesPayment->monthId = $_GET['monthId'];
        $eventFeesPayment->memberId = $_GET['id'];

        $fetchedMonthlyPayment = $monthlyPayment->getMemberPaymentWithMonth()->fetchAll()[0];
        $fetchedEventFees = $eventFeesPayment->readOnMonthAndMember()->fetchAll();

        $monthlyPayment->id = $fetchedMonthlyPayment['id'];
        $monthlyPayment->status = 1;
        $monthlyPayment->changeStatus();

        for($i = 0; $i < count($fetchedEventFees); $i++){
            $eventFeesPayment->id = $fetchedEventFees[$i]['id'];
            $eventFeesPayment->status = 1;
            $eventFeesPayment->changeStatus();
        }

        $member->id = $_GET['id'];
        $fetchedMember = $member->readOne()->fetchAll()[0];

        if($fetchedMember['status'] == 2){
            $member->status = 1;
            $member->changeStatus();
        }

        echo json_encode(array("succ" => true, "msg" => "Payment made successfully"));
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to make payment. Please try again!"));
    }
   
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to make payments. Incomplete Data"));
}

?>