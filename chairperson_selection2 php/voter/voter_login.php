<?php
require_once("../header.php");
?>

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-4">
        <div class="card shadow-lg">
          <div class="card-header bg-dark text-white">
            <h4 class="card-title text-center mb-0">Voter Login</h4>
          </div>
          <div class="card-body">
            <form id="loginForm" action="voter_login_process.php" method="post">
              <div class="form-group">
                <label for="voterId">Voter ID</label>
                <input type="text" class="form-control" id="voterId" name="voter_id" placeholder="Enter your Voter ID" required>
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
              </div>
              <button type="submit" class="btn btn-dark w-100 mt-3">Login</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
