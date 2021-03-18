<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods, Content-Type, Authorization, X-Requested-With");

// get database and survey_category file
require_once "../../config/database.php";
require_once "../../models/survey_category.php";

$database = Database::getInstance();
$surveyCategory = new SurveyCategory($database->getConnection());

// get send data
$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
if(
    !empty($data->id) && 
    !empty($data->name) && 
    !empty($data->description) && 
    isset($data->status)
){
    // set category property values
    $surveyCategory->id = $data->id;
    $surveyCategory->name = $data->name;
    $surveyCategory->description = $data->description;
    $surveyCategory->status = $data->status;

    //check if surveyCategory already exist 
    try{
        if($surveyCategory->isUniqueOnUpdate()){
            $surveyCategory->update();
            // tell the user
            echo json_encode(array("succ" => true, "msg" => "Survey category updated successfully!"));
        }else{
            // tell the user category already exist
            echo json_encode(array("succ" => false, "msg" => "Unable to update survey category. Category already exist"));
        }
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to update survey category. Something went wrong!", "error" => $e->getMessage()));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to update survey category. Incomplete Data"));
}

?>