<?php
include 'header.php';
?>

<style>
body {
    background-image: url('assets/images/bg.jpg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    /* color: white; */
    /* backdrop-filter: blur(1px); */

}

.rounded-box {
    background: rgba(235, 233, 233, 0.38);
    /* backdrop-filter: blur(5px); */
    /* color: white; */
    border-radius: 10px;
    opacity: 1;

    text-align: center;
}
</style>

<div class="rounded-box text-center">
    <div class="container py-5">
        <h1>Welcome to Career Hub</h1>
        <p>Your gateway to finding your dream job or hiring top talent.</p>
    </div>
</div>

<section class="container my-5">
    <div class="row gap-4 justify-content-center">
        <div class="col-md-3 rounded-box shadow">
            <h3>For Job Seekers</h3>
            <p>Create a professional profile, upload your resume, and apply for jobs in just a few clicks.</p>
        </div>
        <div class="col-md-3 rounded-box shadow">
            <h3>For Employers</h3>
            <p>Post job vacancies, review applications, and hire the best candidates with ease.</p>
        </div>
        <div class="col-md-3 rounded-box shadow">
            <h3>For Admins</h3>
            <p>Monitor user accounts, moderate job listings, and ensure a seamless experience for all users.</p>
        </div>
    </div>
</section>

<section class="container my-5 p-3 shadow rounded-box justify-content-center">
    <h2 class="text-center ">Key Features</h2>
    <div>
        <p>✔ User-friendly interface</p>
        <p>✔ Advanced job search filters</p>
        <p>✔ Rhsume upload & profile management</p>
        <p>✔ Employer job posting & application tracking</p>
        <p>✔ Admin control for platform security</p>
        <p>✔ Real-time notifications & email alerts</p>
    </div>
</section>

<!-- Testimonials Section -->
<section class="container my-5 p-3">
    <h2 class="text-center">What Our Users Say</h2>
    <div class="row justify-content-center gap-4">
        <div class="col-md-3 rounded-box shadow p-3 m-2">
            <p>"Career Hub helped me land my dream job in just two weeks!"</p>
            <strong>- Sarah M.</strong>
        </div>
        <div class="col-md-3 rounded-box shadow p-3 m-2">
            <p>"As an employer, I found highly skilled candidates easily. The process was smooth!"</p>
            <strong>- David P.</strong>
        </div>
        <div class="col-md-3 rounded-box shadow p-3 m-2">
            <p>"Managing job listings has never been easier. The admin panel is fantastic!"</p>
            <strong>- Admin Team</strong>
        </div>
    </div>
</section>

<!-- Success Stories -->
<section class="container my-5 text-center p-3 rounded-box shadow">
    <h2>Success Stories</h2>
    <p>Over <strong>10,000+ job seekers</strong> have been successfully placed with top employers.</p>
</section>

<section class="container text-center py-4">
    <a href="register.php" class="btn btn-outline-dark">Get Started</a>
</section>