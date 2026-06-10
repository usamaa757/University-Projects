<?php
include 'header.php';
include '../other/db_connection.php';

// Check if the student_id is set
if (isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];

    // Fetch student details
    $student_sql = "SELECT s.student_name, c.class_name
                    FROM students s
                    JOIN classes c ON s.class_id = c.class_id
                    WHERE s.student_id = ?";
    
    $stmt = $conn->prepare($student_sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $student_result = $stmt->get_result();
    $student = $student_result->fetch_assoc();
    $stmt->close();

    // Fetch results for the student
    $results_sql = "SELECT courses.course_name, r.marks
                    FROM results r
                    JOIN courses ON r.course_id = courses.course_id
                    WHERE r.student_id = ?";
    
    $stmt = $conn->prepare($results_sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $results = $stmt->get_result();
    $stmt->close();
}
?>

    <title>Student Details and Generate PDF</title>
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        h2, h3 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 5px;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button.dmc {
            background-color: #4CAF50; /* Green */
        }
        .button.degree {
            background-color: #8BC34A; /* Light Green */
        }
        .button.dmc:hover {
            background-color: #45a049; /* Darker Green */
        }
        .button.degree:hover {
            background-color: #7CB342; /* Darker Light Green */
        }
    </style>

    <br><br><br>
<div class="container">
        <h3>Create DMC Or Degree</h3>

        <?php if (isset($student)): ?>
            <p><strong>Student Name:</strong> <?php echo htmlspecialchars($student['student_name']); ?></p>
            <p><strong>Class:</strong> <?php echo htmlspecialchars($student['class_name']); ?></p>

            <?php if ($results->num_rows > 0): ?>
                <h3>Results</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Marks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $results->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['marks']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No results found for this student.</p>
            <?php endif; ?>

            <!-- Generate PDF Buttons -->
            <a href="generate_dmc_pdf.php?student_id=<?php echo urlencode($student_id); ?>" class="button dmc">Generate DMC</a>
            <a href="generate_degree_pdf.php?student_id=<?php echo urlencode($student_id); ?>" class="button degree">Generate Degree</a>

        <?php else: ?>
            <p>No student details available. Please select a student.</p>
        <?php endif; ?>

        <!-- Form for selecting student -->
        <h3>Select Student</h3>
        <form method="POST" action="">
            <label for="student_id">Student:</label>
            <select id="student_id" name="student_id" required>
                <?php
                $students_sql = "SELECT DISTINCT s.student_id, s.student_name, c.class_name
                                 FROM students s
                                 JOIN results r ON s.student_id = r.student_id
                                 JOIN classes c ON s.class_id = c.class_id
                                 ORDER BY s.student_name";
                
                $students_result = $conn->query($students_sql);
                
                if ($students_result->num_rows > 0) {
                    while ($row = $students_result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['student_id']) . "'>" .
                             htmlspecialchars($row['student_name']) . ' - ' .
                             htmlspecialchars($row['class_name']) .
                             "</option>";
                    }
                } else {
                    echo "<option value=''>No students with results found</option>";
                }
                ?>
            </select>
            <input type="submit" value="View Details">
        </form>
    </div>
</body>
</html>