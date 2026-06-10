<?php
session_start();

include 'db.php';
$success = "";
$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];
  $selectedRole = $_POST['role'];

  $stmt = $conn->prepare("SELECT id, fullname, password, role, photo FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if ($user['role'] !== $selectedRole) {
      $error = "Selected role doesn't match with user account.";
    } elseif (password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['fullname'] = $user['fullname'];
      $_SESSION['role'] = $user['role'];
      $_SESSION['email'] = $user['email'];
      $_SESSION['photo'] = $user['photo'];

      // Redirect based on role
      switch ($user['role']) {
        case 'admin':
          header("Location: admin/dashboard.php");
          break;
        case 'agent':
          header("Location: agent/dashboard.php");
          break;
        case 'user':
          header("Location: user/dashboard.php");
          break;
        default:
          $error = "Invalid role.";
      }
      exit();
    } else {
      $error = "Invalid password.";
    }
  } else {
    $error = "No user found with this email.";
  }
}
$conn->close();
include 'header.php';
?>

<style>

</style>

<div class="container">
    <div class="section">
        <div class="section-header">
            <i class="fas fa-sign-in-alt"></i>
            <h2>Login</h2>
        </div>
        <?php if ($success): ?>
        <div class="alert success"><?= $success ?></div>
        <?php elseif ($error): ?>
        <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <select name="role" id="role">
                <option value="" selected disabled>--- Select ---</option>
                <option value="admin">Admin</option>
                <option value="agent">Agent</option>
                <option value="user">User</option>
            </select>
            <div class="text-center">

                <button type="submit" class="btn">Login</button>
            </div>
        </form>

        <p style="margin-top: 20px;">Don't have an account?
            <a href="register.php" style="color: var(--dark);">Register here</a>.
        </p>
    </div>
</div>
<?php include 'footer.php'; ?>

</body>

</html>