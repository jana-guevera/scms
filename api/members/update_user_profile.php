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
    !empty($data->gender) && 
    !empty($data->address) && 
    !empty($data->mobileNo)
){
    // set staff property values
    $member->id = $data->id;
    $member->name = $data->name;
    $member->mobileNo = $data->mobileNo;
    $member->gender = $data->gender;
    $member->address = $data->address;
    $member->image = null;
    
    if(!empty($data->imageName)){
        $member->image = $data->imageName;
        $oldImage  = $member->readOne()->fetchAll()[0]['image'];
        if($oldImage != "user.jpg"){
            unlink("../../resources/images/uploads/profile/". $oldImage);
        }
    }

    try{
        $member->updateUserProfile();
        // tell the user
        echo json_encode(array("succ" => true, "msg" => "Profile updated successfully"));

    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to update profile. Something went wrong!", "error" => $e->getMessage()));
    }
     
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to update profile. Incomplete Data"));
}

?>