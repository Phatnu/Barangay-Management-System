<?php
session_start();
include_once("../connections/connection.php");
$con = connection();

if (!isset($_SESSION['STAFFID'])) {
    http_response_code(403);
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

$query = "SELECT ID, INCIDENT_TYPE, DATE_SCHEDULE, TIME_SCHEDULE, COMPLAINANT_NAME, INCIDENT_DESCRIPTION FROM BLOTTER2 
          WHERE DATE_SCHEDULE IS NOT NULL AND TIME_SCHEDULE IS NOT NULL";
$result = $con->query($query);

$events = [];

while ($row = $result->fetch_assoc()) {
    if (!empty($row['DATE_SCHEDULE']) && !empty($row['TIME_SCHEDULE'])) {
        $formattedTime = date("g:i A", strtotime($row['TIME_SCHEDULE']));
$events[] = [
    'id' => $row['ID'],
    'title' => "Hearing: " . $row['INCIDENT_TYPE'] . " ($formattedTime)",
    'start' => $row['DATE_SCHEDULE'],
    'complainant' => $row['COMPLAINANT_NAME'],
    'description' => $row['INCIDENT_DESCRIPTION'],
    'incident' => $row['INCIDENT_TYPE'], // Add this line
    'time' => $formattedTime
];
    }
}

header('Content-Type: application/json');
echo json_encode($events);
?>
