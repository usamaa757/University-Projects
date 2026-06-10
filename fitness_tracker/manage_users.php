<?php
include("navbar.php");
include("db.php");

$full_name = "";
$email = "";
$address = "";
$role = "";
$user_id = "";
$button_text = "Add User";
$msg = "";
$error = "";

if(isset($_GET['msg'])){
    $msg = $_GET['msg'];
}

if(isset($_GET['error'])){
    $error = $_GET['error'];
}
// -------------------------
// Handle Delete
// -------------------------

if (isset($_GET['delete_id'])) {

    $del_id = (int) $_GET['delete_id']; // cast to int for security

    mysqli_begin_transaction($conn);

    try {

        // ===== Delete User Related Data =====
        mysqli_query($conn, "DELETE FROM daily_meals WHERE user_id = $del_id");
        mysqli_query($conn, "DELETE FROM daily_water WHERE user_id = $del_id");
        mysqli_query($conn, "DELETE FROM daily_workouts WHERE user_id = $del_id");
        mysqli_query($conn, "DELETE FROM feedback WHERE user_id = $del_id");
        mysqli_query($conn, "DELETE FROM diet_feedback WHERE user_id = $del_id");
        mysqli_query($conn, "DELETE FROM trainer_suggestions WHERE user_id = $del_id");

        // ===== If user is TRAINER, delete trainer-owned data =====
        mysqli_query($conn, "DELETE FROM workout_routines WHERE trainer_id = $del_id");
        mysqli_query($conn, "DELETE FROM diet_plans WHERE trainer_id = $del_id");

        // ===== Finally delete user =====
        mysqli_query($conn, "DELETE FROM users WHERE id = $del_id");

        mysqli_commit($conn);

        header("Location: manage_users.php?msg=" . urlencode("User deleted successfully."));
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        header("Location: manage_users.php?msg=" . urlencode("Error deleting user."));
        exit();
    }
}


// -------------------------
// Check if Editing
// -------------------------
if(isset($_GET['edit_id'])){
    $user_id = $_GET['edit_id'];
    $edit_user_query = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
    if(mysqli_num_rows($edit_user_query) > 0){
        $edit_user = mysqli_fetch_assoc($edit_user_query);
        $full_name = $edit_user['full_name'];
        $email = $edit_user['email'];
        $address = $edit_user['address'];
        $role = $edit_user['role'];
        $button_text = "Update User";
    } else {
        $error = "User not found.";
    }
}

// -------------------------
// Handle Add / Update
// -------------------------
if(isset($_POST['submit_user'])){
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $role = $_POST['role'];
    $user_id = $_POST['user_id'];

    // Check if email already exists (excluding current user when updating)
    $email_check_sql = "SELECT * FROM users WHERE email='$email'";
    if($user_id != ""){
        $email_check_sql .= " AND id != '$user_id'";
    }
    $email_check = mysqli_query($conn, $email_check_sql);

    if(mysqli_num_rows($email_check) > 0){
        $error = "Email already exists.";
    } else {
        // Update User
        if($user_id != ""){
            if(!empty($_POST['password'])){
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $sql = "UPDATE users SET full_name='$full_name', email='$email', address='$address', role='$role', password='$password' WHERE id='$user_id'";
            } else {
                $sql = "UPDATE users SET full_name='$full_name', email='$email', address='$address', role='$role' WHERE id='$user_id'";
            }

            if(mysqli_query($conn, $sql)){
                $msg = "User updated successfully.";
                header("Location: manage_users.php?msg=".urlencode($msg));
                exit();
            } else {
                $error = "Error updating user.";
            }

        // Add New User
        } else {
            if(empty($_POST['password'])){
                $error = "Password is required.";
            } else {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (full_name, email, address, role, password) VALUES ('$full_name','$email','$address','$role','$password')";
                if(mysqli_query($conn, $sql)){
                    $msg = "User added successfully.";
                    $full_name = $email = $address = $role = "";
                } else {
                    $error = "Error adding user.";
                }
            }
        }
    }
}

// -------------------------
// Fetch All Users
// -------------------------
$users = mysqli_query($conn, "SELECT * FROM users WHERE role != 'admin' ORDER BY id DESC");
?>

<!-- ------------------------- -->
<!-- User Add/Edit Form -->
<!-- ------------------------- -->
<div class="form-container">
    <h2><?php echo $button_text; ?></h2>

    <?php if(isset($error) && $error != "") { ?>
        <p class="error"><?php echo $error; ?></p>
    <?php } ?>

    <?php if(isset($msg) && $msg != "") { ?>
        <p class="message"><?php echo $msg; ?></p>
    <?php } ?>

    <form method="POST">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

        <input type="text" name="full_name" placeholder="Full Name" value="<?php echo $full_name; ?>" required>
        <input type="email" name="email" placeholder="Email Address" value="<?php echo $email; ?>" required>
        <input type="text" name="address" placeholder="Address" value="<?php echo $address; ?>" required>

        <select name="role" required>
            <option value="">Select Role</option>
            <option value="user" <?php if($role=="user") echo 'selected'; ?>>User</option>
            <option value="trainer" <?php if($role=="trainer") echo 'selected'; ?>>Trainer</option>
        </select>

        <input type="password" name="password" placeholder="<?php echo ($user_id != "") ? 'New Password (leave blank to keep current)' : 'Password'; ?>" <?php echo ($user_id == "") ? 'required' : ''; ?>>

        <div class="text-center">
            <button type="submit" name="submit_user"><?php echo $button_text; ?></button>
        </div>
    </form>
</div>

<!-- ------------------------- -->
<!-- Users Table -->
<!-- ------------------------- -->
<div class="table-container">
    <h3>Existing Users</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Address</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($users)) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['full_name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['address']; ?></td>
                <td><?php echo ucfirst($row['role']); ?></td>
                <td>
                    <a href="manage_users.php?edit_id=<?php echo $row['id']; ?>" class="edit">Edit</a>
                    <a href="manage_users.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')" class="delete">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
