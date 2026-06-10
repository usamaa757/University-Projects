<?php
include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Online Seed Record System</title>
    <!-- <link rel="stylesheet" href="style.css"> -->
    <style>
    body {
        background: url('https://images.unsplash.com/photo-1498654896293-37aacf113fd9') no-repeat center center/cover;
        height: 100vh;
        color: #fff;
        display: flex;
        flex-direction: column;
    }

    /* Index  */


    /* --- Overlay --- */
    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
    }


    /* --- Hero Section --- */
    .hero {
        position: relative;
        z-index: 10;
        text-align: center;
        margin: 30px auto;
        width: 80%;
        max-width: 1000px;
        padding: 60px;
        background: rgba(0, 0, 0, 0.46);
        border-radius: 20px;
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.1);
    }



    .hero p {
        text-align: justify;
        line-height: 1.8;
        color: #e0e0e0;
        font-size: 1.05rem;
    }

    @media (max-width: 768px) {
        .hero {
            width: 90%;
            padding: 30px;
        }

        .hero h1 {
            font-size: 2.2rem;
        }

        nav ul {
            flex-direction: column;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            padding: 10px;
        }
    }
    </style>
</head>

<body>

    <section class="hero">
        <h1>Online Portal for Tracking Seeds Record for Cultivation</h1>
        <h2>A Web-Based Agricultural Management Solution</h2>

        <p>
            In the agricultural sector, maintaining accurate and efficient records of a variety of seeds and their usage
            is crucial for productivity and sustainability. The <strong>SeedTrack Portal</strong> is a web-based
            application
            designed to streamline seed record management, enhance transparency, and improve efficiency across the
            agricultural ecosystem of Pakistan.
            <br><br>
            This digital platform enables users to explore seeds by category, view complete details, and place online
            orders securely. Registered <strong>Seed Agents</strong> such as farmers, research institutes, or
            agricultural
            firms can upload seed information after admin approval.
            <br><br>
            Through real-time data visualization and dashboards, administrators and stakeholders can track seed
            performance, monitor sales, and make informed decisions for better cultivation outcomes.
        </p>

        <a href="login.php" class="btn">Get Started</a>
    </section>

</body>

</html>