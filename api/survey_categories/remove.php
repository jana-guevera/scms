<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and survey_category file
require_once "../../config/database.php";
require_once "../../models/survey_category.php";

$database = Database::getInstance();
$surveyCategory = new SurveyCategory($database->getConnection());

// check if id exist
if(isset($_GET['id'])){
    $surveyCategory->id = $_GET['id'];
    $surveyCategory->status = 2;

    try{
        if($surveyCategory->remove()){
            // tell the user
            echo json_encode(array("succ" => true, "msg" => "Survey category removed successfully!"));
        }
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to remove survey category. Something went wrong!", "error" => $e->getMessage()));
    }

}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to remove survey category. Incomplete Data"));
}
?>