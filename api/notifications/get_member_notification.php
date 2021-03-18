<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and notification file
require_once "../../config/database.php";
require_once "../../models/notification.php";

$database = Database::getInstance();
$notification = new Notification($database->getConnection());

// check if id exist
if(isset($_GET['id'])){
    $notification->memberId = $_GET['id'];

    $stmt = $notification->readAllMemberNotification();
    $num = $stmt->rowCount();
    
    // cehck if there are more than 0 rows
    if($num > 0){
        // notifications array
        $notification_array = array();
        $notification_array['records'] = array();

        // retrieve data
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);

            $notification_item = array(
                "id" => $id,
                "memberId" => $memberId,
                "notification" => $notification,
                "status" => $status,
                "created_at" => $created_at,
                "updated_at" => $updated_at
            );

            array_push($notification_array['records'], $notification_item);
        }

        // send notification array data in json format
        echo json_encode($notification_array);
    }else{
        // tell the user no notifications were found
        echo json_encode(array('msg' => "No notifications were found", 'records' => array()));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to fetch notifications. Incomplete Data", 'records' => array()));
}


?>