<?php
include 'db.php';
include 'header.php';


if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
if (isset($_POST['add_student'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    // Check if email already exists
    $check = $conn->prepare("SELECT student_id FROM students WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<div class='alert alert-danger mt-3'>Student with this email already exists!</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO students (student_name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);
        $stmt->execute();

        echo "<div class='alert alert-success mt-3'>Student added successfully!</div>";
    }
}

// Handle CSV Upload
if (isset($_POST['upload_csv'])) {
    if ($_FILES['csv_file']['tmp_name']) {
        $handle = fopen($_FILES['csv_file']['tmp_name'], 'r');
        $inserted = 0;
        $skipped = 0;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $name = trim($data[0]);
            $email = trim($data[1]);
            $password_raw = trim($data[2]);

            if (!$name || !$email || !$password_raw) {
                $skipped++;
                continue;
            }

            $password = password_hash($password_raw, PASSWORD_DEFAULT);

            // Check if email already exists
            $check = $conn->prepare("SELECT student_id FROM students WHERE email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $skipped++;
                continue;
            }

            $stmt = $conn->prepare("INSERT INTO students (student_name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $password);
            $stmt->execute();
            $inserted++;
        }

        echo "<div class='alert alert-success mt-3'>
                CSV uploaded. <strong>$inserted</strong> added, <strong>$skipped</strong> skipped (existing or invalid).
              </div>";
    }
}


// Fetch students and courses
$students = $conn->query("SELECT* FROM students");

?>

<div class="container mt-4">
    <h2>Manage Students</h2>

    <form method="post" class="row g-3 mt-3">
        <div class="col-md-3">
            <input type="text" name="name" class="form-control" placeholder="Full Name" required>
        </div>
        <div class="col-md-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="col-md-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="col-md-2">
            <button type="submit" name="add_student" class="btn btn-primary w-100">Add</button>
        </div>
    </form>


    <form method="post" enctype="multipart/form-data" class="mt-4">
        <label>Upload CSV (Format: student_id, name, email, course_code)</label>
        <div class="input-group">
            <input type="file" name="csv_file" class="form-control" required>
            <button type="submit" name="upload_csv" class="btn btn-success">Upload</button>
        </div>
    </form>

    <table class="table table-striped mt-4">
        <thead class="table-dark">
            <tr>

                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($s = $students->fetch_assoc()) { ?>
            <tr>

                <td><?= htmlspecialchars($s['student_id']) ?></td>
                <td><?= htmlspecialchars($s['student_name']) ?></td>
                <td><?= htmlspecialchars($s['email']) ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>