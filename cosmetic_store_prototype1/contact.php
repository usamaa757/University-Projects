<?php
include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Luxury Cosmetics</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <header class="hero">
        <h1>Contact Us</h1>
        <p>We’d love to hear from you!</p>
    </header>

    <section class="contact-container">
        <h2>Get in Touch</h2>
        <p>If you have any queries, feel free to reach out to us.</p>
        <div class="contact-details">
            <p><i class="fas fa-map-marker-alt"></i> Address: 123 Beauty Lane, Glam City, USA</p>
            <p><i class="fas fa-envelope"></i> Email: support@luxurycosmetics.com</p>
            <p><i class="fas fa-phone"></i> Phone: +1 234 567 890</p>
        </div>


        <form action="#" method="POST">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <textarea name="message" placeholder="Your Message" required></textarea>
            <button type="submit" class="btn">Send Message</button>
        </form>
    </section>

    <footer>
        <p>© 2025 Online Cosmetic Store | Designed with </p>
    </footer>

</body>

</html>