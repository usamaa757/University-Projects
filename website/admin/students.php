<?php
include 'header.php'; // Include the header
include '../db_connection.php'; // Include the database connection
$message = '';

// Check for messages in the URL
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}

// Fetch students from the database
$query = "SELECT * FROM students"; // Adjust the query according to your table structure
$result = mysqli_query($conn, $query);
?>
<br><br><br><br><br>
<div class="container border shadow p-0">
    <div class="">
        <h3 class="bg-primary text-white p-3">Student Management</h3>
    </div>
    <div class="p-3">
        <a href="add_student.php" class="btn btn-primary">Add Student</a>

        <div class="m-2 text-success">
            <?php if (isset($_GET['message'])) {
                echo '<div class="text-success fade show" role="alert">' . htmlspecialchars($_GET['message']) . '</div>';
            } elseif (isset($_GET['error'])) {
                echo '<div class="text-danger alert-dismissible fade show" role="alert">' . htmlspecialchars($_GET['error']) . '</div>';
            }
            ?>
        </div>

        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($student = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $student['student_id']; ?></td>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td>
                                <a href="#"
                                    onclick="showEditForm(<?php echo $student['student_id']; ?>, '<?php echo addslashes($student['name']); ?>')"
                                    class="btn btn-block btn-primary">Edit</a>
                                |
                                <a href="delete_student.php?id=<?php echo $student['student_id']; ?>"
                                    class="btn btn-block btn-danger" onclick="return confirmDelete()">Delete</a>
                            </td>
                        </tr>
                        <!-- Edit Form Row -->
                        <tr id="edit-form-<?php echo $student['student_id']; ?>" style="display: none;">
                            <td colspan="3">
                                <form action="update_student.php" method="POST">
                                    <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                                    <div class="form-group">
                                        <label for="name">Edit Student Name:</label>
                                        <input type="text" name="name" id="name_<?php echo $student['student_id']; ?>"
                                            class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>"
                                            required>
                                    </div>
                                    <button type="submit" class="btn btn-success mt-2">Update</button>
                                    <button type="button" class="btn btn-secondary mt-2"
                                        onclick="hideEditForm(<?php echo $student['student_id']; ?>)">Cancel</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Show the edit form below the selected student row
    function showEditForm(studentId, studentName) {
        // Hide any other open edit forms
        document.querySelectorAll('[id^="edit-form-"]').forEach(el => el.style.display = 'none');

        // Show the selected edit form and fill the student name field
        document.getElementById(`edit-form-${studentId}`).style.display = 'table-row';
        document.getElementById(`name_${studentId}`).value = studentName;
    }

    // Hide the edit form
    function hideEditForm(studentId) {
        document.getElementById(`edit-form-${studentId}`).style.display = 'none';
    }

    // Confirm deletion
    function confirmDelete() {
        return confirm("Are you sure you want to delete this student?");
    }
</script>