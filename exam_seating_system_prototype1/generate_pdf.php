<?php
require_once('tcpdf/tcpdf.php'); // Include TCPDF
include 'db.php';
session_start();

// Check if student is logged in
if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];

    // Fetch student details
    $student_query = mysqli_query($conn, "SELECT student_name FROM students WHERE student_id = '$student_id'");
    $student = mysqli_fetch_assoc($student_query);
    $student_name = $student['student_name'];

    // Fetch exam schedule for logged-in student
    $query = "SELECT es.exam_date, es.exam_time, c.course_name 
              FROM student_exams sea
              JOIN exam_schedule es ON sea.exam_id = es.exam_id
              JOIN courses c ON es.course_id = c.course_id
              WHERE sea.student_id = '$student_id' 
              ORDER BY es.exam_date, es.exam_time";
    $is_single_student = true; // Flag to indicate a single student
} else {
    // If admin is viewing all students' schedules
    $query = "SELECT sea.student_id, s.student_name, es.exam_date, es.exam_time, c.course_name 
              FROM student_exams sea
              JOIN students s ON sea.student_id = s.student_id
              JOIN exam_schedule es ON sea.exam_id = es.exam_id
              JOIN courses c ON es.course_id = c.course_id
              ORDER BY sea.student_id, es.exam_date, es.exam_time";
    $is_single_student = false; // Flag to indicate multiple students
}

$result = mysqli_query($conn, $query);

// Create PDF object
$pdf = new TCPDF();
$pdf->SetTitle("Exam Schedule");

// Set page orientation to **Landscape** for wider tables
if (!$is_single_student) {
    $pdf->SetPageOrientation('L'); // Landscape mode for admin
}

$pdf->AddPage();

// Title
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, "Exam Schedule", 0, 1, 'C');

$pdf->SetFont('helvetica', '', 12);

// If student is logged in, show their details
if ($is_single_student) {
    $pdf->Cell(0, 10, "Roll #: $student_id", 0, 1, 'L');
    $pdf->Cell(0, 10, "Student Name: $student_name", 0, 1, 'L');

    // Table Header (for single student)
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(50, 10, 'Exam Date', 1, 0, 'C');
    $pdf->Cell(50, 10, 'Exam Time', 1, 0, 'C');
    $pdf->Cell(80, 10, 'Subject', 1, 1, 'C');
} else {
    // Table Header (for multiple students - Admin view)
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(25, 10, 'S No', 1, 0, 'C');
    $pdf->Cell(25, 10, 'Student ID', 1, 0, 'C');
    $pdf->Cell(50, 10, 'Student Name', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Exam Date', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Exam Time', 1, 0, 'C');
    $pdf->Cell(90, 10, 'Subject', 1, 1, 'C'); // Adjusted width
}

$pdf->SetFont('helvetica', '', 12);
$sno = 1;
while ($row = mysqli_fetch_assoc($result)) {

    $exam_date = date("d M, Y", strtotime($row['exam_date']));
    $exam_time = date("h:i A", strtotime($row['exam_time']));
    $subject = $row['course_name'];

    if ($is_single_student) {
        // Show only exam details for a single student
        $pdf->Cell(50, 10, $exam_date, 1, 0, 'C');
        $pdf->Cell(50, 10, $exam_time, 1, 0, 'C');
        $pdf->Cell(80, 10, $subject, 1, 1, 'C');
    } else {
        // Show student ID and name for multiple students
        $pdf->Cell(25, 10, $sno++, 1, 0, 'C');
        $pdf->Cell(25, 10, $row['student_id'], 1, 0, 'C');
        $pdf->Cell(50, 10, $row['student_name'], 1, 0, 'C');
        $pdf->Cell(40, 10, $exam_date, 1, 0, 'C');
        $pdf->Cell(40, 10, $exam_time, 1, 0, 'C');
        $pdf->Cell(90, 10, $subject, 1, 1, 'C'); // Adjusted width
    }
}

// Output PDF
$pdf->Output("Exam_Schedule.pdf", 'D'); // Forces download