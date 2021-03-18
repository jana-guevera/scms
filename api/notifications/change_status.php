<?php 
// required headers
header("Access-Control-Allow-Origin: http://localhost/friends_finder/");
header("Content-Type: application/json; charset=UTF-8");

// get database and notification file
require_once "../../config/database.php";
require_once "../../models/notification.php";

$database = Database::getInstance();
$notification = new Notification($database->getConnection());

// check if data exist
if(isset($_GET['id'])){
    $notification->memberId = $_GET['id'];
    $notification->changeStatus();
}
?>