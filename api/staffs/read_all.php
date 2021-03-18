<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and staff file
require_once "../../config/database.php";
require_once "../../models/staff.php";

$database = Database::getInstance();
$staff = new Staff($database->getConnection());

$stmt = $staff->readAll();
$num = $stmt->rowCount();

// cehck if there are more than 0 rows
if($num > 0){
    // staffs array
    $staff_array = array();
    $staff_array['records'] = array();

    // retrieve data
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $staff_item = array(
            "id" => $id,
            "name" => $name,
            "NIC" => $NIC,
            "email" => $email,
            "mobileNo" => $mobileNo,
            "address" => $address,
            "role" => $role,
            "image" => $image,
            "status" => $status,
            "created_at" => $created_at,
            "updated_at" => $updated_at
        );

        array_push($staff_array['records'], $staff_item);
    }

    // send staff array data in json format
    echo json_encode($staff_array);
}else{
    // tell the user no staffs were found
    echo json_encode(array('msg' => "No staffs were found", 'records' => array()));
}


?>