<?php

session_start();
$_SESSION['start_time'] = time();
$total_time_allowed = 60; // 1 minute

if (!isset($_SESSION['start_time'])) {
    $_SESSION['start_time'] = time(); // Initialize the start time
}

$elapsed_time = time() - $_SESSION['start_time'];
$remaining_time = $total_time_allowed - $elapsed_time;

if ($remaining_time <= 0) {
    // Time is up, redirect to timeout page
    header('Location: timeout_page.php');
    exit;
}


// Check if student is logged in, if not, redirect to login page
if (!isset($_SESSION['student_email'])) {
    header("Location: ../../login/student_login.php");
    exit();
}

include 'submit_quiz_process.php';
include 'fetch_quiz.php';

if (isset($_GET['subject_id']) && isset($_GET['subject_name'])) {
    $subject_id = $_GET['subject_id'];
    $subject_name = $_GET['subject_name'];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Record</title>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/form.css">
    <link rel="stylesheet" href="../../assets/fontawesome/web-fonts-with-css/css/fontawesome-all.css">
    <link rel="stylesheet" href="../../assets/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="../../assets/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="../../assets/fontawesome/web-fonts-with-css/css/fontawesome.min.css">
    <script>
        var totalSeconds = <?php echo $total_time_allowed; ?>;
        var remainingSeconds = sessionStorage.getItem('remainingSeconds');

        if (remainingSeconds) {
            totalSeconds = parseInt(remainingSeconds);
        } else {
            sessionStorage.setItem('remainingSeconds', totalSeconds);
        }

        var timerInterval = setInterval(function() {
            totalSeconds--;
            sessionStorage.setItem('remainingSeconds', totalSeconds);

            if (totalSeconds <= 0) {
                clearInterval(timerInterval);
                sessionStorage.removeItem('remainingSeconds'); // Remove the remaining time from session storage
                window.location.href = 'view_quiz.php?subject_id=<?php echo $subject_id; ?>';
            } else {
                var minutes = Math.floor(totalSeconds / 60);
                var seconds = totalSeconds % 60;
                document.getElementById('timer').innerHTML = minutes + 'm ' + seconds + 's';
            }
        }, 1000);
    </script>
    <style>
    .flex-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .flex-container h3 {
        flex: 1;
        text-align: center;
    }
</style>
</head>

<body>
    <input type="checkbox" name="" id="sidebar-toggle">
    <div class="sidebar">

        <div class="sidebar-main">
            <div class="sidebar-user">
                <img src="../../assets/img/logo.png" alt="logo Image">

                <div>
                    <h3><?php echo $_SESSION['student_name']; ?></h3>
                    <span><?php echo $_SESSION['student_email']; ?></span>
                </div>
            </div>
            <div class="sidebar-menu">
                <div class="menu-head">
                    <a href="../student_panel.php">
                        <h2>Dashboard</h2>
                    </a>
                </div>
                <ul>
                    <li> <a href="../student_profile.php">Student Profile</a> </li>
                    <li> <a href="../fee_voucher.php"> Fee Voucher</a> </li>
                    <li> <a href="../attendance_status.php">Attendance Status </a> </li>
                    <li> <a href=""> Help </a> </li>

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



            <div id="container">
                <div class="flex-container">
                    <h3 style="text-align: left;"><?php echo $subject_name ?></h3>
                    <h3 id="timer" style="text-align: right;"></h3>
                </div>
                <div class="table-responsive">



                    <form action="<?php echo $_SERVER['PHP_SELF'] . '?quiz_id=' . $_GET['quiz_id']; ?>" method="post" enctype="multipart/form-data">
                        <table width="100%">
                            <thead>
                                <tr>
                                    <th>Question</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($questions as $question) : ?>
                                    <tr>
                                        <td>
                                            <div><?php echo $question['question_text']; ?></div>
                                            <ul>
                                                <?php foreach ($question['options'] as $option) : ?>
                                                    <li>
                                                        <label>
                                                            <input type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="<?php echo $option['option_id']; ?>" required>
                                                            <?php echo $option['option_text']; ?>
                                                        </label>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                </div>

            </div>
            <div class="submit-btn">
                <button type="submit" name="submit">Submit Quiz</button>
            </div>
            </form>



    </div>
    </main>
    </div>
    <label for="sidebar-toggle" class="body-label"></label>
</body>

</html>