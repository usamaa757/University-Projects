<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>VU Proctors Diary</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: url('bg.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .welcome-box {
            background: rgba(199, 199, 199, 0.31);
            padding: 50px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            max-width: 900px;
            margin: 60px auto;
            text-align: center;
        }

        .welcome-box h1 {
            color: #007bff;
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        p,
        li,
        footer {
            color: #000;

        }

        .welcome-box p {
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .features {
            text-align: left;
            margin-top: 30px;
        }

        .features h2 {
            color: #0056b3;
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        .features ul {
            list-style-type: square;
            line-height: 1.7;
            padding-left: 25px;
        }

        .roles {
            margin-top: 40px;
            background: #f7f9fc;
            padding: 25px;
            border-radius: 10px;
            text-align: left;
        }

        .roles h3 {
            color: #007bff;
            margin-bottom: 10px;
        }

        .roles p {
            margin: 5px 0 15px;
        }

        footer {
            text-align: center;
            margin-top: 40px;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="welcome-box">
        <h1>Welcome to VU Proctors Diary</h1>
        <p>
            The <strong>VU Proctors Diary</strong> is a centralized platform designed to streamline exam duty
            management at Virtual University centers.
            It enables smooth coordination between the <strong>Administration</strong>,
            <strong>Superintendents</strong>, and <strong>Invigilators</strong>.
        </p>

        <div class="features">
            <h2>Key Features</h2>
            <ul>
                <li>Register, update, and manage your professional profile.</li>
                <li>Receive automatic duty assignments and notifications.</li>
                <li>Mark attendance and upload examination reports.</li>
                <li>Track payment details based on verified duties.</li>
                <li>Apply for leave and manage your availability schedule.</li>
                <li>Admin dashboard for approvals, analytics, and performance tracking.</li>
            </ul>
        </div>

        <div class="roles">
            <h3>For Admins</h3>
            <p>Approve user registrations, assign duties, verify attendance, and process payments efficiently.</p>

            <h3>For Superintendents</h3>
            <p>Manage invigilators, upload daily reports, and ensure exam integrity at your center.</p>

            <h3>For Invigilators</h3>
            <p>View assigned duties, mark attendance, and stay informed with instant notifications.</p>
        </div>

        <footer>
            &copy; <?= date("Y"); ?> Virtual University — Proctors Diary | Designed for efficient exam management.
        </footer>
    </div>
</body>

</html>