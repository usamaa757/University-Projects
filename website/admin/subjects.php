<?php
include 'header.php';
include '../db_connection.php';
$message = '';
if (isset($_GET['message'])) {

    $message = $_GET['message'];
}
// Fetch subjects from the database
$query = "SELECT * FROM subjects";
$result = mysqli_query($conn, $query);
?>
<br><br><br><br><br>
<div class="container border shadow p-0">
    <div class="">
        <h3 class="bg-primary text-white p-3">Subject Management</h3>
    </div>
    <div class="p-3">
        <a href="add_subject.php" class="btn btn-primary">Add Subject</a>

        <div class="m-2 text-success">
            <?php if (isset($_GET['message'])) {
                echo '<div class="text-success fade show" role="alert">
            ' . htmlspecialchars($_GET['message']) . '
          </div>';
            } elseif (isset($_GET['error'])) {
                echo '<div class="text-danger alert-dismissible fade show" role="alert">
            ' . htmlspecialchars($_GET['error']) . '
           
          </div>';
            }
            ?>

        </div>
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">

            <table class="table">
                <thead>
                    <tr>
                        <th>Subject ID</th>
                        <th>Subject Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($subject = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $subject['subject_id']; ?></td>
                            <td><?php echo $subject['subject_name']; ?></td>
                            <td>
                                <a href="#"
                                    onclick="showEditForm(<?php echo $subject['subject_id']; ?>, '<?php echo $subject['subject_name']; ?>')"
                                    class="btn btn-block btn-primary">Edit</a>
                                |
                                <a href="delete_subject.php?id=<?php echo $subject['subject_id']; ?>"
                                    class="btn btn-block btn-danger" onclick="return confirmDelete()">Delete</a>
                            </td>
                        </tr>
                        <!-- Edit Form Row -->
                        <tr id="edit-form-<?php echo $subject['subject_id']; ?>" style="display: none;">
                            <td colspan="3">
                                <form action="update_subject.php" method="POST">
                                    <input type="hidden" name="subject_id" value="<?php echo $subject['subject_id']; ?>">
                                    <div class="form-group">
                                        <label for="subject_name">Edit Subject Name:</label>
                                        <input type="text" name="subject_name"
                                            id="subject_name_<?php echo $subject['subject_id']; ?>" class="form-control"
                                            value="<?php echo $subject['subject_name']; ?>" required>
                                    </div>
                                    <button type="submit" class="btn btn-success mt-2">Update</button>
                                    <button type="button" class="btn btn-secondary mt-2"
                                        onclick="hideEditForm(<?php echo $subject['subject_id']; ?>)">Cancel</button>
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
    // Show the edit form below the selected subject row
    function showEditForm(subjectId, subjectName) {
        // Hide any other open edit forms
        document.querySelectorAll('[id^="edit-form-"]').forEach(el => el.style.display = 'none');

        // Show the selected edit form and fill the subject name field
        document.getElementById(`edit-form-${subjectId}`).style.display = 'table-row';
        document.getElementById(`subject_name_${subjectId}`).value = subjectName;
    }

    // Hide the edit form
    function hideEditForm(subjectId) {
        document.getElementById(`edit-form-${subjectId}`).style.display = 'none';
    }

    // Confirm deletion
    function confirmDelete() {
        return confirm("Are you sure you want to delete this subject?");
    }
</script>