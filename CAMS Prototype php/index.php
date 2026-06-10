<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomePage</title>
    <!-- <link rel="stylesheet" href="CSS/style.css"> -->
    <link rel="stylesheet" href="CSS/homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <header>
        <nav class="container">
            <div class="logo">
                CAMS <span>-Admission Portal</span>
            </div>
            <ul>
                <a href="index.php">
                    <li>Home</li>
                </a>
                <li class="dropdown">
                    <a href="#">User</a>
                    <ul class="dropdown-menu">
                        <li><a href="user-login.php">Login/Registration</a></li>
                        <li><a href="user-profile.php">User Profile</a></li>
                    </ul>
                </li>
                </a>
                <a href="admin-login.php">
                    <li>Admin</li>
                </a>
            </ul>
        </nav>
    </header>
    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>Welcome to the College Admission Portal</h1>
                <p>Apply for your desired courses and track your admission status easily.</p>
                <a href="user-login.php" class="btn">Apply Online</a>
            </div>
        </section>
    </main>

    <!-- Footer Section -->
    <footer class="footer">
        <div class="container">
            <div class="footer-columns">
                <div class="footer-column">
                    <h4>About Us</h4>
                    <p>Our College Admission Portal streamlines the application process, helping students apply for their desired courses and track admission status in a user-friendly and efficient manner.</p>
                </div>
                <!-- <div class="footer-column">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="admin.php">Admin</a></li>
                        <li><a href="user.php">User</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Follow Us</h4>
                    <ul class="social-links">
                        <li><a href="#"><i class="fab fa-facebook-f"></i> Facebook</a></li>
                        <li><a href="#"><i class="fab fa-twitter"></i> Twitter</a></li>
                        <li><a href="#"><i class="fab fa-instagram"></i> Instagram</a></li>
                    </ul>
                </div> -->
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 College Admission Portal. All rights reserved.</p>
        </div>
    </footer>

</body>

</html>