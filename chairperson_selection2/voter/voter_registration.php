<?php
include("voter_registration_process.php");
require("../header.php");
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-6">
            <div class="card shadow-lg">
                <div class="card-header bg-dark text-white">
                    <h4 class="card-title text-center mb-0">Voter Registration</h4>
                </div>
                <div class="card-body">
                    <form id="registrationForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <div class="mt-3">
                            <?php
                            if (!empty($errorMsg)) {
                                echo "<div class='alert alert-danger' role='alert'>$errorMsg</div>";
                            }
                            if (!empty($resultMsg)) {
                                echo "<div class='alert alert-success' role='alert'>$resultMsg</div>";
                            }
                            ?>
                        </div>

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="" disabled selected>Select your gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="studentId">Student ID</label>
                            <input type="text" class="form-control" id="studentId" name="student_id" placeholder="Enter your student ID" required>
                        </div>
                        <div class="form-group">
                            <label for="department">Department</label>
                            <input type="text" class="form-control" id="department" name="department" placeholder="Enter your department" required>
                        </div>
                        <button type="submit" class="btn btn-dark w-100">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>