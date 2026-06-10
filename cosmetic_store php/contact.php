<?php include 'header.php'; ?>

<!-- Banner Section -->
<section class="banner">
    <div class="overlay"></div>
    <div class="banner-content">
        <h1>Contact Us</h1>
        <p>We'd love to hear from you! Reach out with questions or feedback.</p>
        <a href="about.php" class="btn-shop">Learn More About Us</a>
    </div>
</section>

<!-- Contact Form Section -->
<div class="form-container">
    <h2>Get In Touch</h2>
    <form action="send_contact.php" method="post">
        <div style="margin-bottom: 15px;">
            <input type="text" name="name" placeholder="Your Name" required style="width: 100%; padding: 10px;">
        </div>
        <div style="margin-bottom: 15px;">
            <input type="email" name="email" placeholder="Your Email" required style="width: 100%; padding: 10px;">
        </div>
        <div style="margin-bottom: 15px;">
            <input type="text" name="subject" placeholder="Subject" required style="width: 100%; padding: 10px;">
        </div>
        <div style="margin-bottom: 15px;">
            <textarea name="message" placeholder="Your Message" rows="5" required
                style="width: 100%; padding: 10px;"></textarea>
        </div>
        <button type="submit" class="btn-shop">Send Message</button>
    </form>
</div>

<!-- Footer -->
<?php include 'footer.php'; ?>