<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and survey file
require_once "../../config/database.php";
require_once "../../models/survey.php";

$database = Database::getInstance();
$survey = new Survey($database->getConnection());

// check if id exist
if(isset($_GET['id'])){
    $survey->id = $_GET['id'];

    $stmt = $survey->readOne();
    $num = $stmt->rowCount();

    // cehck if a survey exist
    if($num == 1){
        // retrieve data
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
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

        // send survey data in json format
        echo json_encode(array("succ" => true, "record" => $survey_item));
    }else{
        // tell the user no survey were found
        echo json_encode(array("succ" => true, 'msg' => "No survey were found"));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to fetch survey. Incomplete Data"));
}
