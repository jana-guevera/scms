<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and event file
require_once "../../config/database.php";
require_once "../../models/event.php";

$database = Database::getInstance();
$event = new Event($database->getConnection());

// check if id exist
if(isset($_GET['id'])){
    $event->id = $_GET['id'];

    $stmt = $event->readOne();
    $num = $stmt->rowCount();

    // cehck if a event exist
    if($num == 1){
        // retrieve data
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        extract($row);

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
            "status" => $status,
            "created_at" => $created_at,
            "updated_at" => $updated_at
        );

        // send event data in json format
        echo json_encode(array("succ" => true, "record" => $event_item));
    }else{
        // tell the user no events were found
        echo json_encode(array("succ" => true, 'msg' => "No event were found"));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to fetch event. Incomplete Data"));
}
