<?php require('header.php'); ?>

<script>
    function showPassword() {
        var x = document.getElementById("password");
        if (x.type === "password") {
            x.type = "text";} else {
            x.type = "password";}}
</script>
<div class="container col-lg-6 justify-content-center align-items-center">
    <div><br><br><h2>Log in</h2></div>
    <div>Please provide email and password</div>
    <hr>
    <form class="login_form row d-grid gap-2" action="validate.php" method="post">
        <div class="col-lg-12 form-group">
            <input id="email" class="form-control" type="email" name="email" placeholder="username@email.com" required>
        </div>
        <div class="col-lg-12 form-group">
            <input id="password" type="password" name="password" placeholder="Password" class="form-control" required>
            <input type="checkbox" onclick="showPassword()"> Show password
        </div>
        <div class="col-lg-12 form-group" align="center">
            <button type="submit" value="Login" class="btn btn-primary btn-lg form-control">Login</button>
        <br><br>
        <a href="signup.php">Not Registered? Sign up</a>
        </div>
        
    </form>
</div>
<?php require('footer.php'); ?>