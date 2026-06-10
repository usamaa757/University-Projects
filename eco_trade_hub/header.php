<?php include "config.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ECO Trade Hub</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .navbar-custom {
      background-color: rgb(80, 166, 252); /* Custom background color */
    }
    .navbar-custom .navbar-brand, .navbar-custom .nav-link {
      color: #fff; /* Custom text color */
    }
    .navbar-custom .nav-link:hover {
      color: #ddd; /* Lighter text color on hover */
    }
    .form-container {
      background-color: #f7f7f7; /* Light background color for the form container */
      border: 1px solid #ddd; /* Light border */
      padding: 20px; /* Padding inside the form container */
      border-radius: 10px; /* Rounded corners */
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Shadow effect */
    }
  </style>
</head>
<body>
  <!-- Header with Navigation Links -->
  <header>
    <nav class="navbar navbar-expand-lg navbar-custom">
      <a class="navbar-brand" href="<?php echo BASE_PATH; ?>/#"><h3>ECO Trade Hub</h3></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_PATH; ?>/index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_PATH; ?>/about.php">About Us</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_PATH; ?>/registration.php">Register</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_PATH; ?>/login.php">Login</a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
