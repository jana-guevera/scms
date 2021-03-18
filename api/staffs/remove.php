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
    $staff->status = 2;

    try{
        if($staff->remove()){
            // tell the user
            echo json_encode(array("succ" => true, "msg" => "Staff removed successfully!"));
        }
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to remove staff. Something went wrong!", "error" => $e->getMessage()));
    }

}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to remove staff. Incomplete Data"));
}
?>