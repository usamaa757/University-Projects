<?php
session_start();

if (!isset($_SESSION['student_email'])) {
  header("Location: ../../login/student_login.php");
  exit();
}

include '../../db_connection.php';

$lecture_title = '';
$video_url = '';

if (isset($_GET['course_id'])) {
  $course_id = intval($_GET['course_id']); // Validate course_id (example)
  $sql = "SELECT course_name FROM courses WHERE course_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('i', $course_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($course = $result->fetch_assoc()) {
    $course_name = $course['course_name'];
  }
  $lesson_id = (isset($_GET['lesson_id'])) ? intval($_GET['lesson_id']) : ''; // Validate lesson_id (optional)

  // ... (database connection code)

  $sql = "SELECT * FROM lessons WHERE course_id = $course_id"; // Assuming lesson_id is optional
  if ($lesson_id) {
    $sql .= " AND lesson_id = $lesson_id";
  }

  $result = mysqli_query($conn, $sql);

  if ($result && mysqli_num_rows($result) > 0) {
    $lecture = mysqli_fetch_assoc($result);
    $file_name = $lecture['file_name'];
    $video_url = "../../admin/lesson/videos/" . $file_name;
  }
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
  <title>Lesson</title>
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

        <a href="../../login/logout.php"> <button>
            <!-- <span class="las la-file-export"></span> -->
            <span class="fa fa-power-off"></span>

          </button></a>


      </div>
    </header>

    <main>
      <div class="page-header">
        <div>
                   <a href="student_lesson_list.php?course_id=<?php echo $course_id; ?>">
            <button>
              Back
            </button>
          </a>
        </div>
      </div>
      <br>
      <div id="quiz-container">
        <div>
          <h3> <?php echo $course_name ?></h3><br>
        </div>


        <div>
        <div>
                   <a href="<?php echo $video_url; ?>">
         
              Download Lesson
          
          </a>
        </div>

          <br>
          <div style="text-align: center;">
            <?php

            if (!empty($video_url)) :
            ?>
              <video width="560" height="315" controls>
                <source src="<?php echo $video_url; ?>" type="video/mp4">
                Your browser does not support the video tag.
              </video>
            <?php

            endif;
            ?>

          </div>



        </div>

      </div>

  </div>

  </main>
  </div>

  <label for="sidebar-toggle" class="body-label"></label>
</body>

</html>