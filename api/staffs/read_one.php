<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and staff file
require_once "../../config/database.php";
require_once "../../models/staff.php";

$database = Database::getInstance();
$staff = new Staff($database->getConnection());

// check if id exist
if(isset($_GET['id'])){
    $staff->id = $_GET['id'];

    $stmt = $staff->readOne();
    $num = $stmt->rowCount();

    // cehck if a staff exist
    if($num == 1){
        // retrieve data
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
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

        // send staff data in json format
        echo json_encode(array("succ" => true, "record" => $staff_item));
    }else{
        // tell the user no staffs were found
        echo json_encode(array("succ" => true, 'msg' => "No staff were found"));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to fetch staff. Incomplete Data"));
}
