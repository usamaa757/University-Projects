<?php
include '../db.php';
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $company = $_POST['company'];
    $location = $_POST['location'];
    $salary = $_POST['salary'];
    $description = $_POST['description'];
    $employer_id = $_SESSION['user_id'];

    $query = "INSERT INTO jobs (employer_id, title, company, location, salary, description) 
              VALUES ($employer_id, '$title', '$company', '$location', '$salary', '$description')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Job posted successfully!'); window.location.href='job_list.php';</script>";
    } else {
        echo "<script>alert('Failed to post job.');</script>";
    }
}
?>


<div class="form-container">

    <form method="post" class="forms">
        <h2>Post a New Job</h2>
        <label>Title:</label><br>
        <input type="text" name="title" required>

        <label>Company:</label><br>
        <input type="text" name="company" required>

        <label>Location:</label><br>
        <input type="text" name="location" required>

        <label>Salary:</label><br>
        <input type="text" name="salary" required>

        <label>Description:</label><br>
        <textarea name="description" required></textarea>
        <div class="text-center">

            <button type="submit" class="btn">Post Job</button>
        </div>
    </form>
</div>

</body>

</html>