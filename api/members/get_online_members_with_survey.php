<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and member file
require_once "../../config/database.php";
require_once "../../models/member.php";
require_once "../../models/member_survey.php";

$database = Database::getInstance();
$member = new Member($database->getConnection());
$memberSurvey = new MemberSurvey($database->getConnection());

$stmt = $member->readOnlineMembers();
$num = $stmt->rowCount();

// cehck if there are more than 0 rows
if($num > 0){
    // members array
    $member_array = array();
    $member_array['records'] = array();

    // retrieve data
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $memberSurvey->memberId = $id;

        $member_item = array(
            "id" => $id,
            "name" => $name,
            "NIC" => $NIC,
            "gender" => $gender,
            "email" => $email,
            "mobileNo" => $mobileNo,
            "address" => $address,
            "memberType" => $memberType,
            "image" => $image,
            "memberSurvey" => $memberSurvey->readAllMemberSurvey()->fetchALL(),
            "status" => $status,
            "created_at" => $created_at,
            "updated_at" => $updated_at
        );

        array_push($member_array['records'], $member_item);
    }

    // send member array data in json format
    echo json_encode($member_array);
}else{
    // tell the user no members were found
    echo json_encode(array('msg' => "No members were found", 'records' => array()));
}


?>