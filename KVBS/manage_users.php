<?php
include("header.php");
include("db_connect.php");

$message = "";
$error = "";
// --- Delete Child ---
if (isset($_GET['delete'])) {
    $parent_id = $_GET['delete'];

    // Step 1: Delete all bookings of the user (including their children)
    $delete_bookings = "DELETE FROM bookings WHERE parent_id='$parent_id'";

    // Step 2: Delete all children of the parent
    $delete_children = "DELETE FROM children WHERE parent_id='$parent_id'";

    // Step 3: Delete the user account
    $delete_user = "DELETE FROM users WHERE id='$parent_id' AND role='parent'";

    if (
        mysqli_query($conn, $delete_bookings) &&
        mysqli_query($conn, $delete_children) &&
        mysqli_query($conn, $delete_user)
    ) {
        $message = "User deleted successfully!";
    } else {
        $error = "Error deleting user data: " . mysqli_error($conn);
    }
}



// --- Handle Add / Update ---


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    if (isset($_POST['id']) && $_POST['id'] != "") {
        // Update existing record
        $id = $_POST['id'];
        // If user entered new password → hash it
        if (!empty($password)) {
            $update_sql = "UPDATE users SET 
                        full_name='$full_name',
                        email='$email',
                        phone='$phone',
                        address='$address',
                        password='$hashed_password'
                        WHERE id='$user_id'";
        } else {
            $query = "UPDATE users SET 
                    full_name='$full_name', email='$email', phone='$phone', address='$address'
                  WHERE id=$id";
            if (mysqli_query($conn, $query)) {
                $message = "Worker updated successfully!";
            } else {
                $messagerrore = "Error updating record: " . mysqli_error($conn);
            }
        }
    } else {
        // Add new record
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Email already registered!";
        } else {
            $query = "INSERT INTO users (full_name, email, phone, password, address, role)
                  VALUES ('$full_name', '$email', '$phone', '$hashed_password', '$address', 'worker')";
            if (mysqli_query($conn, $query)) {
                $message = "Worker added successfully!";
            } else {
                $error = "Error adding worker: " . mysqli_error($conn);
            }
        }
    }
}

// --- Fetch All users ---
$result = mysqli_query($conn, "SELECT * FROM users WHERE role !='admin' ORDER BY id DESC");

// --- Edit Mode ---
$edit_user = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
    $edit_user = mysqli_fetch_assoc($res);
}
?>


<div class="management-container">
    <h1>Manage Users</h1>

    <a class="back" href="dashboard.php">← Back to Dashboard</a>

    <h2><?php echo $edit_user ? "Edit User" : "Add New Worker"; ?></h2>
    <?php

    if (!empty($error)) {
        echo "<div class='error'>$error</div>";
    } else {
        echo "<div class='message'>$message</div>";
    }
    ?>


    <form method="POST" class="form">
        <input type="hidden" name="id" value="<?php echo $edit_user['id'] ?? ''; ?>">

        <input type="text" name="full_name" placeholder="Full Name" value="<?php echo $edit_user['full_name'] ?? ''; ?>"
            required>

        <input type="email" name="email" placeholder="Email" value="<?php echo $edit_user['email'] ?? ''; ?>" required>

        <input type="text" name="phone" placeholder="Phone" value="<?php echo $edit_user['phone'] ?? ''; ?>">

        <input type="password" name="password" placeholder="password">

        <textarea name="address" placeholder="Address" rows="2"><?php echo $edit_user['address'] ?? ''; ?></textarea>

        <div style="grid-column: span 2; text-align:center;">
            <button type="submit"><?php echo $edit_user ? "Update user" : "Add user"; ?></button>
        </div>
    </form>

    <h2>All Health users</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['full_name']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['role']; ?></td>
            <td><?php echo $row['phone']; ?></td>
            <td><?php echo $row['address']; ?></td>
            <td>
                <a class="action edit" href="manage_users.php?edit=<?php echo $row['id']; ?>">Edit</a>
                <a class="action delete" href="manage_users.php?delete=<?php echo $row['id']; ?>"
                    onclick="return confirm('Are you sure you want to delete this worker?');">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>
<?php

include('footer.php');

?>
</body>

</html>