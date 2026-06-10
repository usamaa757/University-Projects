<?php
session_start();
include 'edit_quiz_process.php';
include 'update_quiz.php';
include '../../db_connection.php';
if (!isset($_SESSION['email'])) {

    header("Location: ../../login/admin_login.php");
    exit();
}


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
    <title>Edit Quiz</title>
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
                <li> <a href="quiz_list.php"> Quiz Management </a> </li>
                    
                    <li> <a href="../registration_request.php"> Student Management </a> </li>
                    <li> <a href="../student_list.php">Student Profiles Management </a> </li>
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


            <a href="quiz_list.php"> <button>
                    Back

                </button></a>
            <br><br>
            <div id="container">
                <div>
                    <h3>Quiz</h3>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>?quiz_id=<?php echo $quiz_id; ?>" method="POST">
    <div style="padding: 10px;">
        <?php if (!empty($quiz_details)): ?>
            <?php foreach ($quiz_details as $row): ?>
                <span style='margin:0px 10px'><strong>Start Date</strong></span>
                <input type='date' name='start_date[]' value='<?php echo $row['start_date']; ?>' required>
                <span style='margin:0px 10px'><strong>End Date</strong></span>
                <input type='date' name='end_date[]' value='<?php echo $row['end_date']; ?>' required>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <table width="100%">
            <thead>
                <tr>
                    <th>S No</th>
                    <th>Question</th>
                    <th>Options</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <div class="result-output">
                    <?php
                  
                        if (isset($_GET['result'])) {
                            $resultMsg = $_GET['result'];
                        } elseif (isset($_GET['error'])) {
                            $errorMsg = $_GET['error'];
                        }

                        if (!empty($errorMsg)) {
                            echo "<span id='error'>$errorMsg</span>";
                        } elseif (!empty($resultMsg)) {
                            echo "<span id='result'>$resultMsg</span>";
                        }
                        ?>
                </div>
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">

                <?php if (!empty($questions)): ?>
                    <?php $sno = 1; ?>
                    <?php foreach ($questions as $question_id => $question): ?>
                        <tr>
                            <td><?php echo $sno++; ?></td>
                            <td style="width:50%;">
                                <input style="width:100%;" type="text" name="questions[<?php echo $question_id; ?>][question_text]" value="<?php echo htmlspecialchars($question['question_text']); ?>" required>
                            </td>
                            <td style="width:35%;">
                                <ul>
                                    <?php foreach ($question['options'] as $option): ?>
                                        <li>
                                            <input  style="width:70%;" type="text" name="questions[<?php echo $question_id; ?>][options][<?php echo $option['option_id']; ?>][option_text]" value="<?php echo htmlspecialchars($option['option_text']); ?>" required>
                                            <input type="radio" name="questions[<?php echo $question_id; ?>][correct_option]" value="<?php echo $option['option_id']; ?>" <?php echo $option['is_correct'] ? 'checked' : ''; ?>> Correct
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td>
                                <div class="submit-btn">
                                    <button type="submit">Update</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No questions found for this quiz.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</form>
                <?php echo "</tr>";  ?>
                </tbody>
                </table>


            </div>

    </div>

    </main>
    </div>

    <label for="sidebar-toggle" class="body-label"></label>
</body>

</html>