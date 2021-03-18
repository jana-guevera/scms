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
    $memberId= $_GET['id'];

    $stmt = $schedule->readMemberSchedules($memberId);
    $num = $stmt->rowCount();
    
    // cehck if there are more than 0 rows
    if($num > 0){
        // matchedFriends array
        $schedule_array = array();
        $schedule_array['records'] = array();

        // retrieve data
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
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

            array_push($schedule_array['records'], $schedule_item);
        }

        // send schedule array data in json format
        echo json_encode($schedule_array);
    }else{
        // tell the user no schedules were found
        echo json_encode(array('msg' => "No schedules were found", 'records' => array()));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to fetch member schedules. Incomplete Data", 'records' => array()));
}

?>