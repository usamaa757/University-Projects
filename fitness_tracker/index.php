<?php
include 'navbar.php';
?>


<!-- ================= HERO ================= -->
<section class="hero">
    <h1>Personal Fitness Tracker</h1>
    <p>
        Track your workouts, meals, and progress — all in one secure platform.
        Get personalized fitness plans, expert trainer feedback, and actionable insights
        to achieve your health goals faster.
    </p>
    <a href="login.php">Get Started</a>
</section>

<!-- ================= FEATURES ================= -->
<section class="section dashboard-container">
    <h2 class="section-title">Why Choose Our Platform?</h2>

    <div class="grid">
        <div class="card">
            <div class="icon">📊</div>
            <h3>Progress Tracking</h3>
            <p>Monitor workouts, meals, water intake, and fitness statistics with clear visual insights.</p>
        </div>

        <div class="card">
            <div class="icon">💬</div>
            <h3>Trainer Feedback</h3>
            <p>Receive expert suggestions and encouragement from certified trainers.</p>
        </div>

        <div class="card">
            <div class="icon">📋</div>
            <h3>Personalized Plans</h3>
            <p>Create customized workout and diet plans tailored to your goals.</p>
        </div>

        <div class="card">
            <div class="icon">🔐</div>
            <h3>Secure & Reliable</h3>
            <p>Role-based access control ensures data privacy and system integrity.</p>
        </div>
    </div>
</section>

<!-- ================= USER ROLES ================= -->
<section class="section dashboard-container">
    <h2 class="section-title">User Roles & Capabilities</h2>

    <div class="grid">
        <div class="card">
            <div class="icon">🛠️</div>
            <h3>Admin</h3>
            <p>
                Manage users and trainers, control public fitness content,
                and monitor system activity logs and feedback.
            </p>
        </div>

        <div class="card">
            <div class="icon">🏋️</div>
            <h3>Trainer</h3>
            <p>
                View user feedback on routines, respond with guidance,
                and suggest improvements to fitness plans.
            </p>
        </div>

        <div class="card">
            <div class="icon">👤</div>
            <h3>Registered User</h3>
            <p>
                Log workouts, meals, water intake, track progress,
                and receive personalized trainer suggestions.
            </p>
        </div>

        <div class="card">
            <div class="icon">🌍</div>
            <h3>Guest User</h3>
            <p>
                Browse fitness tips, workout routines, and nutritional advice
                before registering.
            </p>
        </div>
    </div>
</section>

<!-- ================= CTA ================= -->
<section class="hero">
    <h1>Start Your Fitness Journey Today</h1>
    <p>
        Join thousands of users who are transforming their health with
        structured tracking, expert guidance, and data-driven insights.
    </p>
    <a href="register.php">Create Free Account</a>
</section>

<!-- ================= FOOTER ================= -->
<div class="footer">
    © <?php echo date("Y"); ?> Personal Fitness Tracker | All Rights Reserved
</div>

</body>
</html>
