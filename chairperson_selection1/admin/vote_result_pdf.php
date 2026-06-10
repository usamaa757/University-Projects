<?php
require_once('../tcpdf/tcpdf.php');
include '../db_connection.php';

// Fetch candidate results and order by votes in descending order
$query = "SELECT c.name, c.department, c.party, COUNT(v.candidate_id) as votes 
          FROM candidates c 
          LEFT JOIN votes v ON c.candidate_id = v.candidate_id 
          GROUP BY c.candidate_id 
          ORDER BY votes DESC";
$result = $conn->query($query);

// Create a new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Candidate Results');
$pdf->SetSubject('Results of the Election');
$pdf->SetKeywords('TCPDF, PDF, election, result, candidate');

// Set default header data
$pdf->SetHeaderData(
    '', // Leave this empty if you don't want to set a logo
    0, // Width of the logo. Set to 0 if no logo is used.
    'Digital Electoral System for Chairperson Selection', // Header title
    'Election Results' // Header string
);

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// Title
$pdf->Cell(0, 10, 'Election Results', 0, 1, 'C');
$pdf->Ln(10);

// Table header
$html = '<table border="1" cellspacing="3" cellpadding="4">
    <tr>
        <th><b>Name</b></th>
        <th><b>Department</b></th>
        <th><b>Party</b></th>
        <th><b>Votes</b></th>
    </tr>';

// Fetch and display the results
while ($row = $result->fetch_assoc()) {
    $html .= '<tr>
        <td>' . htmlspecialchars($row['name']) . '</td>
        <td>' . htmlspecialchars($row['department']) . '</td>
        <td>' . htmlspecialchars($row['party']) . '</td>
        <td>' . htmlspecialchars($row['votes']) . '</td>
    </tr>';
}

$html .= '</table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('candidate_results.pdf', 'I');

// Close database connection
$conn->close();
?>
