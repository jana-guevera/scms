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
    !empty($data->name) && 
    !empty($data->NIC) && 
    !empty($data->email) &&
    !empty($data->mobileNo) &&
    !empty($data->address) &&
    isset($data->role)
){
    // set staff property values
    $staff->name = $data->name;
    $staff->NIC = $data->NIC;
    $staff->email = $data->email;
    $staff->mobileNo = $data->mobileNo;
    $staff->address = $data->address;
    $staff->role = $data->role;

    //check if staff already exist 
    if($staff->isEmailUnique()){
        if($staff->isNICUnique()){
            $staff->id = generateStaffId();

            try{
                $staff->create();
                // tell the user
                echo json_encode(array("succ" => true, "msg" => "Staff created successfully"));

            }catch(Exception $e){
                echo json_encode(array("succ" => false, "msg" => "Unable to create staff. Something went wrong!", "error" => $e->getMessage()));
            }
            
        }else{
            // tell the user nic already exist
            echo json_encode(array("succ" => false, "msg" => "Unable to create staff. NIC already exist"));
        }
    }else{
    // tell the user email already exist
    echo json_encode(array("succ" => false, "msg" => "Unable to create staff. Email already exist"));
    }
     
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to create staff. Incomplete Data"));
}


function generateStaffId(){
    $myfile = fopen("staff_id.txt", "r") or die("Unable to open file!");
    $staff_id =  fgets($myfile);
    fclose($myfile);
    $staff_num = preg_replace('/[^0-9]/', '', $staff_id) + 1;
    $staff_id = "EMP".$staff_num;

    $myfile = fopen("staff_id.txt", "w") or die("Unable to open file!");
    fwrite($myfile, $staff_id);
    fclose($myfile);

    return $staff_id;
}

?>