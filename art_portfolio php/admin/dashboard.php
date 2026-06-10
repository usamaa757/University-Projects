<?php
include 'header.php';



$name = $_SESSION['name'];
$email = $_SESSION['email'];
?>


<a href=""></a>

<main>
    <div class="box">
        <h2>Hello, <?php echo htmlspecialchars($name); ?>! </h2>
        <p>You are logged in with the email: <strong><?php echo htmlspecialchars($email); ?></strong></p>
        <p>Here you can search travel groups, join discussions, and plan your next adventure.</p>

    </div>
</main>

</body>

</html>