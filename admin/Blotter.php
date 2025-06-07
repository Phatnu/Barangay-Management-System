<?php
require('../fpdf186/fpdf.php');
include_once("../connections/connection.php");
$con = connection();

// Get blotter ID
$id = $_GET['id'] ?? null;
if (!$id) die("No ID provided.");

// Fetch Barangay Captain
$sql = "SELECT * FROM OFFICIAL WHERE POSITION = 'Barangay captain (punong barangay)'";
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

// Fetch barangay and municipal logos
$info_query = mysqli_query($con, "SELECT * FROM barangay_info LIMIT 1");
$info = mysqli_fetch_assoc($info_query);

// $barangay_logo = '../images/default_barangay.png';
// $municipal_logo = '../images/default_municipal.png';

if (!empty($info['BARANGAY_LOGO']) && file_exists('barangayimage/' . $info['BARANGAY_LOGO'])) {
    $barangay_logo = 'barangayimage/' . $info['BARANGAY_LOGO'];
}
if (!empty($info['MUNICIPAL_LOGO']) && file_exists('barangayimage/' . $info['MUNICIPAL_LOGO'])) {
    $municipal_logo = 'barangayimage/' . $info['MUNICIPAL_LOGO'];
}


$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// --- ADD BORDER ---
$pdf->SetDrawColor(128, 128, 128);
$pdf->SetLineWidth(0.2);
$pdf->Rect(1, 1, 208, 295);

// --- BACKGROUND IMAGE ---
$bgPath = '../images/manila.png';
if (file_exists($bgPath)) {
    $pdf->Image($bgPath, 12.5, 58.5, 185, 180);
}

// --- HEADER LOGOS ---
// Header logos from DB
$pdf->Image($barangay_logo, 15, 10, 25);
$pdf->Image($municipal_logo, 170, 10, 25);

// --- OFFICIAL HEADER ---
// Header text
$pdf->SetY(12);
$pdf->SetFont('Times', '', 10);
$pdf->Cell(0, 5, 'REPUBLIC OF THE PHILIPPINES', 0, 1, 'C');


$pdf->SetFont('Times', 'B', 13);
$pdf->Cell(0, 6, 'NATIONAL CAPITAL REGION', 0, 1, 'C');

$pdf->SetFont('Times', '', 11);
$pdf->Cell(0, 6, 'CITY OF MANILA', 0, 1, 'C');

$pdf->SetFont('Times', '', 11);
$pdf->Cell(0, 6, 'THIRD CONGRESSIONAL DISTRICT BINONDO', 0, 1, 'C');

$pdf->SetFont('Times', '', 11);
$pdf->Cell(0, 6, 'BARANGAY 287', 0, 1, 'C');

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
    "Issued this $date_issued at BARANGAY 287.";

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
