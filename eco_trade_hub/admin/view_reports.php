<?php
include("../db_connection.php");

// Set default date range (last 7 days)
$end_date = date('Y-m-d');
$start_date = date('Y-m-d', strtotime('-7 days'));

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
}

// Fetch Sales Data from Database
$sql = "SELECT DATE(o.order_date) as date, SUM( oi.price) as total_sales 
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        WHERE o.order_date BETWEEN ? AND ?
        GROUP BY DATE(o.order_date)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$sales_data = [];
while ($row = $result->fetch_assoc()) {
    $sales_data[] = $row;
}

$stmt->close();
$conn->close();

// Handle CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=sales_report.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, array('Sale Report'));
    fputcsv($output, array('Date', 'Total Sales'));

    foreach ($sales_data as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}

// Handle PDF Export
if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
  // Include TCPDF
require_once('TCPDF/tcpdf.php');
class MYPDF extends TCPDF {
    // Page header

        public function Header() {
            // Set margins
            $this->SetMargins(10, 15, 10); // Left, Top, Right
    
            // Set font for the title
            $this->SetFont('helvetica', 'B', 14);
    
            // Set text color for the title
            $this->SetTextColor(252, 27, 27); // RGB color for the title text
    
            // Title, centered with adjusted margins
            $this->Cell(0, 10, 'ECO TRADE HUB', 0, 1, 'C');
    
            // Reset text color to default
            $this->SetTextColor(0, 0, 0); // RGB color for normal text (black)
    
            // Add a line below the header
            $this->Ln(5); // Line break
            $this->SetLineStyle(array('width' => 0.5, 'color' => array(0, 0, 0)));
            $this->Line(10, $this->GetY(), 200, $this->GetY());
            $this->Ln(5); // Another line break after the line
        }
    }
    

// Create new PDF document
$pdf = new MYPDF();

// Add a page
$pdf->AddPage();

// Set font for the content
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, "Sale Report", 0, 1, 'C');


// Build the table
$html = '<table border="1" style="border-collapse: collapse; padding: 10px;">'; // Start the table with cell padding

// Add the header row with padding
$html .= '<tr><th style="padding: 10px;">Date</th><th style="padding: 10px;">Sale</th></tr>';

$grand_total = 0; // Initialize grand total

// Loop through each row of sales data
foreach ($sales_data as $row) {
    $html .= "<tr><td style='padding: 10px;'>{$row['date']}</td><td style='padding: 10px;'>{$row['total_sales']}</td></tr>";
    $grand_total += $row['total_sales']; // Accumulate the total sales
}

// Add the grand total row with padding
$html .= "<tr><td style='padding: 10px;'>Total</td><td style='padding: 10px;'>{$grand_total}</td></tr>";

$html .= '</table>'; // End the table

// Write the HTML content to the PDF
$pdf->writeHTML($html);

// Output the PDF as a download
$pdf->Output('sales_report.pdf', 'D');
exit();

}
include("admin_header.php");
?>
<div class="container mb-1 mt-3 border p-0">
    <div class="text-center bg-dark text-white m-0 p-2 mb-3">
        <h3 class="m-0">Sales Trends</h3>
    </div>
    <div class="form-container mt-2 p-2">
        <!-- Filter Form -->
        <form method="GET" action="">
            <div class="row d-flex align-items-end">
                <div class="form-group col-md-3">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date); ?>" required>
                </div>

                <div class="form-group col-md-3">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date); ?>" required>
                </div>

                <div class="form-group col-md-3">
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </div>
            </div>
        </form>

        <!-- Sales Trends Chart -->
        <div class="mt-4 border" style="background-color: #fcfeff;">
            <canvas id="salesChart"></canvas>
        </div>

        <!-- Export Buttons -->
        <div class="mt-3">
            <a href="?start_date=<?php echo htmlspecialchars($start_date); ?>&end_date=<?php echo htmlspecialchars($end_date); ?>&export=csv" class="btn btn-sm btn-success">Export as CSV</a>
            <a href="?start_date=<?php echo htmlspecialchars($start_date); ?>&end_date=<?php echo htmlspecialchars($end_date); ?>&export=pdf" class="btn btn-sm btn-danger">Export as PDF</a>
        </div>
    </div>
</div>
<!-- Chart.js Script -->
<script>
    var ctx = document.getElementById('salesChart').getContext('2d');
    var salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($sales_data, 'date')); ?>,
            datasets: [{
                label: 'Sales Trends',
                data: <?php echo json_encode(array_column($sales_data, 'total_sales')); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Total Sales'
                    }
                }
            }
        }
    });
</script>
</body>

</html>