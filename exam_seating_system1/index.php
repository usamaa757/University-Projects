<?php include 'header.php'; ?>
<link rel="stylesheet" href="styles/home.css">

<div class="hero-section">
    <h1>Welcome to the Exam Seating Arrangement System</h1>
    <p class="tagline">Smart, Efficient & Fair Exam Management for Educational Institutions</p>
    <a href="login.php" class="cta-button">Get Started</a>
</div>

<div class="intro-section">
    <h2>About the Project</h2>
    <p>This web application efficiently manages exam seating arrangements for educational institutions. It simplifies
        assigning seats based on classroom capacity, fairness rules, and student needs.</p>
</div>

<div class="objectives-scope">
    <div class="card">
        <h3>Objectives</h3>
        <ul>
            <li>Create a user-friendly interface for administrators.</li>
            <li>Ensure fair and policy-compliant seating assignments.</li>
            <li>Allow students to easily view their seating info.</li>
        </ul>
    </div>

    <div class="card">
        <h3>Scope</h3>
        <ul>
            <li><strong>In Scope:</strong> Auth, data input, auto-arrangement, notifications</li>
            <li><strong>Out of Scope:</strong> Furniture layout, non-exam arrangements</li>
        </ul>
    </div>
</div>

<div class="features-section">
    <h2>Key Functionalities</h2>
    <div class="features-grid">
        <div class="feature-card">
            <h4>User Authentication</h4>
            <p>Secure login for Admins and Students.</p>
        </div>
        <div class="feature-card">
            <h4>Data Management</h4>
            <p>Manage students, courses, schedules; support CSV uploads.</p>
        </div>
        <div class="feature-card">
            <h4>Seating Generator</h4>
            <p>Auto-generate fair seating based on capacity, distance, and conflicts.</p>
        </div>
        <div class="feature-card">
            <h4>Notifications</h4>
            <p>Email/SMS reminders for students and exam alerts.</p>
        </div>
        <div class="feature-card">
            <h4>Reporting</h4>
            <p>Generate and download PDF seating plans for each exam.</p>
        </div>
    </div>
</div>


<footer class="footer">
    &copy; <?php echo date("Y"); ?> Exam Seating Arrangement System. All rights reserved.
</footer>

</body>

</html>