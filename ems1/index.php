<?php
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Event Management System</title>
    <style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background-color: #f9f9fb;
        color: #333;
    }

    .hero {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        color: white;
        padding: 80px 20px;
        text-align: center;
    }

    .hero h1 {
        font-size: 3.5rem;
        margin-bottom: 10px;
    }

    .hero p {
        font-size: 1.3rem;
        opacity: 0.9;
    }





    .section {
        max-width: 1100px;
        margin: 60px auto;
        padding: 0 20px;
    }

    .feature-title {
        text-align: center;
        margin-bottom: 40px;
    }

    .features {
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.05);
        padding: 30px;
        width: 100%;
        max-width: 480px;
        transition: transform 0.3s;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card h3 {
        color: #2c3e50;
        margin-bottom: 15px;
    }

    .card ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .card ul li {
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }

    @media (max-width: 768px) {
        .hero h1 {
            font-size: 2.5rem;
        }

        .features {
            flex-direction: column;
            align-items: center;
        }
    }
    </style>
</head>

<body>

    <div class="hero">
        <h1>Event Management System</h1>
        <p>Plan. Manage. Connect. All in one place.</p>

    </div>

    <div class="section">
        <div class="feature-title">
            <h2>What You Can Do</h2>
        </div>
        <div class="features">
            <div class="card">
                <h3>For Event Organizers</h3>
                <ul>
                    <li>Create & manage events</li>
                    <li>Edit or delete event details</li>
                    <li>View RSVPs & attendance</li>
                </ul>
            </div>
            <div class="card">
                <h3>For Attendees</h3>
                <ul>
                    <li>Browse & RSVP to events</li>
                    <li>Search events by date or keyword</li>
                    <li>Track your upcoming events</li>
                </ul>
            </div>
        </div>
    </div>

</body>

</html>