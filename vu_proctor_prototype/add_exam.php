<?php
include 'navbar.php';
include 'db.php';

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exam_name = trim($_POST['exam_name']);
    $exam_date = $_POST['exam_date'];
    $center    = trim($_POST['center']);

    if (!empty($exam_name) && !empty($exam_date) && !empty($center)) {
        $sql = $conn->prepare("INSERT INTO exams (exam_name, exam_date, center) VALUES (?,?,?)");
        $sql->bind_param("sss", $exam_name, $exam_date, $center);
        if ($sql->execute()) {
            $msg = "Exam created successfully!";
        } else {
            $msg = " Error: " . $conn->error;
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<div class="container">
    <h2> Add New Exam</h2>
    <?php if (isset($msg)) echo "<p class='msg success'>$msg</p>"; ?>
    <?php if (isset($error)) echo "<p class='msg error'>$error</p>"; ?>
    <form method="post">
        <label>Exam Name:</label>
        <input type="text" name="exam_name" required placeholder="Enter exam name">

        <label>Exam Date:</label>
        <input type="date" name="exam_date" required>

        <label>Center:</label>
        <input type="text" name="center" required placeholder="Enter exam center">

        <button type="submit">Create Exam</button>
    </form>
</div>
</body>

</html>