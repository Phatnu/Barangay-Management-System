<?php
require('../fpdf186/fpdf.php');
include_once("../connections/connection.php");
$con = connection();

// Get business permit ID
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

// Fetch business permit data
$sql = "SELECT * FROM BUSINESS_PERMIT WHERE ID = '$id'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);
if (!$row) die("Business Permit not found.");

// Assign values
$business_owner = $row['BUSINESS_OWNER'];
$business_name = $row['BUSINESS_NAME'];
$business_type = $row['BUSINESS_TYPE'];
$business_address = $row['BUSINESS_ADDRESS'];
$status = $row['STATUS'];
$permit_num = $row['PERMIT_NUM'];
$date_issued = date("F j, Y");

// Fetch barangay and municipal logos
$info_query = mysqli_query($con, "SELECT * FROM barangay_info LIMIT 1");
$info = mysqli_fetch_assoc($info_query);

// $barangay_logo = '../images/default_barangay.png';
// $municipal_logo = '../images/default_municipal.png';

if (!empty($info['BARANGAY_LOGO']) && file_exists('../admin/barangayimage/' . $info['BARANGAY_LOGO'])) {
    $barangay_logo = '../admin/barangayimage/' . $info['BARANGAY_LOGO'];
}
if (!empty($info['MUNICIPAL_LOGO']) && file_exists('../admin/barangayimage/' . $info['MUNICIPAL_LOGO'])) {
    $municipal_logo = '../admin/barangayimage/' . $info['MUNICIPAL_LOGO'];
}


$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// Add Border
$pdf->SetDrawColor(128, 128, 128);
$pdf->SetLineWidth(0.2);
$pdf->Rect(1, 1, 208, 295);

// Background Image
$bgPath = '../images/manila.png';
if (file_exists($bgPath)) {
    $pageW = 210; $pageH = 297; $imgW = 185; $imgH = 180;
    $x = ($pageW - $imgW) / 2;
    $y = ($pageH - $imgH) / 2;
    $pdf->Image($bgPath, $x, $y, $imgW, $imgH);
}

// Header Logos
// $pdf->Image('../images/calumpit.png', 15, 10, 25);
// $pdf->Image('../images/malolos.png', 170, 10, 25);
$pdf->Image($barangay_logo, 15, 10, 25);
$pdf->Image($municipal_logo, 170, 10, 25);


// Official Header
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

// Line
$pdf->SetDrawColor(50, 50, 50);
$pdf->SetLineWidth(0.3);
$pdf->Line(20, $pdf->GetY() + 3, 190, $pdf->GetY() + 3);
$pdf->Ln(10);

// Title
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'BUSINESS CLEARANCE', 0, 1, 'C');
$pdf->Ln(8);

// Body Content
$pdf->SetFont('Arial', '', 12);
$body = "TO WHOM IT MAY CONCERN:\n\nThis is to certify that $business_owner, owner of $business_name, engaged in $business_type and located at $business_address, has been issued Business Permit No. $permit_num by BARANGAY 287.\n\nThis clearance is issued on $date_issued under the status '$status'.\n\nIssued at BARANGAY 287.";

$pdf->MultiCell(0, 10, $body, 0, 'J');
$pdf->Ln(20);

// Signature Block
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

// Thumb Mark Box
$thumbX = 150;
$thumbY = 230;
$pdf->Rect($thumbX, $thumbY, 40, 30);
$pdf->SetXY($thumbX, $thumbY + 13);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 20, 'Right Thumb Mark', 0, 0, 'C');

// Business Owner Signature Label
$pdf->SetXY($thumbX, $thumbY + 35);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 5, $business_owner, 0, 2, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 5, "Business Owner's Signature", 0, 0, 'C');

// Footer Image
$pdf->Image('../images/new_bg.png', 0, 255, 210);

$pdf->Output('I', 'business_clearance.pdf');
?>
