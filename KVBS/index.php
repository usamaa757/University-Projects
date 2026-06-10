<?php
include("header.php");
?>

<div class="hero">
    <div class="hero-content">
        <div class="hero-text">
            <h1>Welcome to <span>KVBS</span></h1>
            <p>
                The <strong>Kids Vaccination Booking System</strong> helps parents easily schedule safe home
                vaccinations for their children. Our healthcare workers come to you — ensuring
                timely, comfortable, and trusted vaccination care for your little ones.
            </p>

            <div class="buttons">
                <a href="register.php">Register as Parent</a>
                <a href="login.php" class="secondary">Login</a>
            </div>
        </div>

        <div class="hero-image">
            <img src="home.jpg" alt="Vaccination Illustration">
        </div>
    </div>
</div>
<section class="features">
    <h2>Why Choose KVBS?</h2>
    <div class="feature-cards">
        <div class="card">
            <i class="fa-solid fa-house-medical"></i>
            <h3>Home Vaccination</h3>
            <p>Healthcare workers visit your home at your preferred time and date for your child's safety.</p>
        </div>
        <div class="card">
            <i class="fa-solid fa-user-nurse"></i>
            <h3>Safe & Verified</h3>
            <p>All our medical professionals are verified and follow safe vaccination protocols.</p>
        </div>
        <div class="card">
            <i class="fa-solid fa-calendar-check"></i>
            <h3>Easy Booking</h3>
            <p>Book, track, and manage vaccinations easily through our secure online system.</p>
        </div>
    </div>
</section>
<?php

include('footer.php');

?>
</body>

</html>