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

    $stmt = $member->readOne();
    $num = $stmt->rowCount();

    // cehck if a member exist
    if($num == 1){
        // retrieve data
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        extract($row);

        $member_item = array(
            "id" => $id,
            "name" => $name,
            "NIC" => $NIC,
            "gender" => $gender,
            "email" => $email,
            "mobileNo" => $mobileNo,
            "address" => $address,
            "memberType" => $memberType,
            "image" => $image,
            "status" => $status,
            "created_at" => $created_at,
            "updated_at" => $updated_at
        );

        // send member data in json format
        echo json_encode(array("succ" => true, "record" => $member_item));
    }else{
        // tell the user no members were found
        echo json_encode(array("succ" => true, 'msg' => "No member were found"));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to fetch member. Incomplete Data"));
}
