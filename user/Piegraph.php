<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "barangay_system";

$con = new mysqli($hostname, $username, $password, $database);

if ($con->connect_error) {
    echo json_encode(['error' => $con->connect_error]);
    exit;
}

// ✅ Get total male and female residents
$sql = "
    SELECT GENDER as gender, COUNT(*) as total 
    FROM resident 
    WHERE GENDER IN ('Male', 'Female') 
    GROUP BY GENDER";

$result = $con->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'gender' => $row['gender'],
        'total' => $row['total']
    ];
}
header('Content-Type: application/json');
echo json_encode($data);

?>
