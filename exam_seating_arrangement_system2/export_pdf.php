<?php
require_once('tcpdf/tcpdf.php');
include 'db.php';
session_start();

// Identify user type
$is_admin = isset($_SESSION['admin_id']);
$is_student = isset($_SESSION['student_id']);

if (!$is_admin && !$is_student) {
    die('Unauthorized access. Please log in.');
}

// Create TCPDF instance
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Exam Seating System');
$pdf->SetMargins(10, 20, 10);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->AddPage();

// --- Admin View ---
if ($is_admin) {
    $pdf->SetTitle('Seating Arrangement (Admin View)');
    $pdf->SetHeaderData('', 0, 'All Students Seating Arrangement', '');
    $pdf->setHeaderFont(['helvetica', '', 12]);
    $pdf->setFooterFont(['helvetica', '', 10]);

    // Fetch all seat data
    $seats = $conn->query("
        SELECT sa.*, s.student_name, s.course_id, c.course_name 
        FROM seating_arrangements sa
        JOIN students s ON sa.student_id = s.student_id
        LEFT JOIN courses c ON s.course_id = c.course_id
    ");

    // Organize seats into a grid
    $grid = [];
    $maxRow = $maxCol = 0;

    while ($seat = $seats->fetch_assoc()) {
        $r = (int)$seat['row'];
        $c = (int)$seat['columns'];
        $grid[$r][$c] = $seat;

        if ($r > $maxRow) $maxRow = $r;
        if ($c > $maxCol) $maxCol = $c;
    }

    // Generate grid HTML
    $html = '<h2 style="text-align:center;">Seating Arrangement Overview</h2>';
    $html .= '<table border="1" cellpadding="4" cellspacing="0">';
    $html .= '<thead><tr><th></th>';

    for ($c = 1; $c <= $maxCol; $c++) {
        $html .= '<th><b>Col ' . $c . '</b></th>';
    }
    $html .= '</tr></thead><tbody>';

    for ($r = 1; $r <= $maxRow; $r++) {
        $html .= '<tr><th><b>Row ' . $r . '</b></th>';
        for ($c = 1; $c <= $maxCol; $c++) {
            if (isset($grid[$r][$c])) {
                $seat = $grid[$r][$c];
                $html .= '<td style="background-color:#f5f5f5;">';
                $html .= '<strong>' . htmlspecialchars($seat['student_name'] ?? 'N/A') . '</strong><br>';
                $html .= '<small>' . htmlspecialchars($seat['course_name'] ?? 'No Course') . '</small><br>';
                $html .= '<span style="font-size:10px;">ID: ' . htmlspecialchars($seat['student_id']) . '</span><br>';
                $html .= '<span style="font-size:10px;">Seat No: ' . htmlspecialchars($seat['seat_number']) . '</span>';
                $html .= '</td>';
            } else {
                $html .= '<td style="background-color:#ffffff;">&nbsp;</td>';
            }
        }
        $html .= '</tr>';

        if ($r < $maxRow) {
            $html .= '<tr>';
            $html .= '<td colspan="' . ($maxCol + 1) . '" style="height:10px; background-color:#ffffff;"></td>';
            $html .= '</tr>';
        }
    }

    $html .= '</tbody></table>';
}

// --- Student View ---
if ($is_student) {
    $pdf->SetTitle('Your Seating Arrangement');
    $pdf->SetHeaderData('', 0, 'Your Assigned Seat', '');
    $pdf->setHeaderFont(['helvetica', '', 12]);
    $pdf->setFooterFont(['helvetica', '', 10]);

    $student_id = $_SESSION['student_id'];

    $result = $conn->query("
        SELECT sa.*, s.student_name, s.course_id, c.course_name 
        FROM seating_arrangements sa
        JOIN students s ON sa.student_id = s.student_id
        LEFT JOIN courses c ON s.course_id = c.course_id
        WHERE sa.student_id = '$student_id'
    ");

    $html = '<h2 style="text-align:center;">Your Assigned Seat</h2>';

    if ($result && $result->num_rows > 0) {
        $seat = $result->fetch_assoc();

        $html .= '<table border="1" cellpadding="6" cellspacing="0" width="100%" style="margin-top:20px;">';
        $html .= '<tr><th style="width:30%;">Student Name</th><td>' . htmlspecialchars($seat['student_name']) . '</td></tr>';
        $html .= '<tr><th>Course Name</th><td>' . htmlspecialchars($seat['course_name']) . '</td></tr>';
        $html .= '<tr><th>Student ID</th><td>' . htmlspecialchars($seat['student_id']) . '</td></tr>';
        $html .= '<tr><th>Seat Number</th><td>' . htmlspecialchars($seat['seat_number']) . '</td></tr>';
        $html .= '<tr><th>Row</th><td>' . htmlspecialchars($seat['row']) . '</td></tr>';
        $html .= '<tr><th>Column</th><td>' . htmlspecialchars($seat['columns']) . '</td></tr>';
        $html .= '</table>';
    } else {
        $html .= '<p style="color:red;">No seating arrangement assigned yet.</p>';
    }
}

// Output to PDF
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('seating_arrangement.pdf', 'I');