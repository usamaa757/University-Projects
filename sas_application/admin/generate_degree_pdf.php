<?php
ob_start(); // Start output buffering
require_once '../TCPDF/tcpdf.php';

include '../other/db_connection.php';

// Check if student_id is set and is a valid integer
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];
    // Prepare SQL statement to fetch student name and pass_year
    $sql = "SELECT students.student_name, DATE_FORMAT(results.pass_year, '%d %b, %Y') AS pass_year_formatted
            FROM students
            INNER JOIN results ON students.student_id = results.student_id
            WHERE students.student_id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch data of each row
            $row = $result->fetch_assoc();
            $student_name = $row["student_name"];
            $pass_year = $row["pass_year_formatted"];
        } else {
            echo "Student is not eligible";
            exit;
        }
        
        $stmt->close();
    } else {
        echo "Error preparing SQL statement.";
        exit;
    }

    // Close database connection
    $conn->close();
    ob_end_clean();

    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('Degree Certificate');
    $pdf->SetSubject('Degree Certificate');
    $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

    // Set default header data
    $pdf->SetHeaderData('PDF_HEADER_LOGO', PDF_HEADER_LOGO_WIDTH, 'Degree Certificate', 'University of Example');

    // Set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // Set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // Set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetPrintFooter(false);

    // Set auto page breaks
    $pdf->SetAutoPageBreak(false, 0);

    // Set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // Set font
    $pdf->SetFont('times', '', 12);

    // Add a page
    $pdf->AddPage();

    // Set some content
    $html = <<<EOD
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Degree Certificate</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            border: 2px solid #000;
            padding: 50px;
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            position: relative;
        }
        .header, .footer {
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 32px;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .content {
            margin: 50px 0;
            text-align: center;
            font-size: 18px;
            line-height: 1.6;
        }
        .degree-title {
            font-size: 24px;
            font-weight: bold;
            margin-top: 10px;
            text-transform: uppercase;
        }
        .seal {
            position: absolute;
            bottom: 20px;
            left: 20px;
        }
        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature div {
            text-align: center;
            font-size: 18px;
        }
        .signature div p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>University of Example</h1>
            <h2>Degree Certificate</h2>
        </div>
        <div class="content">
            <p>This is to certify that</p>
            <p class="degree-title">$student_name</p>
            <p>has successfully completed the requirements for the degree of</p>
            <p class="degree-title">Bachelor of Science in Computer Science</p>
            <p>at the University of Example</p>
            <p>on $pass_year</p>
        </div>
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left;">
                    __________________________<br>
                    Registrar<br>
                    University of Example
                </td>
                <td style="text-align: right;">
                    __________________________<br>
                    Dean<br>
                    University of Example
                </td>
            </tr>
        </table>
        <div style="text-align: center;">
            <img class="seal" src="seal.png" alt="University Seal" width="100">
        </div>
    </div>
</body>
</html>
EOD;

    // Output the HTML content
    $pdf->writeHTML($html, true, false, true, false, '');

    // Close and output PDF document
    $pdf->Output('degree_certificate.pdf', 'I');

} else {
    echo "Invalid student ID.";
    exit;
}
?>
