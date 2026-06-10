<?php
session_start();
if(!$_SESSION['email']){
    header("Location: ../../login/admin_login.php" );
}


include 'create_quiz_process.php';


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
    <title>Add Quiz Question</title>
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
                    <li> <a href="../assignment/assignment_record.php"> Assignment Management</a> </li>
                    <li> <a href="quiz_list.php"> Quiz Management </a> </li>
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
                        <!-- <span class="las la-file-export"></span> -->
                        <span class="fa fa-power-off"></span>
                    </button></a>
            </div>
        </header>
        <main>
            <div class="page-header">
                <div>
                <a href="create_quiz.php?course_id=<?php echo $course_id;?>"> <button>
                       Back
                    </button></a>
               
                </div>
                <br>
                <br>
            </div>
            <div id="container">
                <div style="width: auto;">
                    <h3><?php echo $course_name; ?></h3>
                </div>
                <div style="padding: 15px 5px;">
                    <div class="result-output">
                        <?php
                        if (!empty($errorMsg)) {
                            echo "<span id = 'error'>$errorMsg</span>";
                        } elseif (!empty($resultMsg)) {
                            echo "<span id ='result'>$resultMsg </span>";
                        }
                        ?>
                    </div>
            
                   
                    <form action="<?php echo $_SERVER['PHP_SELF'] . '?quiz_id=' . $_GET['quiz_id']; ?>" method="post">
                    <!-- <h4><?php echo "Quiz No: " . $sn++; ?></h4> -->

                        <div class="quiz-content">
                            <label for="question">Question:</label><br>
                            <textarea id="question" name="question" rows="4" cols="70"></textarea><br>

                            <label for="option1">Option 1:</label>
                            <input type="text" id="option1" name="option1">

                            <label for="option2">Option 2:</label>
                            <input type="text" id="option2" name="option2">

                            <label for="option3">Option 3:</label>
                            <input type="text" id="option3" name="option3">

                            <label for="option4">Option 4:</label>
                            <input type="text" id="option4" name="option4">

                            <label for="correct_answer">Correct Answer:</label>
                            <select id="correct_answer" name="correct_answer">
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                                <option value="4">Option 4</option>
                            </select>
                        </div>

                        <div class="submit-btn">
                            <button type="submit" name="submit">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <label for="sidebar-toggle" class="body-label"></label>
</body>

</html>