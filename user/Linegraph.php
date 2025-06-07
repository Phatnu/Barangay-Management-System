<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "barangay_system";

$con = new mysqli($hostname, $username, $password, $database);

if ($con->connect_error) {
    echo $con->connect_error;
    exit;
}

// Group by month only, focusing on the data in 2025
$sql = "SELECT YEAR(DATE) as year,MONTH(DATE) as month,COUNT(*) as total FROM resident GROUP BY year, month ORDER BY year, month ASC";

$result = $con->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    // Create a date string for the first day of each month
    $monthStr = sprintf("%04d-%02d-01", $row['year'], $row['month']);
    
    $data[] = [
        'month' => $monthStr,
        'total' => $row['total']
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
?>
