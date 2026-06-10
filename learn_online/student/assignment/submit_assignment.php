<?php
session_start();
if (!isset($_SESSION['student_email'])) {
    header("Location: ../../login/student_login.php");
    exit();
}

// Fetch assignments
include 'student_assingment_fetch_process.php';
include 'assignment_submission_process.php'; 

if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
  
}

$student_name = $_SESSION['student_name'];
$student_email = $_SESSION['student_email'];
$student_picture = $_SESSION['profile_pic'];

$baseUrl = 'http://localhost/learn_online/'; 
$imagePath = $baseUrl . str_replace('../', '', htmlspecialchars($student_picture));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Submission</title>
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
                    <h3><?php echo htmlspecialchars($student_name); ?></h3>
                    <span><?php echo htmlspecialchars($student_email); ?></span>
                </div>
            </div>
            <div class="sidebar-menu">
                <div class="menu-head">
                <a href="../student_dashboard.php">
                        <h2>Dashboard</h2>
                    </a>
                </div>
                <ul>
                    <li> <a href="../student_profile.php">Student Profile</a> </li>
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
            
                <a href="../../login/logout.php">
                    <button>
                        <span class="fa fa-power-off"></span>
                    </button>
                </a>
            </div>
        </header>
       
        <main>
        <a href="assignment_list.php?course_id= <?php echo $course_id;?>"> <button>
                        Back

                    </button></a>
                    <br><br>
            <div class="page-header">
                <div>

                    <?php if (!empty($assignments)) : ?>
                        <h3><?php echo $assignments[0]['course_name']; ?></h3>
                    <?php endif; ?>

                    <?php 
                    $sno = 1;
                    // Loop through the assignments array to populate HTML divs
                    foreach ($assignments as $assignment) {
                    ?>
                        <div class="assignment-container">
                            <h3>Assignment No: <?php echo $sno++ ?></h3>
                            <div style="padding: 15px">
                                <?php
                                // Display error or result messages
                                if (!empty($errorMsg)) {
                                    echo "<span id='error'>$errorMsg</span>";
                                } elseif (!empty($resultMsg)) {
                                    echo "<span id='result'>$resultMsg</span>";
                                }
                                ?>
                                <p><strong>Assignment Question:</strong> <?php echo $assignment["assignment_question"]; ?></p>
                             

                                <p>Answer: </p>
                                <form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?assignment_id=' . $assignment['assignment_id'] . '&course_id=' . $assignment['course_id']; ?>">
                                    <textarea rows="6" cols="50" name="assignment_answer" placeholder="Enter your answer here"></textarea>
                                    <br>
                                    <input type="hidden" name="course_id" value="<?php echo $assignment['course_id']; ?>">
                                    <input type="hidden" name="assignment_id" value="<?php echo $assignment['assignment_id']; ?>">
                                    <button type="submit">Submit Answer</button>
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
