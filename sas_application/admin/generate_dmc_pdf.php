<?php
ob_start(); // Start output buffering

include '../other/db_connection.php'; // Include your database connection script

require_once '../TCPDF/tcpdf.php'; // Adjust the path for TCPDF library

// Initialize variables
$student = [];
$results = [];
$total_marks = 0;
$total_obtained_marks = 0;
$percentage = 0;

$pass_fail_status = '';

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    // Fetch student details
    $studentQuery = "SELECT s.student_name, c.class_name
                     FROM students s
                     JOIN classes c ON s.class_id = c.class_id
                     WHERE s.student_id = ?";

    $stmt_student = $conn->prepare($studentQuery);
    $stmt_student->bind_param("i", $student_id);
    $stmt_student->execute();
    $studentResult = $stmt_student->get_result();
    $student = $studentResult->fetch_assoc();
    $stmt_student->close();

    // Fetch results details
    $resultsQuery = "SELECT r.marks, r.total_marks, c.course_name
                     FROM results r
                     JOIN courses c ON r.course_id = c.course_id
                     WHERE r.student_id = ?";

    $stmt_results = $conn->prepare($resultsQuery);
    $stmt_results->bind_param("i", $student_id);
    $stmt_results->execute();
    $resultsResult = $stmt_results->get_result();

    // Calculate total marks
    if ($resultsResult->num_rows > 0) {
        while ($row = $resultsResult->fetch_assoc()) {
            $total_obtained_marks += $row['marks']; // Update to correctly accumulate obtained marks
            $total_marks += $row['total_marks']; // Update to correctly accumulate obtained marks
            $results[] = [
                'course_name' => $row['course_name'],
                'marks' => $row['marks'],
                'total_marks' => $row['total_marks']
            ];
        }

        // Assuming total_marks is the sum of obtained marks, so percentage calculation is based on obtained marks
       // Set total_marks to obtained_marks
        if ($total_marks > 0) {
            $percentage = ($total_obtained_marks / $total_marks) * 100;
        }

        // Determine pass/fail status
        $pass_fail_status = ($percentage < 50) ? 'Fail' : 'Pass';
    } else {
        echo "No results found for this student.";
        exit();
    }
    $stmt_results->close();
}
ob_end_clean();
// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Detailed Marks Certificate');
$pdf->SetSubject('Detailed Marks Certificate');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// Set default header data
$pdf->SetHeaderData('', 0, '', '');

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
$pdf->SetFont('times', '', 12);

// HTML content
$html = '
<body style="font-family: \'Vollkorn Regular\', serif; margin: 0; padding: 0; background-color: #f4f4f4;">
    <div class="certificate" id="certificate" style="width: 650px; margin: 0 auto; background-color: #fff; margin-top: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(4, 4, 2, 0.4);">
        <div style="border: 1px solid rgb(153, 153, 153); padding: 20px;">
            <h2 style="text-align: center;">School Automation System</h2>
            <h3 style="text-align: center;">Detailed Marks Certificate</h3>
            <div class="details">
                <p><strong>Student Name:</strong> ' . htmlspecialchars($student['student_name']) . '</p>
                <p><strong>Student ID:</strong> ST' . htmlspecialchars($student_id) . '</p>
                <p><strong>Class:</strong> ' . htmlspecialchars($student['class_name']) . '</p>
            </div>
            <div class="subject-details">
                <h3>Course Details:</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding: 15px; border: 1px solid #ddd;">Course</th>
                            <th style="padding: 15px; border: 1px solid #ddd;">Marks</th>
                        </tr>
                    </thead>
                    <tbody>';

if (!empty($results)) {
    foreach ($results as $result) {
        $html .= '<tr>
            <td style="padding: 15px; border: 1px solid #ddd;">' . htmlspecialchars($result['course_name']) . '</td>
            <td style="padding: 15px; border: 1px solid #ddd;">' . htmlspecialchars($result['marks']) . '</td>
        </tr>';
    }
} else {
    $html .= '<tr>
        <td colspan="2" style="padding: 15px; border: 1px solid #ddd;">No course marks found</td>
    </tr>';
}

$html .= '
                    </tbody>
                </table>
            </div>
            <p><strong>Total Marks:</strong> ' . htmlspecialchars($total_marks) . '</p>
            <p><strong>Total Obtained Marks:</strong> ' . htmlspecialchars($total_obtained_marks) . '</p>
            <p><strong>Percentage:</strong> ' . number_format($percentage, 2) . '%</p>
            <p><strong>Status:</strong> ' . htmlspecialchars($pass_fail_status) . '</p>
        </div>
    </div>
</body>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');
$file_name = $student['student_name'] . '_ST' . $student_id;

// Close and output PDF document
$pdf->Output('Detailed_Marks_Certificate_' . $file_name . '.pdf', 'I');


?>
