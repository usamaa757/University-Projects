<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>VU Proctors Diary</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
          crossorigin="anonymous">

    <style>
        body {
            background: url("AP012916-fueling-station-16x9.jpg") no-repeat center center fixed;
            background-size: cover;
            position: relative;
            height: 100vh;
            margin: 0;
        }

        /* Overlay */
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); /* dark overlay */
            z-index: -1;
        }

        .hero-section {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: white;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 70px;
            font-family: Algerian, serif;
            font-weight: bold;
            background: rgba(255,255,255,0.8);
            color: #222;
            padding: 20px 40px;
            border-radius: 15px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.3);
        }

        .hero-section p {
            font-size: 22px;
            margin-top: 15px;
            background: rgba(255,255,255,0.8);
            color: #333;
            padding: 10px 20px;
            border-radius: 10px;
        }

        .btn-custom {
            margin-top: 30px;
            padding: 12px 30px;
            font-size: 18px;
            border-radius: 30px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include "header/header.php";  ?>

    <!-- Hero Section -->
    <div class="hero-section">
        <h1>VU Proctors-Diary</h1>
        <p>Manage Proctors, Duties, and Exams Effectively</p>
        <a href="User_Login.php" class="btn btn-primary btn-lg btn-custom">Get Started</a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
