<?php
session_start();
require('fpdf/fpdf.php');

// Database Connection
$conn = new mysqli("localhost", "root", "", "gasease");

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

$booking_id = $_GET['booking_id'];

// Get Booking Details
$sql = "SELECT * FROM bookings WHERE id='$booking_id'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

// Get Payment Details
$pay_sql = "SELECT * FROM payments WHERE booking_id='$booking_id'";
$pay_result = mysqli_query($conn, $pay_sql);
$payment = mysqli_fetch_assoc($pay_result);

// Calculate Amount based on cylinder type
$cylinder = $row['cylinder_type'];
if ($cylinder == "Domestic 14.2 KG") {
    $amount = 1100;
    $base_price = 877.21;
    $cgst = 14.14;
    $sgst = 14.14;
    $igst = 0;
} elseif ($cylinder == "Commercial 19 KG") {
    $amount = 2200;
    $base_price = 1754.42;
    $cgst = 28.29;
    $sgst = 28.29;
    $igst = 0;
} elseif ($cylinder == "Commercial 47.5 KG") {
    $amount = 5200;
    $base_price = 4145.75;
    $cgst = 66.86;
    $sgst = 66.86;
    $igst = 0;
} else {
    $amount = 1000;
    $base_price = 797.47;
    $cgst = 12.86;
    $sgst = 12.86;
    $igst = 0;
}

// Create PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetMargins(15, 15, 15);

// ===== HEADER - Logo Section =====
$pdf->SetFont('Arial', 'B', 24);
$pdf->SetTextColor(13, 27, 42);
$pdf->Cell(0, 12, 'GASEASE', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(75, 90, 102);
$pdf->Cell(0, 6, 'SINCE 2026', 0, 1, 'C');
$pdf->Ln(3);

// Decorative line
$pdf->SetDrawColor(13, 110, 253);
$pdf->SetLineWidth(0.5);
$pdf->Line(15, 35, 195, 35);
$pdf->Ln(8);

// ===== Business Details =====
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetTextColor(13, 27, 42);
$pdf->Cell(0, 8, 'GASEASE GAS AGENCY (0000107174)', 0, 1, 'C');

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(75, 90, 102);
$pdf->MultiCell(0, 5, "SHOP NO 3&4, GASEASE MAIN ROAD\n\"PUNE,411001 MAHARASHTRA\"", 0, 'C');
$pdf->Ln(1);

$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(13, 27, 42);
$pdf->Cell(0, 5, 'GSTN :: 07AAE00000', 0, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, 'Generated On :: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
$pdf->Ln(3);

// ===== Tax Invoice Title =====
$pdf->SetFont('Arial', 'B', 15);
$pdf->SetTextColor(13, 27, 42);
$pdf->Cell(0, 8, 'Tax Invoice', 0, 1, 'C');
$pdf->Ln(3);

// ===== Distributor Details =====
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(13, 27, 42);
$pdf->Cell(0, 6, 'Distributor Details', 0, 1, 'L');
$pdf->SetDrawColor(13, 27, 42);
$pdf->SetLineWidth(0.2);
$pdf->Line(15, 117, 195, 117);
$pdf->Ln(3);

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(75, 90, 102);
$pdf->Cell(30, 5, 'Name', 0, 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(13, 27, 42);
$pdf->Cell(0, 5, 'GASEASE GAS AGENCY', 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(75, 90, 102);
$pdf->Cell(30, 5, 'Address', 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(13, 27, 42);
$pdf->MultiCell(0, 4, "SHOP NO 3&4, PUNE MAIN ROAD\nPUNE 411001, MAHARASHTRA", 0, 'L');

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(75, 90, 102);
$pdf->Cell(30, 5, 'GSTN', 0, 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(13, 27, 42);
$pdf->Cell(0, 5, '07AAEFB5546L1Z9', 0, 1);
$pdf->Ln(2);

// ===== Customer Details =====
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(13, 27, 42);
$pdf->Cell(0, 6, 'Customer Details', 0, 1, 'L');
$pdf->SetDrawColor(13, 27, 42);
$pdf->SetLineWidth(0.2);
$pdf->Line(15, 150, 195, 150);
$pdf->Ln(3);

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(75, 90, 102);
$pdf->Cell(30, 5, 'Name', 0, 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(13, 27, 42);
$pdf->Cell(0, 5, htmlspecialchars($_SESSION['fullname'] ?? 'Customer'), 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(75, 90, 102);
$pdf->Cell(30, 5, 'Address', 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(13, 27, 42);
$pdf->MultiCell(0, 4, htmlspecialchars($row['address']), 0, 'L');

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(75, 90, 102);
$pdf->Cell(30, 5, 'GSTN', 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(13, 27, 42);
$pdf->Cell(0, 5, '-', 0, 1);
$pdf->Ln(3);

// ===== Product Table =====
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(13, 27, 42);
$pdf->Cell(0, 6, 'Product', 0, 1, 'L');

// Table Header
$pdf->SetFillColor(13, 27, 42);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 9);

$pdf->Cell(85, 8, 'Description', 1, 0, 'L', true);
$pdf->Cell(25, 8, 'Qty', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Unit', 1, 0, 'C', true);
$pdf->Cell(45, 8, 'Price', 1, 1, 'R', true);

// Table Body
$pdf->SetFillColor(248, 249, 250);
$pdf->SetTextColor(13, 27, 42);
$pdf->SetFont('Arial', '', 9);

$pdf->Cell(85, 7, htmlspecialchars($row['cylinder_type']), 1, 0, 'L');
$pdf->Cell(25, 7, '1', 1, 0, 'C');
$pdf->Cell(25, 7, 'EA', 1, 0, 'C');
$pdf->Cell(45, 7, 'Rs. ' . number_format($amount, 2), 1, 1, 'R');

$pdf->Ln(3);

// ===== Pricing Breakdown =====
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(75, 90, 102);

$pdf->Cell(120, 6, '', 0, 0);
$pdf->Cell(35, 6, 'Base Price', 0, 0, 'R');
$pdf->Cell(25, 6, 'Rs. ' . number_format($base_price, 2), 0, 1, 'R');

$pdf->Cell(120, 6, '', 0, 0);
$pdf->Cell(35, 6, 'Add: SGST', 0, 0, 'R');
$pdf->Cell(25, 6, 'Rs. ' . number_format($sgst, 2), 0, 1, 'R');

$pdf->Cell(120, 6, '', 0, 0);
$pdf->Cell(35, 6, 'Add: CGST', 0, 0, 'R');
$pdf->Cell(25, 6, 'Rs. ' . number_format($cgst, 2), 0, 1, 'R');

$pdf->Cell(120, 6, '', 0, 0);
$pdf->Cell(35, 6, 'Add: IGST/UTGST', 0, 0, 'R');
$pdf->Cell(25, 6, 'Rs. ' . number_format($igst, 2), 0, 1, 'R');

$pdf->Cell(120, 6, '', 0, 0);
$pdf->Cell(35, 6, 'Other Charges/Discounts', 0, 0, 'R');
$pdf->Cell(25, 6, 'Rs. 0.00', 0, 1, 'R');

// Total - Final Price
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(13, 27, 42);
$pdf->Cell(120, 7, '', 0, 0);
$pdf->Cell(35, 7, 'Final Price', 0, 0, 'R');
$pdf->SetTextColor(255, 122, 61);
$pdf->Cell(25, 7, 'Rs. ' . number_format($amount, 2), 0, 1, 'R');

// Net Due
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(13, 27, 42);
$pdf->Cell(120, 6, '', 0, 0);
$pdf->Cell(35, 6, 'Net Due', 0, 0, 'R');
$pdf->SetTextColor(40, 167, 69);
$pdf->Cell(25, 6, 'Rs. 0.00', 0, 1, 'R');

$pdf->Ln(2);

// ===== Payment Details =====
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(13, 27, 42);
$pdf->Cell(60, 6, 'Amount Paid', 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(30, 6, 'Rs. ' . number_format($amount, 2), 0, 0);

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(13, 27, 42);
$pdf->Cell(40, 6, 'Payment Method:', 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(13, 27, 42);
$pdf->Cell(0, 6, htmlspecialchars($payment['payment_method'] ?? 'Cash'), 0, 1);

$pdf->Ln(3);

// ===== Booking Status =====
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(13, 27, 42);
$pdf->Cell(35, 5, 'Booking ID:', 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(35, 5, '#' . str_pad($row['id'], 4, '0', STR_PAD_LEFT), 0, 0);

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(13, 27, 42);
$pdf->Cell(30, 5, 'Status:', 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(40, 167, 69);
$pdf->Cell(0, 5, 'PAID', 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(75, 90, 102);
$pdf->Cell(35, 5, 'Payment Date:', 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(13, 27, 42);
$pdf->Cell(0, 5, date('d/m/Y H:i:s'), 0, 1);

$pdf->Ln(5);

// ===== Footer =====
$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(150, 150, 150);
$pdf->Cell(0, 5, 'This is a computer generated document and needs no signatures.', 0, 1, 'C');
$pdf->SetTextColor(255, 122, 61);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 6, 'Stay Home, Stay Safe.', 0, 1, 'C');
$pdf->SetFont('Arial', 'I', 7);
$pdf->SetTextColor(150, 150, 150);
$pdf->Cell(0, 5, 'Thank You for Choosing GasEase!', 0, 1, 'C');

// ===== Output PDF =====
$pdf->Output('D', 'GasEase_Receipt_' . $booking_id . '.pdf');

$conn->close();
?>