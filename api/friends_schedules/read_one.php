<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and friends_schedules file
require_once "../../config/database.php";
require_once "../../models/friends_schedule.php";
require_once "../../models/member.php";

$database = Database::getInstance();
$schedule = new FriendsSchedule($database->getConnection());
$member = new Member($database->getConnection());

// check if id exist
if(isset($_GET['id'])){
    $schedule->id = $_GET['id'];

    $stmt = $schedule->readOne();
    $num = $stmt->rowCount();

    // cehck if a event exist
    if($num == 1){
        // retrieve data
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        extract($row);

        $schedule_item = array(
            "id" => $id,
            "scheduleCreaterId" => $scheduleCreaterId,
            "scheduleReceiverId" => $scheduleReceiverId,
            "startDateTime" => $startDateTime,
            "endDateTime" => $endDateTime,
            "status" => $status,
            "created_at" => $created_at,
            "updated_at" => $updated_at,
            "scheduleCreator" => $member->getSingleRecord($scheduleCreaterId)->fetchAll()[0],
            "scheduleReceiver" => $member->getSingleRecord($scheduleReceiverId)->fetchAll()[0],
        );

        // send event data in json format
        echo json_encode(array("succ" => true, "record" => $schedule_item));
    }else{
        // tell the user no events were found
        echo json_encode(array("succ" => true, 'msg' => "No schedules were found"));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to fetch schedule. Incomplete Data"));
}
