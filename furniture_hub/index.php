<?php
include "navbar.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Renewed Furniture Hub</title>
    <link rel="stylesheet" href="styles.css">
    <style>
    header {
        background: linear-gradient(to right, #5d3fd3, #8e7df5);
        color: #fff;
        text-align: center;
        padding: 6rem 2rem;
    }


    header p {
        font-size: 1.2rem;
        max-width: 800px;
        margin: auto;
        color: #fff;
    }

    header h1 {
        color: #fff;

    }

    section {
        padding: 4rem 2rem;
        max-width: 1100px;
        margin: auto;
    }

    section h2 {
        text-align: center;
        margin-bottom: 2rem;
        font-size: 2rem;
        color: #5d3fd3;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
    }

    .info-card {
        background: #fff;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s;
    }

    .info-card:hover {
        transform: translateY(-5px);
    }

    .info-card h3 {
        color: #5d3fd3;
        margin-bottom: 1rem;
    }

    .info-card p {
        line-height: 1.6;
    }

    footer {
        background: #333;
        color: #fff;
        text-align: center;
        padding: 2rem;
    }

    footer a {
        color: #5d3fd3;
        text-decoration: none;
    }
    </style>
</head>

<body>

    <header>
        <h1>Renewed Furniture Hub</h1>
        <p>Buy & Sell Used Furniture from the comfort of your home. Our platform allows you to upload, manage, and
            purchase furniture easily with secure transactions and user-friendly interfaces.</p>
    </header>

    <section>
        <h2>User Types</h2>
        <div class="info-grid">
            <div class="info-card">
                <h3>Guest (Unregistered User)</h3>
                <p>Can browse available furniture, view descriptions, prices, and seller contact information. Cannot
                    upload or purchase furniture without registering.</p>
            </div>
            <div class="info-card">
                <h3>Registered User</h3>
                <p>Can upload furniture for sale, place orders, update profile, submit reviews, feedback, complaints,
                    and provide ratings to sellers after purchase.</p>
            </div>
            <div class="info-card">
                <h3>Administrator (Admin)</h3>
                <p>Has full control over the website. Can manage users, approve/reject registrations, manage furniture
                    listings, block/unblock items, generate reports, and oversee all transactions.</p>
            </div>
        </div>
    </section>

    <section>
        <h2>Functional Features</h2>
        <div class="info-grid">
            <div class="info-card">
                <h3>User Registration & Login</h3>
                <p>Easy signup interface for new users and secure login for registered users to access full
                    functionalities.</p>
            </div>
            <div class="info-card">
                <h3>View Furniture</h3>
                <p>View furniture details with complete descriptions, images, condition, price, and seller information.
                </p>
            </div>
            <div class="info-card">
                <h3>Upload & Manage Furniture</h3>
                <p>Registered users can upload furniture, edit details, update pricing, and remove listings when
                    necessary.</p>
            </div>
            <div class="info-card">
                <h3>Search & Filter</h3>
                <p>Search furniture by category, price range, condition, and location for quick access to desired items.
                </p>
            </div>
            <div class="info-card">
                <h3>Order & Payment</h3>
                <p>Place purchase orders securely with options for cash on delivery or bank transfer directly to the
                    seller.</p>
            </div>
            <div class="info-card">
                <h3>Feedback & Complaints</h3>
                <p>Submit feedback, ratings, and complaints for transactions. Admin can review complaints and take
                    appropriate actions.</p>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Renewed Furniture Hub. All rights reserved.</p>
    </footer>

</body>

</html>