<?php
include '../db_connection.php';
$errorMsg = '';
$resultMsg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $name = $_POST['name'];
  $gender = $_POST['gender'];
  $party = $_POST['party'];
  $department = $_POST['department'];
  $description = $_POST['description'];

  // Prepare and bind
  $stmt = $conn->prepare("INSERT INTO candidates (name, party, gender, department, description) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("sssss", $name, $party, $gender, $department, $description);

  // Execute the statement
  if ($stmt->execute()) {
    $resultMsg = "Candidate registration successful.";
  } else {
    $errorMsg = "Registration failed. Please try again.";
  }

  // Close the statement and connection
  $stmt->close();
  $conn->close();
}
require "../header.php";
?>

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg">
          <div class="card-header bg-dark text-white">
            <h4 class="card-title text-center mb-0">Candidate Registration</h4>
          </div>
          <div class="card-body">
            <form id="candidateForm" action="candidate_registration.php" method="post">
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
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter candidate name" required>
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
                <label for="department">Department Name</label>
                <input type="text" class="form-control" id="department" name="department" placeholder="Candidate's Department" required>
              </div>
              <div class="form-group">
                <label for="party">Party</label>
                <input type="text" class="form-control" id="party" name="party" placeholder="Enter party name" required>
              </div>
              <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Enter candidate description" rows="3"></textarea>
              </div>
              <button type="submit" class="btn btn-dark w-100">Submit</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
