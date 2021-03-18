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
    !empty($data->mobileNo)
){
    // set staff property values
    $staff->id = $data->id;
    $staff->name = $data->name;
    $staff->mobileNo = $data->mobileNo;
    $staff->image = null;
    
    if(!empty($data->imageName)){
        $staff->image = $data->imageName;
        $oldImage  = $staff->readOne()->fetchAll()[0]['image'];
        if($oldImage != "user.jpg"){
            unlink("../../resources/images/uploads/profile/". $oldImage);
        }
    }

    try{
        $staff->updateUserProfile();
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