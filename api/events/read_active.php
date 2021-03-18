<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and event file
require_once "../../config/database.php";
require_once "../../models/event.php";

$database = Database::getInstance();
$event = new Event($database->getConnection());

$stmt = $event->readActive();
$num = $stmt->rowCount();

// cehck if there are more than 0 rows
if($num > 0){
    // events array
    $event_array = array();
    $event_array['records'] = array();

    // retrieve data
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
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

        array_push($event_array['records'], $event_item);
    }

    // send event array data in json format
    echo json_encode($event_array);
}else{
    // tell the user no events were found
    echo json_encode(array('msg' => "No events were found"));
}


?>