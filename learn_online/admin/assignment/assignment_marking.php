<?php
session_start();
if (!isset($_SESSION['email'])) {

    header("Location: ../../login/admin_login.php");
    exit();
}
include '../../db_connection.php';
$assignments = [];
if (isset($_GET['assignment_id']) && isset($_GET['student_id'])) {
    $assignment_id = $_GET['assignment_id'];
    $student_id = $_GET['student_id'];

    $fetch_assignments_query = "SELECT sa.*, c.course_name
    FROM student_assignment sa 
    INNER JOIN courses c ON sa.course_id = c.course_id
    WHERE sa.assignment_id = ? AND student_id = ? ";
    $stmt = $conn->prepare($fetch_assignments_query);
    $stmt->bind_param("ii", $assignment_id, $student_id);
    $stmt->execute();
    $assignments_result = $stmt->get_result();

    if ($assignments_result->num_rows > 0) {
        while ($row = $assignments_result->fetch_assoc()) {
            $assignments[] = $row;
        }
    } else {
        $errorMsg = "No assignments found for marking.";
    }
} else {
    $errorMsg = "No assignment ID found.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $assignment_id = $_POST['assignment_id'];
    $marks = $_POST['marks'];

    // Validate input
    if (empty($student_id) || empty($assignment_id) || empty($marks)) {
        $errorMsg = "All fields are required.";
    } else {
        // Update the database
        $sql = "UPDATE student_assignment SET marks = ? WHERE student_id = ? AND assignment_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $marks, $student_id, $assignment_id);

        if ($stmt->execute()) {
            $resultMsg = "Marks added successfully.";
        } else {
            $errorMsg = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();

$admin_name = $_SESSION['name'];
$admin_email = $_SESSION['email'];
$admin_picture = $_SESSION['profile_pic'];
$baseUrl = 'http://localhost/learn_online/';
$imagePath = $baseUrl . str_replace('../', '', htmlspecialchars($admin_picture));

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Marking</title>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="../../assets/css/form.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/fontawesome/web-fonts-with-css/css/fontawesome-all.css">
    <link rel="stylesheet" href="../../assets/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="../../assets/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="../../assets/fontawesome/web-fonts-with-css/css/fontawesome.min.css">
</head>

<body>
    <input type="checkbox" name="" id="sidebar-toggle">
    <div class="sidebar">

        <div class="sidebar-main">
            <div class="sidebar-user">
                <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Profile Picture">

                <div>
                    <h3><?php echo htmlspecialchars($admin_name); ?></h3>
                    <span><?php echo htmlspecialchars($admin_email); ?></span>
                </div>
            </div>
            <div class="sidebar-menu">
                <div class="menu-head">
                    <a href="../admin_dashboard.php">
                        <h2>Dashboard</h2>
                    </a>
                </div>
                <ul>
                <li> <a href="../admin_profile.php">Profile</a> </li>
                    <li> <a href="../course/course_management.php">Course Management</a> </li>
                    <li> <a href="assignment_record.php"> Assignment Management</a> </li>
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
                
                <a href="../../login/logout.php"> <button>
                        <span class="fa fa-power-off"></span>
                    </button></a>
            </div>
        </header>

        <main>
            <div class="page-header">
                <div>
                <a href="assignment_list.php?assignment_id=<?php echo $assignment_id;?>"> <button>
                        Back
                    </button></a>
                </div>
            </div>
            <br>
                    <?php
                    $sno = 1;
                   
                  if (!empty($assignments)) : ?>
                        <h3><?php echo $assignments[0]['course_name']; ?></h3>
                    <?php endif; ?>
                    <?php
     
                    foreach ($assignments as $assignment) {
                    ?>
                   
                        <div class="assignment-container">
                           
                            <h3>Assignment No: <?php echo $sno++ ?></h3>
                            <div style="padding: 15px">
                                <?php
                                if (!empty($errorMsg)) {
                                    echo "<span id='error'>$errorMsg</span>";
                                } elseif (!empty($resultMsg)) {
                                    echo "<span id='result'>$resultMsg</span>";
                                }
                                ?>
                                
                                <p><strong>Student ID:</strong> <?php echo $assignment["student_id"]; ?></p>
                  

                                <p><strong>Assignment Answer:</strong> <?php echo $assignment["assignment_answer"]; ?></p>
                                <form method='POST' action='assignment_marking.php?assignment_id=<?php echo $assignment['assignment_id'] . '&student_id=' . $assignment['student_id']; ?>'>
                                    <input type='hidden' name='assignment_id' value='<?php echo $assignment["assignment_id"] ?>'>
                                    <input type='hidden' name='student_id' value='<?php echo $assignment["student_id"] ?>'>
                                    <label for='marks'>Marks:</label>
                                    <input type='number' name='marks' id="marks" value='<?php echo $assignment["marks"];?>' required>
                                    <button type='submit'>Submit</button>
                                </form>

                            </div>
                        </div>
                    <?php
                    }
                    ?>



                </div>

        </main>
    </div>

    <label for="sidebar-toggle" class="body-label"></label>
</body>

</html>