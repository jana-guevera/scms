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
    !empty($data->name) && 
    !empty($data->description)
){
    // set category property values
    $surveyCategory->name = $data->name;
    $surveyCategory->description = $data->description;

    //check if surveyCategory already exist 
    try{
        if($surveyCategory->isUnique()){
            $surveyCategory->create();
            // tell the user
            echo json_encode(array("succ" => true, "msg" => "Survey category created successfully!"));
        }else{
            // tell the user category already exist
            echo json_encode(array("succ" => false, "msg" => "Unable to create survey category. Category already exist"));
        }
    }catch(Exception $e){
        echo json_encode(array("succ" => false, "msg" => "Unable to create survey category. Something went wrong!", "error" => $e->getMessage()));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to create survey category. Incomplete Data"));
}

?>