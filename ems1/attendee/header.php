<?php
session_start();
// Check if the user is an attendee
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'attendee') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <title>Register</title>
    <style>
    /* Reset some default styles */
    body,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    p,
    a {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f7fa;
        color: #333;
    }

    /* Navbar styles */
    .navbar {
        background-color: #000000;
        /* Change this to any shade of blue */


        padding: 15px 30px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .navbar .container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .navbar .navbar-brand {
        font-weight: bold;
        color: white;
        font-size: 1.5rem;
        text-decoration: none;
    }

    .navbar .navbar-brand:hover {
        color: #f39c12;
    }

    .navbar .nav-links {
        display: flex;
        gap: 20px;
    }

    .navbar .nav-link {
        color: white;
        text-decoration: none;
        font-size: 1rem;
        transition: color 0.3s ease;
    }

    .navbar .nav-link:hover {
        color: #f39c12;
    }

    /* Mobile responsiveness */
    .navbar .navbar-toggler {
        display: none;
    }

    /* Navbar for mobile */
    @media (max-width: 768px) {
        .navbar .navbar-toggler {
            display: block;
            background-color: transparent;
            border: none;
        }

        .navbar .nav-links {
            display: none;
            flex-direction: column;
            gap: 15px;
            margin-top: 10px;
        }

        .navbar .nav-links.active {
            display: flex;
        }

        .navbar .nav-link {
            font-size: 1.25rem;
        }
    }

    .navbar-toggler-icon {
        width: 25px;
        height: 2px;
        background-color: white;
        position: relative;
    }

    .navbar-toggler-icon::before,
    .navbar-toggler-icon::after {
        content: '';
        position: absolute;
        width: 25px;
        height: 2px;
        background-color: white;
        left: 0;
    }

    .navbar-toggler-icon::before {
        top: -7px;
    }

    .navbar-toggler-icon::after {
        top: 7px;
    }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Event Management</a>
            <button class="navbar-toggler" onclick="toggleNavbar()">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="nav-links">
                <a class="nav-link" href="event_list.php">Event List</a>
                <a class="nav-link" href="view_rsvp.php">View RSVPs</a>
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <script>
    // Toggle Navbar visibility for mobile
    function toggleNavbar() {
        const navLinks = document.querySelector('.nav-links');
        navLinks.classList.toggle('active');
    }
    </script>

</body>

</html>