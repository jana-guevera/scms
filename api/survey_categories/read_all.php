<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and survey_category file
require_once "../../config/database.php";
require_once "../../models/survey_category.php";

$database = Database::getInstance();
$surveyCategory = new SurveyCategory($database->getConnection());

$stmt = $surveyCategory->readAll();
$num = $stmt->rowCount();

// cehck if there are more than 0 rows
if($num > 0){
    // surveyCategoryies array
    $surveyCategory_array = array();
    $surveyCategory_array['records'] = array();

    // retrieve data
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $surveyCategory_item = array(
            "id" => $id,
            "name" => $name,
            "description" => $description,
            "status" => $status,
            "created_at" => $created_at,
            "updated_at" => $updated_at
        );

        array_push($surveyCategory_array['records'], $surveyCategory_item);
    }

    // send surveyCategory array data in json format
    echo json_encode($surveyCategory_array);
}else{
    // tell the user no surveyCategoryies were found
    echo json_encode(array('msg' => "No survey categories were found", 'records' => array()));
}


?>