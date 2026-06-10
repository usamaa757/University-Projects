<?php
// // Start session
session_start();
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Kids Vaccination Booking System (KVBS)</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

</head>

<body>

    <header>
        <div class="header-container">
            <?php

            if (isset($_SESSION['user_id'])) { ?>

            <a href="dashboard.php">
                <h1>Kids Vaccination Booking System (KVBS)</h1>
            </a>

            <?php } ?>
            <a href="index.php">
                <h1>Kids Vaccination Booking System (KVBS)</h1>
            </a>
            <nav>
                <?php

                if (isset($_SESSION['user_id'])) { ?>


                <a href="logout.php" class="logout">Logout</a>

                <?php
                }
                if (isset($_SESSION['role']) == 'admin') { ?>

                <?php
                } else if (isset($_SESSION['role']) == 'parent') { ?>

                <?php
                } else if (isset($_SESSION['role']) == 'worker') { ?>

                <?php
                } else { ?>
                <a href="index.php">Home</a>
                <a href="register.php">Register</a>
                <a href="login.php">Login</a>
                <?php
                }
                ?>

            </nav>
        </div>
    </header>
</body>


</html>