<?php
session_start();
require('../fpdf186/fpdf.php');
include_once("../connections/connection.php");
$con = connection();

// Get blotter ID
$id = $_GET['id'] ?? null;
if (!$id) die("No ID provided.");

// Fetch Barangay Captain
$sql = "SELECT * FROM OFFICIAL WHERE POSITION = 'Captain'";
$result1 = mysqli_query($con, $sql);
if (!$result1 || mysqli_num_rows($result1) == 0) {
    $barangay_captain = "Barangay Captain";
} else {
    $rows = mysqli_fetch_assoc($result1);
    $barangay_captain = $rows['FULL_NAME'];
}

// Fetch blotter data
$sql = "SELECT * FROM BLOTTER2 WHERE ID = '$id'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);
if (!$row) die("Blotter record not found.");

// Assign values from DB
$complainant = $row['COMPLAINANT_NAME'];
$respondent = $row['RESPONDENT'];
$victim = $row['VICTIM'];
$incident_type = $row['INCIDENT_TYPE'];
$incident_date = $row['INCIDENT_DATE'];
$incident_time = $row['INCIDENT_TIME'];
$location = $row['INCIDENT_LOCATION'];
$status = $row['STATUS'];
$description = $row['INCIDENT_DESCRIPTION'];
$date_sched = $row['DATE_SCHEDULE'];
$time_sched = $row['TIME_SCHEDULE'];

$date_issued = date("F j, Y");


// ✅ INSERT INTO activity_log start
if (isset($_SESSION['STAFFID'])) {
    $staffId = $_SESSION['STAFFID'];
    $action = "Print";
    $log_desc = "Printed blotter certificate for complainant: $complainant";
    $targetTable = "BLOTTER2";
    $targetId = $id;
    $targetName = $complainant;

    $stmtLog = $con->prepare("INSERT INTO activity_log 
        (STAFFID, ACTION_TYPE, ACTION_DESCRIPTION, TARGET_TABLE, TARGET_ID, TARGET_NAME) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmtLog->bind_param("isssis", $staffId, $action, $log_desc, $targetTable, $targetId, $targetName);
    $stmtLog->execute();
    $stmtLog->close();
}
// ✅ INSERT INTO activity_log end


$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// --- ADD BORDER ---
$pdf->SetDrawColor(128, 128, 128);
$pdf->SetLineWidth(0.2);
$pdf->Rect(1, 1, 208, 295);

// --- BACKGROUND IMAGE ---
$bgPath = '../images/san_marcos_bg.png';
if (file_exists($bgPath)) {
    $pdf->Image($bgPath, 12.5, 58.5, 185, 180);
}

// --- HEADER LOGOS ---
$pdf->Image('../images/calumpit.png', 15, 10, 25);
$pdf->Image('../images/malolos.png', 170, 10, 25);

// --- OFFICIAL HEADER ---
$pdf->SetY(12);
$pdf->SetFont('Times', '', 10);
$pdf->Cell(0, 5, 'REPUBLIC OF THE PHILIPPINES', 0, 1, 'C');

$pdf->SetFont('Times', 'B', 13);
$pdf->Cell(0, 6, 'PROVINCE OF BULACAN', 0, 1, 'C');

$pdf->SetFont('Times', '', 11);
$pdf->Cell(0, 6, 'MUNICIPALITY OF CALUMPIT', 0, 1, 'C');

$pdf->SetFont('Times', '', 11);
$pdf->Cell(0, 6, 'BARANGAY SAN MARCOS', 0, 1, 'C');

$pdf->SetFont('Times', 'B', 14);
$pdf->Cell(0, 6, 'OFFICE OF THE PUNONG BARANGAY', 0, 1, 'C');

// --- LINE ---
$pdf->SetDrawColor(50, 50, 50);
$pdf->SetLineWidth(0.3);
$pdf->Line(20, $pdf->GetY() + 3, 190, $pdf->GetY() + 3);
$pdf->Ln(10);

// --- TITLE ---
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'BLOTTER RECORD CERTIFICATE', 0, 1, 'C');
$pdf->Ln(8);

// --- BODY TEXT ---
$pdf->SetFont('Arial', '', 12);
$body = "This is to certify that an incident has been recorded in the Barangay Blotter involving:\n\n" .
    "Complainant: $complainant\n" .
    "Respondent: $respondent\n" .
    "Victim: $victim\n" .
    "Type of Incident: $incident_type\n" .
    "Date & Time: $incident_date at $incident_time\n" .
    "Location: $location\n\n" .
    "Incident Description:\n$description\n\n";

if ($status === "Scheduled") {
    $body .= "A hearing is scheduled on $date_sched at $time_sched.\n\n";
}

$body .= "Status: $status\n\n" .
    "Issued this $date_issued at Barangay San Marcos, Calumpit, Bulacan.";

$pdf->MultiCell(0, 10, $body, 0, 'J');
$pdf->Ln(20);

// --- SIGNATURE BLOCK ---
$blockWidth = 60;
$pageWidth = $pdf->GetPageWidth();
$rightCenterX = $pageWidth * 0.75 - ($blockWidth / 2);

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetX($rightCenterX);
$pdf->Cell($blockWidth, 2, strtoupper($barangay_captain), 0, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->SetX($rightCenterX);
$pdf->Cell($blockWidth, 5, '__________________________', 0, 1, 'C');

$pdf->SetFont('Arial', '', 11);
$pdf->SetX($rightCenterX);
$pdf->Cell($blockWidth, 10, 'PUNONG BARANGAY', 0, 1, 'C');

// --- THUMB MARK BOX ---
// $thumbX = 150; $thumbY = 230;
// $pdf->Rect($thumbX, $thumbY, 40, 30);
// $pdf->SetXY($thumbX, $thumbY + 13);
// $pdf->SetFont('Arial', '', 10);
// $pdf->Cell(40, 20, 'Right Thumb Mark', 0, 0, 'C');

// --- FOOTER IMAGE ---
$pdf->Image('../images/new_bg.png', 0, 255, 210);

// --- OUTPUT ---
$pdf->Output('I', 'blotter_certificate.pdf');
?>
