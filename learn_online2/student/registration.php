<?php
include 'registration_process.php';
include '../include_files/fetch_table_data.php';
$data = fetchTableData('courses');

require ('../header.php');
?>

    <!-- Home Page Content -->
    <div class="container mt-4">
        <div class="row">
            <!-- Registration Form -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        Registration Form
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
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
                                <label for="name">Name</label>
                                <input type="text" name="name" class="form-control" id="name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" class="form-control" id="email" required>
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select name="gender" id="gender" class="form-control" required>
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="others">Others</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="course_id">Courses</label>
                                <select name="course_id" id="course_id" class="form-control" required>
                                    <option value="" disabled selected>Select Course</option>
                                    <?php foreach ($data as $course) { ?>
                                        <option value="<?php echo $course["course_id"]; ?>"><?php echo $course["course_name"]; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" name="phone" class="form-control" id="phone" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control" id="password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" id="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Now</button>
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
