<?php 
include 'login_process.php';
require('../header.php');
?>

    <!-- Home Page Content -->
    <div class="container mt-4">
        <div class="row">
            <!-- Contact Form -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        Student Login
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                            <div class="form-group">
                                <?php
                                if (!empty($errorMsg)) {
                                    echo "<span class='text-danger'>$errorMsg</span>";
                                } elseif (!empty($resultMsg)) {
                                    echo "<span class='text-success'>$resultMsg</span>";
                                }
                                ?>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" class="form-control" id="email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control" id="password">
                            </div>
                            <button type="submit" class="btn btn-primary">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="js/jquery-3.5.1.slim.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
