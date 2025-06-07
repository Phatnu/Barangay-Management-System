<?php
require('../fpdf186/fpdf.php');
include_once("../connections/connection.php");
$con = connection();

// Get resident ID
$id = $_GET['id'] ?? null;
if (!$id) die("No ID provided.");

// Fetch official position data
// Fetch official position data (Barangay Captain)
$sql = "SELECT * FROM OFFICIAL WHERE POSITION = 'Barangay captain (punong barangay)'";
$result1 = mysqli_query($con, $sql);

if (!$result1 || mysqli_num_rows($result1) == 0) {
    $barangay_captain = "Barangay Captain";
} else {
    $rows = mysqli_fetch_assoc($result1);
    $barangay_captain = $rows['FULL_NAME'];

}






// Fetch resident data
$sql = "SELECT * FROM RESIDENT WHERE RESIDENT_ID = '$id'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);
if (!$row) die("Resident not found.");

// Assign values from DB
$name = $row['FIRST_NAME'] . ' ' . $row['LAST_NAME'];
$birth_day = $row['BIRTH_DATE'];
$gender = $row['GENDER'];
$address = $row['ADDRESS'] ?? 'BARANGAY 287';
$purpose = $row['purpose'] ?? 'scholarship application'; // Optional field
$profile = $row['PROFILE'];
$age = $row['AGE'];
$date_issued = date("F j, Y");
// $barangay_captain = "Hon. Jose L. Santos";

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

// --- ADD BORDER ---
$pdf->SetDrawColor(128, 128, 128);
$pdf->SetLineWidth(0.2);
$pdf->Rect(1, 1, 208, 295);

// --- BACKGROUND IMAGE (centered) ---
$bgPath = '../images/manila.png';
if (file_exists($bgPath)) {
    $pageW = 210; $pageH = 297; $imgW = 185; $imgH = 180;
    $x = ($pageW - $imgW) / 2;
    $y = ($pageH - $imgH) / 2;
    $pdf->Image($bgPath, $x, $y, $imgW, $imgH);
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
$pdf->Cell(0, 10, 'CERTIFICATE OF INDIGENCY', 0, 1, 'C');
$pdf->Ln(8);

// --- BODY TEXT ---
$pdf->SetFont('Arial', '', 12);
$body = "TO WHOM IT MAY CONCERN:\n\nThis is to certify that $name, $gender, $age years old, born on $birth_day, is a resident of $address and is considered to be part of an indigent family.\n\nThis certification is issued upon the request of the above-named person for the purpose of $purpose.\n\nIssued this $date_issued at $address.";
$pdf->MultiCell(0, 10, $body, 0, 'J');
$pdf->Ln(20);

// --- SIGNATURE ---
// Define the width of the signature block (you can adjust if needed)
$blockWidth = 60;

// Move X to center the block within the right half of the page
$pageWidth = $pdf->GetPageWidth();
$rightCenterX = $pageWidth * 0.75 - ($blockWidth / 2); // Center of right half

// Barangay Captain Name
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetX($rightCenterX);
$pdf->Cell($blockWidth, 2, strtoupper($barangay_captain), 0, 1, 'C');

// Signature Line
$pdf->SetFont('Arial', '', 12);
$pdf->SetX($rightCenterX);
$pdf->Cell($blockWidth, 5, '__________________________', 0, 1, 'C');

// Title
$pdf->SetFont('Arial', '', 11);
$pdf->SetX($rightCenterX);
$pdf->Cell($blockWidth, 10, 'PUNONG BARANGAY', 0, 1, 'C');


// --- RIGHT THUMB MARK AND SIGNATURE BOX start ---

// Set X and Y for the box (bottom-right side of page)
$thumbX = 150; // Adjust X position
$thumbY = 230; // Adjust Y position

// Draw a rectangle box for the thumb mark
$pdf->Rect($thumbX, $thumbY, 40, 30); // X, Y, Width, Height

// Add the label inside the box
$pdf->SetXY($thumbX, $thumbY + 13);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 20, 'Right Thumb Mark', 0, 0, 'C');

// Add the resident name below the box
$pdf->SetXY($thumbX, $thumbY + 35);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 5, $name, 0, 2, 'C');

// Label for signature
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 5, "Tax Payer's Signature", 0, 0, 'C');
// --- RIGHT THUMB MARK AND SIGNATURE BOX end ---



// --- PROFILE PICTURE (optional) ---
if ($profile && file_exists("Image/$profile")) {
    $pdf->Image("Image/$profile", 10, 80, 30, 30);
}

// --- ADD FOOTER IMAGE ---
// Add the orange wave image at the bottom of the page
$pdf->Image('../images/new_bg.png', 0, 255, 210); // Adjust Y position as needed

// Only call Output once, at the very end
$pdf->Output('I', 'certificate.pdf');
?>
