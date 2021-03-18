<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods, Content-Type, Authorization, X-Requested-With");

// get database and survey file
require_once "../../config/database.php";
require_once "../../models/member_survey.php";

$database = Database::getInstance();
$memberSurvey = new MemberSurvey($database->getConnection());

// get send data
$data = json_decode(file_get_contents("php://input"), true);

if(count($data) > 0){
    try{
        $memberSurvey->answerSurvey($data);

        // tell the user
        echo json_encode(array("succ" => true, "msg" => "Answers updated successfully!"));
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to update answers. Something went wrong!", "error" => $e->getMessage()));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to update answers. Incomplete Data"));
}

?>
