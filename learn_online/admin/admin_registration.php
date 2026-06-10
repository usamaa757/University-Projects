<?php
session_start();

// Check if admin is logged in, if not, redirect to login page
if (!isset($_SESSION['email'])) {
   header("Location: ../login/admin_login.php");
   exit();
}


include 'admin_registration_process.php';

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
    <title>Admin Registration</title>
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

      <div class="page-header">
                <div>
                <a href="admin_dashboard.php"> <button>
                     Back

                    </button></a>
                </div>


            </div>
         <div class="form-container">
            <div class="headding">
               <h3 class="sub-heading">Admin's Registration</h3>

            </div>
            <div class="form">

               <form method='POST' action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="form" enctype="multipart/form-data">

                  <div class="result-output">
                     <?php
                     echo "<span id='error'>$errorMsg</span>";
                     echo "<span id='result'>$resultMsg</span>";
                     ?>
                  </div>
                  <div class="reg-form-group">
                     <label for="name">Name</label>
                     <input type="text" name="name" id="name" placeholder="Name" required>
                  </div>



                  <div class="reg-form-group">
                     <label for="email">Email</label>
                     <input type="email" name="email" id="email" placeholder="abc@gmail.com" required>
                  </div>
                  <div class="reg-form-group">
                     <label for="picture">Picture</label>
                     <input type="file" id="picture" name="picture" required>
                  </div>
                  <div class="reg-form-group">
                     <label for="password">Password</label>
                     <input type="password" id="password" name="password" required placeholder="Password">
                  </div>
                  
                  <div class="reg-form-group">
                     <label for="confirmPassword">Confirm Password</label>
                     <input type="password" id="confirmPassword" name="confirmPassword" required placeholder="Confirm Password">
                  </div>
                  <div class="submit-btn">
                     <button type="submit">Submit</button>
                  </div>
               </form>
            </div>

         </div>

      </main>
   </div>

   <label for="sidebar-toggle" class="body-label"></label>
</body>

</html>