<?php require('header.php'); ?>

<script>
    function showPassword() {
        var x = document.getElementById("password");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
</script>
<div class="container col-lg-6 justify-content-center align-items-center">
    <br><br><h2> Sign up</h2>
    <div>Please fill in this form to create an account!</div>
    <hr>
    <form action="addUser.php" method="POST" class="d-grid gap-2">
        <div class="form-group">
            <label style="font-weight:bold;" name="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
        </div>
        <div class="form-group">
        <label style="font-weight:bold;" name="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="form-group">
        <label style="font-weight:bold;" name="password">Password:</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
            <input type="checkbox"  onclick="showPassword()"> Show password
        </div>
        <div class="form-group">
        <label style="font-weight:bold;" name="contact">Contact:</label>
            <input type="number" class="form-control" id="contact" name="contact" placeholder="Contact" required>
            <span style="color: red; font-size: small">Contact number must be 11 digits</span>
        </div>
        <div  align="center">
        <button type="submit" id="signup" class="btn btn-primary btn-lg form-control">Register</button>
        <br><br>
        <a href="login.php">Already have an account? Login</a>
        </div>
    </form>
</div>
<?php require('footer.php'); ?>