<?php
include '../db.php';
include 'header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'employer') {
    header("Location: ../login.php");
    exit;
}

$employer_id = $_SESSION['user_id'];
$job_id = $_GET['job_id'];

if (!isset($_GET['job_id'])) {
    header("Location: job_list.php");
    exit;
}


// Fetch existing job details
$query = "SELECT * FROM jobs WHERE job_id = $job_id AND employer_id = $employer_id";
$result = mysqli_query($conn, $query);



$row = mysqli_fetch_assoc($result);
$title = $row['title'];
$location = $row['location'];
$salary = $row['salary'];
$description = $row['description'];
$company = $row['company'];

// Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_title = $_POST['title'];
    $new_location = $_POST['location'];
    $company = $_POST['company'];
    $new_salary =  $_POST['salary'];
    $new_description = $_POST['description'];

    $update_query = "
        UPDATE jobs 
        SET title = '$new_title', company = '$company', location = '$new_location', salary = '$new_salary', description = '$new_description' 
        WHERE job_id = $job_id AND employer_id = $employer_id
    ";

    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Job updated successfully.'); window.location.href='job_list.php';</script>";
    } else {
        echo "<script>alert('Failed to update job.');</script>";
    }
}
?>

<div class="form-container">



    <form method="post" class="forms">
        <h2>Edit Job</h2>
        <label>Title:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>

        <label>Company:</label>
        <input type="text" name="company" value="<?php echo htmlspecialchars($company); ?>" required>

        <label>Location:</label>
        <input type="text" name="location" value="<?php echo htmlspecialchars($location); ?>" required>

        <label>Salary:</label>
        <input type="text" name="salary" value="<?php echo htmlspecialchars($salary); ?>" required>

        <label>Description:</label>
        <textarea name="description" required><?php echo htmlspecialchars($description); ?></textarea>
        <div class="text-center">

            <button type="submit" class="btn">Update Job</button>
        </div>
    </form>
</div>
</body>

</html>