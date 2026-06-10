<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: user-login.php");
    exit();
}

include 'db_connect.php';

$student_id = $_SESSION['student_id'];

// Fetch educational details
$education_query = "SELECT * FROM education WHERE student_id = ?";
$stmt = $conn->prepare($education_query);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$education_result = $stmt->get_result();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle education records
    $education_ids = $_POST['education_id'];
    $qualifications = $_POST['qualification'];
    $institute_names = $_POST['institute_name'];
    $passing_years = $_POST['passing_year'];
    $grades = $_POST['grade'];
    $obtained_marks = $_POST['obtained_marks'];
    $total_marks = $_POST['total_marks'];

    foreach ($education_ids as $index => $education_id) {
        $qualification = $conn->real_escape_string($qualifications[$index]);
        $institute_name = $conn->real_escape_string($institute_names[$index]);
        $passing_year = $conn->real_escape_string($passing_years[$index]);
        $grade = $conn->real_escape_string($grades[$index]);
        $obtained_mark = $conn->real_escape_string($obtained_marks[$index]);
        $total_mark = $conn->real_escape_string($total_marks[$index]);

        $sql = "UPDATE education SET qualification='$qualification', institute_name='$institute_name', passing_year='$passing_year', grade='$grade', obtained_marks='$obtained_mark', total_marks='$total_mark' WHERE education_id='$education_id'";

        if (!$conn->query($sql)) {
            echo "Error updating record: " . $conn->error;
        } 
    }

    // Handle file uploads and updates
    $upload_dir = 'uploads/'; // Directory to store uploaded files

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Process Matric image if uploaded
    if (isset($_FILES['matric']) && $_FILES['matric']['error'] === UPLOAD_ERR_OK) {
        $matric_image_path = $upload_dir . basename($_FILES['matric']['name']);
        move_uploaded_file($_FILES['matric']['tmp_name'], $matric_image_path);

        $sql = "UPDATE student_marksheets SET marksheet_img=? WHERE student_id=? AND marksheet_type='Matric'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $matric_image_path, $student_id);
        if (!$stmt->execute()) {
            echo "Error updating matric image: " . $stmt->error;
        }
        $stmt->close();
    }

    // Process Intermediate image if uploaded
    if (isset($_FILES['intermediate']) && $_FILES['intermediate']['error'] === UPLOAD_ERR_OK) {
        $intermediate_image_path = $upload_dir . basename($_FILES['intermediate']['name']);
        move_uploaded_file($_FILES['intermediate']['tmp_name'], $intermediate_image_path);

        $sql = "UPDATE student_marksheets SET marksheet_img=? WHERE student_id=? AND marksheet_type='Intermediate'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $intermediate_image_path, $student_id);
        if (!$stmt->execute()) {
            echo "Error updating intermediate image: " . $stmt->error;
        } else {
        }
        $stmt->close();
    }
    
    echo "<script>
            alert('Form updated successfully!');
            window.location.href = 'view_your_form.php';
          </script>";
    exit();
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Education Information</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        form {
            margin: 0;
        }

        fieldset {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }

        legend {
            font-weight: bold;
            color: #007bff;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            color: #333;
        }

        input[type="text"],
        input[type="date"],
        select,
        input[type="file"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button[type="submit"] {
            padding: 10px 20px;
            margin: 20px 0;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }

        a.btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #6c757d;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
        }

        a.btn:hover {
            background-color: #5a6268;
        }

        .section {
            margin-bottom: 20px;
        }

        .section h3 {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            margin: 0;
            border-radius: 5px 5px 0 0;
        }

        .section table {
            width: 100%;
            border-collapse: collapse;
        }

        .section table th,
        .section table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .section table th {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Edit Education Information</h1>
        <form method="post" enctype="multipart/form-data">
            <?php while ($row = $education_result->fetch_assoc()): ?>
                <fieldset>
                    <legend>Record <?php echo htmlspecialchars($row['qualification']); ?></legend>
                    <input type="hidden" name="education_id[]" value="<?php echo htmlspecialchars($row['education_id']); ?>">
                    <label for="qualification">Qualification:</label>
                    <input type="text" name="qualification[]" value="<?php echo htmlspecialchars($row['qualification']); ?>"><br>
                    <label for="institute_name">Institute Name:</label>
                    <input type="text" name="institute_name[]" value="<?php echo htmlspecialchars($row['institute_name']); ?>"><br>
                    <label for="passing_year">Passing Year:</label>
                    <input type="text" name="passing_year[]" value="<?php echo htmlspecialchars($row['passing_year']); ?>"><br>
                    <label for="grade">Grade:</label>
                    <input type="text" name="grade[]" value="<?php echo htmlspecialchars($row['grade']); ?>"><br>
                    <label for="obtained_marks">Obtained Marks:</label>
                    <input type="text" name="obtained_marks[]" value="<?php echo htmlspecialchars($row['obtained_marks']); ?>"><br>
                    <label for="total_marks">Total Marks:</label>
                    <input type="text" name="total_marks[]" value="<?php echo htmlspecialchars($row['total_marks']); ?>"><br>
                </fieldset>
                <br>
            <?php endwhile; ?>

            <!-- File inputs for Matric and Intermediate, placed outside the while loop -->
            <fieldset>
                <legend>Upload Mark Sheet Images</legend>
                <label for="matric">Matric Mark Sheet Image:</label>
                <input type="file" name="matric" accept=".jpg,.jpeg,.png,.gif"><br>
                <label for="intermediate">Intermediate Mark Sheet Image:</label>
                <input type="file" name="intermediate" accept=".jpg,.jpeg,.png,.gif"><br>
            </fieldset>
            <br>

            <button type="submit">Update</button>
        </form>



        <a href="view_your_form.php" class="btn">Back to Application</a>
    </div>
</body>

</html>