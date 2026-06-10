<?php
session_start();
// Establish connection
include "../db_connection.php";

if (!isset($_SESSION['email'])) {

    header("Location: ../login/admin_login.php");
    exit();
   
}
// Check ifadmin_id is provided in the URL
if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];

    // Prepare and execute SQL statement to fetchadmin record
    $stmt = $conn->prepare("SELECT * FROM admin WHERE admin_id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc(); // Fetch admin data

    // Check ifadmin exists
    if (!$admin) {
        // Redirect with error message ifadmin not found
        header("Location:admin_profile.php?error=Admin not found");
        exit();
    }
} else {
    // Redirect ifadmin_id is not provided
    header("Location:admin_profile.php?error=Admin ID not provided");
    exit();
}
if (isset($_GET['success']) && $_GET['success'] == 'true') {
    $resultMsg = "Admin updated...!";
}

// Check for error message
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'password_mismatch') {
        $errorMsg = "Passwords don't match!";
    } elseif ($_GET['error'] == 'update_failed') {
        $errorMsg = "Failed to update admin details. Please try again later.";
    }
}


// Close statement and connection
$stmt->close();
$conn->close();

$admin_name = $_SESSION['name'];
$admin_email = $_SESSION['email'];
$admin_picture = $_SESSION['profile_pic'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/form.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/fontawesome/web-fonts-with-css/css/fontawesome-all.css">
    <link rel="stylesheet" href="../assets/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/fontawesome/web-fonts-with-css/css/fontawesome.min.css">
</head>

<body>
    <input type="checkbox" name="" id="sidebar-toggle">
    <div class="sidebar">

        <div class="sidebar-main">
            <div class="sidebar-user">
            <img src="<?php echo htmlspecialchars($admin_picture); ?>" alt="Profile Picture">

                <div>
                    <h3><?php echo htmlspecialchars($admin_name); ?></h3>
                    <span><?php echo htmlspecialchars($admin_email); ?></span>
                </div>
            </div>
            <div class="sidebar-menu">
                <div class="menu-head">
                    <a href="admin_dashboard.php">
                        <h2>Dashboard</h2>
                    </a>
                </div>
                <ul>
                    <li> <a href="admin_profile.php">Profile</a> </li>
                    <li> <a href="course/course_management.php">Course Management</a> </li>
                    <li> <a href="assignment/assignment_record.php"> Assignment Management</a> </li>
                    <li> <a href="quiz/quiz_list.php"> Quiz Management </a> </li>
                    <li> <a href="lesson/lesson_record.php"> Lesson Management </a> </li>
                    <li> <a href="admin_registration.php"> Admin Registration </a> </li>
                   
                </ul>
            </div>
        </div>

    </div>

    <div class="main-content">

        <header>
            <div class="menu-toggle">
                <label for="sidebar-toggle">
                    <span class="las la-bars"></span>
                </label>
            </div>

            <div class="header-icons">
           
                <a href="../login/logout.php"> <button>
                        <!-- <span class="las la-file-export"></span> -->
                        <span class="fa fa-power-off"></span>

                    </button></a>
            </div>
        </header>

        <main>
            <!-- <div class="page-header">
                <div>
                    <h1>Admin Dashboard</h1>
                </div>
            </div> -->

            <div>
                <div class="form-container">
                    <div class="headding">
                        <h3>Details</h3>

                    </div>
                    <div class="form">

                        <div class="result-output">
                            <?php
                            if (!empty($errorMsg)) {
                                echo "<span id = 'error'>$errorMsg</span>";
                            } elseif (!empty($resultMsg)) {
                                echo "<span id ='result'>$resultMsg </span>";
                            }
                            ?>
                        </div>

                        <form action="update_admin_profile.php" method="post">
                            <input type="hidden" name="admin_id" value="<?php echo $admin['admin_id']; ?>">                           
                            
                            <label for="name">Name:</label>
                            <input type="text" name="name" value="<?php echo $admin['name']; ?>"><br>
                            <label for="password">Password:</label>
                            <input type="password" name="password"><br>
                            <label for="confirm_password">Confirm Password:</label>
                            <input type="password" name="confirm_password"><br>


                            <div class="submit-btn">
                                <button type="submit">Submit</button>
                            </div>
                        </form>



                    </div>

                </div>

            </div>

        </main>
    </div>
    </div>
    <label for="sidebar-toggle" class="body-label"></label>


</body>

</html>