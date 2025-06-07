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

$sql = "
    SELECT YEAR(DATE) as year, MONTH(DATE) as month, COUNT(*) as total 
    FROM blotter2 
    GROUP BY year, month 
    ORDER BY year, month ASC";

$result = $con->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $monthStr = sprintf("%04d-%02d-01", $row['year'], $row['month']);
    $data[] = [
        'month' => $monthStr,
        'total' => $row['total']
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
?>
