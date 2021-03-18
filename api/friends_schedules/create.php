<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods, Content-Type, Authorization, X-Requested-With");

// get database and friends_schedule file
require_once "../../config/database.php";
require_once "../../models/friends_schedule.php";

$database = Database::getInstance();
$schedule = new FriendsSchedule($database->getConnection());

// get send data
$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
if(
    !empty($data->scheduleCreaterId) &&
    !empty($data->scheduleReceiverId) && 
    !empty($data->startDateTime) && 
    !empty($data->endDateTime) 
){
    // set schedule property values
    $schedule->scheduleCreaterId = $data->scheduleCreaterId;
    $schedule->scheduleReceiverId = $data->scheduleReceiverId;
    $schedule->startDateTime = $data->startDateTime;
    $schedule->endDateTime = $data->endDateTime;

    // create schedule
    try{
        $schedule->create();
        // tell the user
        echo json_encode(array("succ" => true, "msg" => "Schedule added successfully!"));
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to add schedule. Something went wrong!", "error" => $e->getMessage()));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to add schedule. Incomplete Data"));
}

?>