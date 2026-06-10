<?php
include("header.php");
?>
<!-- Registration Form -->
<div class="container mt-3">

  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-6">
      <div class="border shadow bg-white rounded">
        <h3 class="text-center bg-dark text-white p-3">Register to ECO Trade Hub</h3>
        <form action="registration_process.php" method="POST" class="mt-2">
          <?php if (isset($_GET['msg'])) : ?>
            <div class="alert alert-info">
              <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
          <?php endif; ?>
          <div class="p-4">
            <div class="form-group">
              <label for="full_name">Full Name:</label>
              <input type="text" class="form-control" id="full_name" name="full_name" required>
            </div>
            <div class="form-group">
              <label for="email">Email:</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
              <label for="contact_number">Contact Number:</label>
              <input type="text" class="form-control" id="contact_number" maxlength="11" name="contact_number" required>
            </div>
            <div class="form-group">
              <label for="address">Address:</label>
              <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
            </div>
            <div class="form-group">
              <label for="role">Role:</label>
              <select class="form-control" id="role" name="role" required>
                <option value="buyer">Buyer</option>
                <option value="seller">Seller</option>
              </select>
            </div>
            <div class="form-group">
              <label for="password">Password:</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
              <label for="confirm_password">Confirm Password:</label>
              <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-primary">Register</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>