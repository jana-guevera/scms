<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods, Content-Type, Authorization, X-Requested-With");

// get database and staff file
require_once "../../config/database.php";
require_once "../../models/staff.php";

$database = Database::getInstance();
$staff = new Staff($database->getConnection());

// get send data
$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
if(
    !empty($data->id) && 
    !empty($data->name) && 
    !empty($data->NIC) && 
    !empty($data->email) &&
    !empty($data->mobileNo) &&
    !empty($data->address) &&
    isset($data->role) &&
    isset($data->status) 
){
    // set staff property values
    $staff->id = $data->id;
    $staff->name = $data->name;
    $staff->NIC = $data->NIC;
    $staff->email = $data->email;
    $staff->mobileNo = $data->mobileNo;
    $staff->address = $data->address;
    $staff->role = $data->role;
    $staff->status = $data->status;

    //check if staff already exist 
    if($staff->isEmailUniqueOnUpdate()){
        if($staff->isNICUniqueOnUpdate()){

            try{
                $staff->update();
                // tell the user
                echo json_encode(array("succ" => true, "msg" => "Staff updated successfully"));

            }catch(Exception $e){
                echo json_encode(array("succ" => false, "msg" => "Unable to update staff. Something went wrong!", "error" => $e->getMessage()));
            }
            
        }else{
            // tell the user nic already exist
            echo json_encode(array("succ" => false, "msg" => "Unable to update staff. NIC already exist"));
        }
    }else{
    // tell the user email already exist
    echo json_encode(array("succ" => false, "msg" => "Unable to update staff. Email already exist"));
    }
     
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to update staff. Incomplete Data"));
}

?>