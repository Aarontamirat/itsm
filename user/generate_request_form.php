<?php
require_once('../libs/tcpdf/tcpdf.php');
session_start();

if (!isset($_POST['selected']) || empty($_POST['selected'])) {
    $_SESSION['error'] = 'No Selected Incident';
    header("Location: my_incident_history.php");
    exit;
}

// Create new PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Lucy Insurance');
$pdf->SetTitle('Maintenance/Repair Request Form');
$pdf->SetMargins(15, 20, 15);
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 10); // Use a Unicode font that supports checkbox symbols

$date = date('d/m/Y');

// Insert logo centered at the top using TCPDF's Image() method
$logoPath = '../uploads/letterHeader.jpg'; // Adjust path as needed
if (file_exists($logoPath)) {
    // Get page width and image width to center the image
    $pageWidth = $pdf->getPageWidth() - $pdf->getMargins()['left'] - $pdf->getMargins()['right'];
    $imgWidth = 190; // width in mm, adjust as needed
    $x = ($pdf->getPageWidth() - $imgWidth) / 2;
    $y = 10; // top margin in mm
    $pdf->Image($logoPath, $x, $y, $imgWidth, 0, '', '', 'T', false, 300, '', false, false, 0, false, false);

    // Add margin bottom of about 100px (~35mm)
    $pdf->SetY($y + 35);
}

// Improved, compact, and spaced styling for the PDF form
$html = '
<style>
    body { font-family: dejavusans, sans-serif; }
    .header { text-align: right; font-size: 10px; margin-bottom: 8px; }
    .title { text-align: center; font-size: 15px; font-weight: bold; margin-bottom: 6px; letter-spacing: 1px; }
    .subtitle { 
        text-align: left; 
        font-size: 11px; 
        margin-bottom: 12px;
    }
    table.form-table {
        border-collapse: collapse;
        width: 100%;
        margin: 18px 0px;
        font-size: 10px;
    }
    table.form-table th, table.form-table td {
        border: 1px solid #d0d0d0;
        padding: 4px 3px;
        text-align: left;
    }
    table.form-table th {
        background-color: #f2f2f2;
        font-weight: bold;
        color: #222;
    }
    .sign-table {
        width: 100%;
        margin-top: 40px;
        font-size: 10px;
        border-spacing: 12px 0;
    }
    .sign-cell {
        width: 48%;
        vertical-align: top;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        padding: 10px 8px;
        background: #fcfcfc;
    }
    .action-section {
        margin-top: 18px;
        font-size: 10px;
        padding: 10px 8px;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        background: #fafafa;
    }
    .checkbox-label {
        margin-right: 18px;
        font-size: 10px;
        vertical-align: middle;
    }
    .underline {
        border-bottom: 1px solid #888;
        display: inline-block;
        min-width: 120px;
        height: 13px;
        vertical-align: middle;
        margin-bottom: 2px;
    }
    .branch-name {
        text-decoration: underline;
    }
    .spacer { height: 8px; display: block; }
</style>
<div class="header"><strong>Date:</strong> ' . $date . '</div>
<div class="title">Maintenance Request Form</div>
<div class="subtitle">
    <strong>Division:</strong>____________ &nbsp;&nbsp; 
    <strong>Department:</strong> _______________ &nbsp;&nbsp; 
    <strong>Branch: </strong><span class="branch-name">'. $_SESSION['branch_name'] .' </span>
</div>
<span class="spacer"></span>
<br>
<table class="form-table">
    <tr>
        <th>Ser No.</th>
        <th>Description of Item</th>
        <th>Tag No.</th>
        <th>Type of Problem</th>
        <th>Date Problem Detected</th>
        <th>Action Required</th>
    </tr>';

foreach ($_POST['selected'] as $i => $jsonIncident) {
    $incident = json_decode($jsonIncident, true);

    $html .= '<tr>
        <td>' . ($i + 1) . '</td>
        <td>' . htmlspecialchars($incident['name']) . '</td>
        <td></td>
        <td>' . htmlspecialchars($incident['description']) . '</td>
        <td>' . date('d/m/Y', strtotime($incident['created_at'])) . '</td>
        <td></td>
    </tr>';
}
$html .= '</table><br><br>';

$html .= '<table cellpadding="10" width="100%">
<tr cellp40dding="5" width="100%"><td style="height:80px;"><strong>Requested by</strong><br><br>Name: _______________________________<br><br>Signature: _______________________________</td>
<td style="height:80px;"><strong>Approved by</strong><br><br>Name: _______________________________<br><br>Signature: _______________________________</td></tr>
</table><br><br><br>';

$html .= '<strong style="text-align:center; text-decoration:underline; font-size: 12px;">Action Taken by HR & Logistics Division</strong><br><br>
&#9744; Request Approved &nbsp;&nbsp;&nbsp;&nbsp; 
&#9744; Rejected<br><br>
Description of Corrective Action taken:_______________________________________________
______________________________________________________________________________________________
______________________________________________________________________________________________
_______________________________________________<br><br>
Owner of Action Process';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('maintenance_request_form.pdf', 'I');
