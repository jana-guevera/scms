<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and eventCategory file
require_once "../../config/database.php";
require_once "../../models/event_category.php";

$database = Database::getInstance();
$eventCategory = new EventCategory($database->getConnection());

$stmt = $eventCategory->readAll();
$num = $stmt->rowCount();

// cehck if there are more than 0 rows
if($num > 0){
    // eventCategorys array
    $eventCategory_array = array();
    $eventCategory_array['records'] = array();

    // retrieve data
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $eventCategory_item = array(
            "id" => $id,
            "name" => $name,
            "description" => $description,
            "status" => $status,
            "created_at" => $created_at,
            "updated_at" => $updated_at
        );

        array_push($eventCategory_array['records'], $eventCategory_item);
    }

    // send eventCategory array data in json format
    echo json_encode($eventCategory_array);
}else{
    // tell the user no eventCategorys were found
    echo json_encode(array('msg' => "No event categories were found", 'records' => array()));
}


?>