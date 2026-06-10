<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Online Toys Finding Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <main>
        <header class="">
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                <div class="container-fluid col-lg-12">
                        <span class="navbar-brand mb-0 h1">Online Toys Finding Application</span>
                    
<?php 
//Display Visitor Header
if (isset($_SESSION["isAdmin"])==NULL) {
?>
<div class="navbar nav">
                        <form method="post" action="../search.php" class="d-flex">
                            <input type="text" id="toySearch" name="toySearch" class="form-control" placeholder="Im searching for..." required>
                            <button type="submit" name="submit" class="btn btn-sm btn-danger me-2">Search</button>
                        </form>
</div>
            
        </div>
    </nav>
</header>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <ul class="navbar-nav nav col-12 col-md-auto mb-2 mb-md-0">
      <li><a href="../index.php" class="nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'index.php') { echo 'active'; } ?>">Home</a></li>
    </ul>
    <div class="navbar-nav col-12 col-md-auto d-flex">
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">Login</a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="login.php">User Login</a></li>
            <li><a class="dropdown-item" href="/admin/adminlogin.php">Admin Login</a></li>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'signup.php') { echo 'active'; } ?>" href="signup.php">Sign Up</a></li>
    </ul>
    </div>
  </div>
</nav>

<?php 
}
//Display User Header 
elseif(($_SESSION["isAdmin"])==0) {
?>
<div class="navbar nav">
                        <form method="post" action="../search.php" class="d-flex">
                            <input type="text" id="toySearch" name="toySearch" class="form-control" placeholder="Im searching for..." required>
                            <button type="submit" name="submit" class="btn btn-sm btn-danger me-2">Search</button>
                        </form>
      </div>
                    
                </div>
            </nav>
        </header>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <ul class="navbar-nav nav col-12 col-md-auto mb-2 mb-md-0">
        <li><a href="home.php" class="nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'home.php') { echo 'active'; } ?>">Shop</a></li>
        <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">Categories</a>
          <ul class="dropdown-menu" >
            <li><a class="dropdown-item" href="category.php?cat=1">Dolls</a></li>
            <li><a class="dropdown-item" href="category.php?cat=2">Cars</a></li>
            <li><a class="dropdown-item" href="category.php?cat=3">Stuffed Toys</a></li>
          </ul>
        </li>
        <li><a href="request.php" class="nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'request.php') { echo 'active'; } ?>">Request Toy</a></li>
    </ul>
    <div class="navbar-nav col-12 col-md-auto d-flex">
        <ul class="navbar-nav me-auto ">
            <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"><?php echo 'Hi '.$_SESSION["name"]; ?></a>
              <ul class="dropdown-menu" >
                <li><a class="dropdown-item" href="updateprofile.php">My Profile</a></li>
                <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
              </ul>
            </li>
            <li class="nav-item"><a class="nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'cart.php') { echo 'active'; } ?>" href="cart.php">My Cart</a></li>
        </ul>
    </div>
  </div>
</nav>

<?php 
} 
//Display Admin Header
elseif(($_SESSION["isAdmin"])==1) {
?>

                </div>
            </nav>
        </header>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid col-md-auto d-flex">
    <ul class="navbar-nav nav col-12 col-md-auto mb-2 mb-md-0">
        <li><a href="/admin/admin.php" class="nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'admin.php') { echo 'active'; } ?>">Home</a></li>
        <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">Toys</a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="/admin/addtoys.php">Add Toys</a></li>
            <li><a class="dropdown-item" href="/admin/updatetoys.php">Update Toys</a></li>
            <li><a class="dropdown-item" href="/admin/deletetoys.php">Delete Toys</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">Categories</a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="/admin/addcat.php">Add Category</a></li>
            <li><a class="dropdown-item" href="/admin/updatecat.php">Update Category</a></li>
            <li><a class="dropdown-item" href="/admin/deletecat.php">Delete Category</a></li>
          </ul>
        </li>
        <li><a href="/admin/orders.php" class="nav-link ">Orders</a></li>
        <li><a href="/admin/requests.php" class="nav-link ">Requests</a></li>
        <li><a href="/admin/orders.php" class="nav-link ">Shipping Charges</a></li>
        <li><a href="/admin/stock.php" class="nav-link ">Stocks</a></li>
        <li><a href="/admin/reports.php" class="nav-link ">Reports</a></li>
        <li><a href="../logout.php" class="nav-link ">Logout</a></li>
    </ul>
  </div>
</nav>
<?php 
}
?>