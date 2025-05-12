<?php
require '../config/db.php';
require_once '../libs/tcpdf/tcpdf.php';

// Fetch incidents for PDF export
$stmt = $pdo->query("SELECT * FROM incidents ORDER BY created_at DESC");
$incidents = $stmt->fetchAll();

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// Add title
$pdf->Cell(0, 10, 'Incidents Report', 0, 1, 'C');

// Add table header
$pdf->Cell(20, 10, 'ID', 1, 0, 'C');
$pdf->Cell(40, 10, 'Title', 1, 0, 'C');
$pdf->Cell(40, 10, 'Priority', 1, 0, 'C');
$pdf->Cell(40, 10, 'Status', 1, 0, 'C');
$pdf->Cell(40, 10, 'Assigned To', 1, 0, 'C');
$pdf->Ln();

// Add incidents as table rows
foreach ($incidents as $incident) {
    $pdf->Cell(20, 10, $incident['id'], 1, 0, 'C');
    $pdf->Cell(40, 10, $incident['title'], 1, 0, 'C');
    $pdf->Cell(40, 10, $incident['priority'], 1, 0, 'C');
    $pdf->Cell(40, 10, $incident['status'], 1, 0, 'C');
    $pdf->Cell(40, 10, $incident['assigned_to'], 1, 0, 'C');
    $pdf->Ln();
}

// Output PDF as download
$pdf->Output('incidents_report.pdf', 'D');
exit;
