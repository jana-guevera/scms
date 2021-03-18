<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and survey file
require_once "../../config/database.php";
require_once "../../models/survey.php";
require_once "../../models/member_survey.php";

$database = Database::getInstance();
$survey = new Survey($database->getConnection());
$memberSurvey = new MemberSurvey($database->getConnection());

// check if id exist
if(isset($_GET['id'])){
    $survey->id = $_GET['id'];
    $survey->status = 2;
    $memberSurvey->surveyId = $survey->id;

    try{
        if($memberSurvey->removeAll()){
            $survey->remove();

            // tell the user
            echo json_encode(array("succ" => true, "msg" => "Survey removed successfully!"));
        }
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to remove survey. Something went wrong!", "error" => $e->getMessage()));
    }

}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to remove survey. Incomplete Data"));
}
?>