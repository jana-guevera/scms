<?php 

require_once "./config/database.php";
require_once "./models/member_event.php";
require_once "./models/event.php";

$database = Database::getInstance();
$memberEvent = new MemberEvent($database->getConnection());
$event = new Event($database->getConnection());

// fetch all atteding member events
$memberEvents = $memberEvent->readAllAttedingEvent()->fetchAll();
for($i = 0; $i < count($memberEvents); $i++){
    $memberEvent->id = $memberEvents[$i]['id'];
    $memberEvent->status = 2;
    $memberEvent->changeStatus();
}

// fetch all active events
$events = $event->readActiveCompletedEvents()->fetchAll();
for($i = 0; $i < count($events); $i++){
    $event->id = $events[$i]['id'];
    $event->status = 3;
    $event->changeStatus();
}


?>