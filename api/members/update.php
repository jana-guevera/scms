<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods, Content-Type, Authorization, X-Requested-With");

// get database and member file
require_once "../../config/database.php";
require_once "../../models/member.php";

$database = Database::getInstance();
$member = new Member($database->getConnection());

// get send data
$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
if(
    !empty($data->id) && 
    !empty($data->name) && 
    !empty($data->NIC) && 
    !empty($data->gender) && 
    !empty($data->email) &&
    !empty($data->mobileNo) &&
    !empty($data->address) &&
    isset($data->memberType) &&
    !empty($data->image) &&
    isset($data->status) && 
    !empty($data->oldImage)
){
    // set member property values
    $member->id = $data->id;
    $member->name = $data->name;
    $member->NIC = $data->NIC;
    $member->gender = $data->gender;
    $member->email = $data->email;
    $member->mobileNo = $data->mobileNo;
    $member->address = $data->address;
    $member->memberType = $data->memberType;
    $member->image = $data->image;
    $member->status = $data->status;

    //check if member already exist 
    if($member->isEmailUniqueOnUpdate()){
        if($member->isNICUniqueOnUpdate()){

            try{
                $member->update();

                if($data->oldImage != "user.jpg" && $data->image != $data->oldImage){
                    unlink("../../resources/images/uploads/profile/". $data->oldImage);
                }

                // tell the user
                echo json_encode(array("succ" => true, "msg" => "Member details updated successfully"));

            }catch(Exception $e){
                echo json_encode(array("succ" => false, "msg" => "Unable to update Member. Something went wrong!", 
                "error" => $e->getMessage()));
            }
            
        }else{
            // tell the user nic already exist
            echo json_encode(array("succ" => false, "msg" => "Unable to update member. NIC already exist"));
        }
    }else{
    // tell the user email already exist
    echo json_encode(array("succ" => false, "msg" => "Unable to update member. Email already exist"));
    }

}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to update member. Incomplete Data"));
}


?>