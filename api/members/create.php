<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods, Content-Type, Authorization, X-Requested-With");

// get database and member file
require_once "../../config/database.php";
require_once "../../models/member.php";
require_once "../../models/survey.php";
require_once "../../models/member_survey.php";


$database = Database::getInstance();
$member = new Member($database->getConnection());
$survey = new Survey($database->getConnection());
$memberSurvey = new MemberSurvey($database->getConnection());

// get send data
$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
if(
    !empty($data->name) && 
    !empty($data->NIC) && 
    !empty($data->gender) && 
    !empty($data->email) &&
    !empty($data->mobileNo) &&
    !empty($data->address)
){
    // set member property values
    $member->name = $data->name;
    $member->NIC = $data->NIC;
    $member->gender = $data->gender;
    $member->email = $data->email;
    $member->mobileNo = $data->mobileNo;
    $member->address = $data->address;
    $member->memberType = 0;

    if(!empty($data->image)){
        $member->image = $data->image;
    }else{
        $member->image = "user.jpg";
    }

    //check if member already exist 
    if($member->isEmailUnique()){
        if($member->isNICUnique()){
            $member->id = generateMemberId();

            try{
                $member->create();

                $memberSurvey->memberId = $member->id;
                $survey_arr = $survey->readAll()->fetchAll();
                $memberSurvey->createMultiple($survey_arr);

                // tell the user
                echo json_encode(array("succ" => true, "msg" => "Member created successfully"));

            }catch(Exception $e){
                $member->remove();
                echo json_encode(array("succ" => false, "msg" => "Unable to create Member. Something went wrong!", "error" => $e->getMessage()));
            }
            
        }else{
            // tell the user nic already exist
            echo json_encode(array("succ" => false, "msg" => "Unable to create member. NIC already exist"));
        }
    }else{
    // tell the user email already exist
    echo json_encode(array("succ" => false, "msg" => "Unable to create member. Email already exist"));
    }
     
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to create member. Incomplete Data"));
}


function generateMemberId(){
    $myfile = fopen("member_id.txt", "r") or die("Unable to open file!");
    $member_id =  fgets($myfile);
    fclose($myfile);
    $member_num = preg_replace('/[^0-9]/', '', $member_id) + 1;
    $member_id = "MEM".$member_num;

    $myfile = fopen("member_id.txt", "w") or die("Unable to open file!");
    fwrite($myfile, $member_id);
    fclose($myfile);

    return $member_id;
}

?>