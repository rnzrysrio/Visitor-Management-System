<?php
require('../dependencies/fpdf.php');
include('db.php');

$from = isset($_POST['from']) ? $_POST['from'] : '';
$to = isset($_POST['to']) ? $_POST['to'] : '';

if ($from && $to) {
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE visit_date BETWEEN ? AND ?");
    $stmt->bind_param("ss", $from, $to);
} else {
    $stmt = $conn->prepare("SELECT * FROM appointments");
}

$stmt->execute();
$result = $stmt->get_result();

$pdf = new FPDF('L', 'mm', 'Legal');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);  // Smaller font size for more compact text
$pdf->Cell(0, 10, 'Appointment Report', 0, 1, 'C');
if ($from && $to) {
    $pdf->Cell(0, 10, "From: $from To: $to", 0, 1, 'C');
}
$pdf->Ln(5);

// Reduce header font size and cell width
$pdf->SetFont('Arial', 'B', 7);
$headers = ['Name', 'Email', 'Phone', 'Visit Date', 'Check-In', 'Check-Out', 'Purpose', 'Department', 'Visit Status', 'Approval Status', 'Attendance', 'Encoder'];
foreach ($headers as $header) {
    $pdf->Cell(28, 6, $header, 1);  // Reduced width and height of each header
}
$pdf->Ln();

// Set font for content with smaller size
$pdf->SetFont('Arial', '', 6.5);  // Smaller font size for content
while ($row = $result->fetch_assoc()) {
    $visit_status = $row['visit_status'] == '1' ? 'Checked-In' : ($row['visit_status'] == '2' ? 'Reserved' : 'Checked-Out');
    $approval_status = $row['appointment_status'] == '1' ? 'Approved' : ($row['appointment_status'] == '2' ? 'Pending' : 'Denied');
    $attendance = $row['attendance'] == '1' ? 'Attended' : ($row['attendance'] == '2' ? 'Pending' : 'Did Not Attend');

    // Position the first 6 cells using Cell with smaller width and height
    $pdf->Cell(28, 6, $row['name'], 1);
    $pdf->Cell(28, 6, $row['email'], 1);
    $pdf->Cell(28, 6, $row['phone'], 1);
    $pdf->Cell(28, 6, $row['visit_date'], 1);
    $pdf->Cell(28, 6, $row['checkin_time'], 1);
    $pdf->Cell(28, 6, $row['checkout_time'], 1);

    // Handle the 'Purpose' column (we'll keep it as is for now)
    $pdf->Cell(28, 6, substr($row['purpose'], 0, 20), 1); // Truncate long text for compactness
    
    // Continue with the other cells (use smaller size)
    $pdf->Cell(28, 6, $row['department'], 1);
    $pdf->Cell(28, 6, $visit_status, 1);
    $pdf->Cell(28, 6, $approval_status, 1);
    $pdf->Cell(28, 6, $attendance, 1);
    $pdf->Cell(28, 6, $row['encoder'], 1);
    $pdf->Ln();
}

$pdf->Output();
$stmt->close();
?>
