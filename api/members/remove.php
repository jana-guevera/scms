<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and member file
require_once "../../config/database.php";
require_once "../../models/member.php";

$database = Database::getInstance();
$member = new Member($database->getConnection());

// check if id exist
if(isset($_GET['id'])){
    $member->id = $_GET['id'];
    $member->status = 3;

    try{
        if($member->changeStatus()){
            // tell the user
            echo json_encode(array("succ" => true, "msg" => "Member removed successfully!"));
        }
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to remove Member. Something went wrong!", "error" => $e->getMessage()));
    }

}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to remove member. Incomplete Data"));
}
?>