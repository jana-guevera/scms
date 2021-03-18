<?php 

require_once "./config/database.php";
require_once "./models/month.php";
require_once "./models/monthly_payment.php";
require_once "./models/event_fees_payment.php";
require_once "./models/member.php";

$database = Database::getInstance();
$month = new Month($database->getConnection());
$monthlyPayment = new MonthlyPayment($database->getConnection());
$eventFeesPayment = new EventFeesPayment($database->getConnection());
$member = new Member($database->getConnection());

$current_month = date('01-m-Y');

$month->month = $current_month;

$monthStmt = $month->readOne();
$monthRowCount = $monthStmt->rowCount();

// create month if doesnt exist
if($monthRowCount != 1){
    $month->create();
}

$month->month = $current_month;
$currentMonthId = $month->readOne()->fetchAll()[0]['id'];

// fetch all active members
$activeMembers = $member->readAll()->fetchAll();

// check if monthly fee has been created for each member
for($i = 0; $i < count($activeMembers); $i++){
    // get current month payment
    $monthlyPayment->monthId = $currentMonthId;
    $monthlyPayment->memberId = $activeMembers[$i]['id'];
    $monthlyPayStmt = $monthlyPayment->getMemberPaymentWithMonth();
    $monthlyPayRow = $monthlyPayStmt->rowCount();

    // check if current month payment has been created
    if($monthlyPayRow != 1){
        $monthlyPayment->amount = $activeMembers[$i]['memberType'] == 0 ? 12 : 5;
        $monthlyPayment->create();
    }

    // get all payment months of a client
    $clientMonthlyPayments = $monthlyPayment->readAll()->fetchAll();

    for($k = 0; $k < count($clientMonthlyPayments); $k++){
        // check if the payment has not been done for a month
        if($clientMonthlyPayments[$k]['status'] == 0){
            $monthlyPayment->status = $clientMonthlyPayments[$k]['status'];
            $monthlyPayment->month = $clientMonthlyPayments[$k]['month'];
            // check if the payment is overdue 
            $currentDate= date_create(date("d-m-Y"));
            $observingDate= date_create($monthlyPayment->month);
            $diff= date_diff($observingDate,$currentDate);
            $diff = $diff->format("%R%a");

            if($diff >= 30){
                $member->id = $activeMembers[$i]['id'];
                $member->status = 3;
                $member->changeStatus();

                $monthlyPayment->id = $clientMonthlyPayments[$k]['id'];
                $monthlyPayment->status = 2;
                $monthlyPayment->changeStatus();
            }
        }
    }

    // get event fees of a client
    $eventFeesPayment->memberId = $activeMembers[$i]['id'];
    $eventFees = $eventFeesPayment->readAll()->fetchAll();

    for($j = 0; $j < count($eventFees); $j++){
        // check if the payment has not been done for an event
        if($eventFees[$j]['status'] == 0){
            $eventFeesPayment->status = $eventFees[$j]['status'];
            $eventFeesPayment->month = $eventFees[$j]['month'];
            // check if the payment is overdue 
            $currentDate= date_create(date("d-m-Y"));
            $observingDate= date_create($eventFeesPayment->month);
            $diff= date_diff($observingDate,$currentDate);
            $diff = $diff->format("%R%a");

            if($diff >= 30){
                $member->id = $activeMembers[$i]['id'];
                $member->status = 3;
                $member->changeStatus();

                $eventFeesPayment->id = $eventFees[$j]['id'];
                $eventFeesPayment->status = 2;
                $eventFeesPayment->changeStatus();
            }
        }
    }
}











?>