<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and survey file
require_once "../../config/database.php";
require_once "../../models/survey.php";

$database = Database::getInstance();
$survey = new Survey($database->getConnection());

$stmt = $survey->readAll();
$num = $stmt->rowCount();

// cehck if there are more than 0 rows
if($num > 0){
    // surveyies array
    $survey_array = array();
    $survey_array['records'] = array();

    // retrieve data
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $survey_item = array(
            "id" => $id,
            "question" => $question,
            "answers" => $answers,
            "categoryId" => $categoryId,
            "categoryName" => $categoryName,
            "inputType" => $inputType,
            "created_at" => $created_at,
            "updated_at" => $updated_at
        );

        array_push($survey_array['records'], $survey_item);
    }

    // send survey array data in json format
    echo json_encode($survey_array);
}else{
    // tell the user no surveyies were found
    echo json_encode(array('msg' => "No survey were found", 'records' => array()));
}


?>