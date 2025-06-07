<?php
// --- Database configuration ---
$host = "localhost";
$username = "root";
$password = "";
$database_name = "BARANGAY_SYSTEM";

// --- Connect to MySQL ---
$conn = new mysqli($host, $username, $password, $database_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");

// --- Get all table names ---
$tables = [];
$result = $conn->query("SHOW TABLES");
if (!$result) {
    die("Error retrieving tables: " . $conn->error);
}
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

// --- Start SQL script ---
$sqlScript = "";

foreach ($tables as $table) {
    // --- Get CREATE TABLE script ---
    $result = $conn->query("SHOW CREATE TABLE `$table`");
    if (!$result) continue;
    $row = $result->fetch_row();

    // Add DROP + CREATE
    $sqlScript .= "\n\nDROP TABLE IF EXISTS `$table`;\n";
    $sqlScript .= $row[1] . ";\n\n";

    // --- Table data ---
    $result = $conn->query("SELECT * FROM `$table`");
    if (!$result) continue;

    $columnCount = $result->field_count;

    while ($row = $result->fetch_row()) {
        $sqlScript .= "INSERT INTO `$table` VALUES(";
        for ($j = 0; $j < $columnCount; $j++) {
            $value = isset($row[$j]) ? $conn->real_escape_string($row[$j]) : "";
            $sqlScript .= '"' . $value . '"';
            if ($j < $columnCount - 1) {
                $sqlScript .= ',';
            }
        }
        $sqlScript .= ");\n";
    }

    $sqlScript .= "\n";
}

// --- Write to .sql file ---
if (!empty($sqlScript)) {
    $backup_file_name = $database_name . '_backup_' . date("Y-m-d_H-i-s") . '.sql';

    $fileHandler = fopen($backup_file_name, 'w+');
    if (!$fileHandler) {
        die("Error creating backup file.");
    }

    fwrite($fileHandler, $sqlScript);
    fclose($fileHandler);

    // --- Download the backup file ---
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($backup_file_name));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($backup_file_name));

    ob_clean();
    flush();
    readfile($backup_file_name);

    // Delete after download (optional)
    unlink($backup_file_name);
    exit;
} else {
    echo "No data found in the database.";
}
?>
