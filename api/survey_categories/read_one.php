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

    $stmt = $surveyCategory->readOne();
    $num = $stmt->rowCount();

    // cehck if a surveyCategory exist
    if($num == 1){
        // retrieve data
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        extract($row);

        $category_item = array(
            "id" => $id,
            "name" => $name,
            "description" => $description,
            "status" => $status,
            "created_at" => $created_at,
            "updated_at" => $updated_at
        );

        // send surveyCategory data in json format
        echo json_encode(array("succ" => true, "record" => $category_item));
    }else{
        // tell the user no members were found
        echo json_encode(array("succ" => true, 'msg' => "No survey category were found"));
    }
}else{
    // tell the user incomplete data
    echo json_encode(array("succ" => false, "msg" => "Unable to fetch survey category. Incomplete Data"));
}
