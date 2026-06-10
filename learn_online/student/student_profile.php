<?php
session_start();
// Establish connection
include "../db_connection.php";

if (!isset($_SESSION['student_email'])) {

    header("Location: ../login/student_login.php");
    exit();
   
}
// Check ifstudent_id is provided in the URL
if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];

    // Prepare and execute SQL statement to fetchstudent record
    $stmt = $conn->prepare("SELECT * FROM registration WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc(); // Fetchstudent data

    // Check ifstudent exists
    if (!$student) {
        // Redirect with error message ifstudent not found
        header("Location:student_profile.php?error=Student not found");
        exit();
    }
} else {
    // Redirect ifstudent_id is not provided
    header("Location:student_profile.php?error=Student ID not provided");
    exit();
}
if (isset($_GET['success']) && $_GET['success'] == 'true') {
    $resultMsg = "Student edited...!";
}

// Check for error message
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'password_mismatch') {
        $errorMsg = "Passwords don't match!";
    } elseif ($_GET['error'] == 'update_failed') {
        $errorMsg = "Failed to update Student details. Please try again later.";
    }
}


// Close statement and connection
$stmt->close();
$conn->close();



$student_name = $_SESSION['student_name'];
$student_email = $_SESSION['student_email'];
$student_picture = $_SESSION['profile_pic'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
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
            <img src="<?php echo htmlspecialchars($student_picture); ?>" alt="Profile Picture">

                <div>
                    <h3><?php echo htmlspecialchars($student_name); ?></h3>
                    <span><?php echo htmlspecialchars($student_email); ?></span>
                </div>
            </div>
            <div class="sidebar-menu">
                <div class="menu-head">
                    <a href="student_dashboard.php">
                        <h2>Dashboard</h2>
                    </a>
                </div>
                <ul>
                    <li> <a href="student_profile.php">Student Profile</a> </li>               

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
            <div class="page-header">
                <div>
                <a href="student_dashboard.php"> <button>
                        Back</button></a>
                </div>
            </div> 

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

                        <form action="update_student_profile.php" method="post">
                            <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">                           
                            <label for="phone">Phone:</label>
                            <input type="text" name="phone" value="<?php echo $student['phone']; ?>"><br>
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