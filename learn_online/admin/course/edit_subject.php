<?php session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../../login/admin_login.php");
    exit();
}
include '../../db_connection.php';

$errorMsg = "";
$resultMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['subject_id']) && !empty($_POST['subject_id'])) {
        $subject_id = $_POST['subject_id'];
        $subject_name = $_POST['subject_name'];

        // Update subject name in the database
        $stmt = $conn->prepare("UPDATE subjects SET subject_name = ? WHERE subject_id = ?");
        $stmt->bind_param("si", $subject_name, $subject_id);

        if ($stmt->execute()) {
            $resultMsg = "Subject has been updated successfully.";
        } else {
            $errorMsg = "Failed to update the subject.";
        }

        $stmt->close();
    } else {
        $errorMsg = "No subject ID provided.";
    }
} elseif (isset($_GET['subject_id'])) {
    $subject_id = $_GET['subject_id'];

    // Fetch the current subject details
    $stmt = $conn->prepare("SELECT subject_name FROM subjects WHERE subject_id = ?");
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    $stmt->bind_result($subject_name);
    $stmt->fetch();
    $stmt->close();
} else {
    $errorMsg = "No subject ID provided.";
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Management</title>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="../../../assets/css/style.css">
    <link rel="stylesheet" href="../../../assets/css/form.css">
    <link rel="stylesheet" href="../../../assets/fontawesome/web-fonts-with-css/css/fontawesome-all.css">
    <link rel="stylesheet" href="../../../assets/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="../../../assets/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="../../../assets/fontawesome/web-fonts-with-css/css/fontawesome.min.css">
</head>

<body>
    <input type="checkbox" name="" id="sidebar-toggle">
    <div class="sidebar">

        <div class="sidebar-main">
            <div class="sidebar-user">
                <img src="../../../assets/img/logo.png" alt="logo Image">
                <div>
                    <h3><?php echo $_SESSION['name']; ?></h3>
                    <span><?php echo $_SESSION['email']; ?></span>
                </div>
            </div>
            <div class="sidebar-menu">
                <div class="menu-head">
                    <a href="../../admin_panel.php">
                        <h2>Dashboard</h2>
                    </a>
                </div>
                <ul>
                <li> <a href="../admin_profile.php">Profile</a> </li>
                    <li> <a href="course_management.php">Course Management</a> </li>
                    <li> <a href="../assignment/assignment_record.php"> Assignment Management</a> </li>
                    <li> <a href="../quiz/quiz_list.php"> Quiz Management </a> </li>
                    <li> <a href="../lesson/lesson_record.php"> Lesson Management </a> </li>
                    <li> <a href="../admin_registration.php"> Admin Registration </a> </li>
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

                <a href="../../../login/logout.php"> <button>
                        <!-- <span class="las la-file-export"></span> -->
                        <span class="fa fa-power-off"></span>

                    </button></a>


            </div>
        </header>

        <main>


         
            <br>
            <div class="container">
        <h3>Edit Subject</h3>
        <?php
        if (!empty($errorMsg)) {
            echo "<span id='error'>$errorMsg</span>";
        } elseif (!empty($resultMsg)) {
            echo "<span id='result'>$resultMsg</span>";
        }
        ?>
        <div>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" name="subject_id" value="<?php echo htmlspecialchars($subject_id); ?>">
            <label for="subject_name">Subject Name:</label>
            <input type="text" name="subject_name" id="subject_name" value="<?php echo htmlspecialchars($subject_name); ?>" required>
            <button type="submit">Update Subject</button>
        </form>
        <button onclick="window.location.href='class_management.php'">Back</button>
    </div>
    </div>



    </main>
    </div>

    <label for="sidebar-toggle" class="body-label"></label>


</body>

</html>