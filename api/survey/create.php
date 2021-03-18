<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods, Content-Type, Authorization, X-Requested-With");

// get database and survey file
require_once "../../config/database.php";
require_once "../../models/survey.php";
require_once "../../models/member_survey.php";
require_once "../../models/member.php";

$database = Database::getInstance();
$survey = new Survey($database->getConnection());
$memberSurvey = new MemberSurvey($database->getConnection());
$member = new Member($database->getConnection());

// get send data
$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
if(
    !empty($data->question) && 
    !empty($data->answers) && 
    !empty($data->categoryId) && 
    isset($data->inputType)
){
    // set survey property values
    $survey->question = $data->question;
    $survey->answers = $data->answers;
    $survey->categoryId = $data->categoryId;
    $survey->inputType = $data->inputType;

    try{
        $survey->id = $survey->create();

        if($survey->id != 0){
            $member_arr = $member->readAll()->fetchAll();
            $memberSurvey->createForMultipleMembers($member_arr, $survey->id);
        }

        // tell the user
        echo json_encode(array("succ" => true, "msg" => "Survey created successfully!"));
    }catch(Exception $e){
        if($survey->id > 0){
            $survey->remove();
        }
        echo json_encode(array("succ" => false, "msg" => "Unable to create survey. Something went wrong!", "error" => $e->getMessage()));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to create survey. Incomplete Data"));
}

?>