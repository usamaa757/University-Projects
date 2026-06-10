<?php
// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
$userType = $isLoggedIn ? $_SESSION['user_type'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' : ''; ?>Academic Resource Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <!-- Logo on far left -->
                <a href="index.php" class="logo">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Academic</span>Portal
                </a>
                
                <!-- Desktop Navigation on far right -->
                <div class="nav-wrapper">
                        <?php if($isLoggedIn): ?>

                    <ul class="nav-links">
                        <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                        <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li><a href="upload.php"><i class="fas fa-upload"></i> Upload</a></li>
                        <li><a href="download.php"><i class="fas fa-download"></i> Downloads</a></li>
                        <li><a href="feedback.php"><i class="fas fa-comments"></i> Feedback</a></li>
                        <li><a href="portfolio.php"><i class="fas fa-user-tie"></i> My Portfolio</a></li>
                    </ul>
                            <?php endif; ?>
                    
                    <div class="auth-buttons">
                        <?php if($isLoggedIn): ?>
                            <span class="welcome-text">Welcome, <?php echo htmlspecialchars($username); ?></span>
                            <?php if($userType === 'admin'): ?>
                                <a href="admin.php" class="btn btn-primary">Admin</a>
                            <?php endif; ?>
                            <a href="logout.php" class="btn btn-secondary">Logout</a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-secondary">Login</a>
                            <a href="register.php" class="btn btn-primary">Register</a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Mobile menu toggle -->
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </nav>
        
        </div>
    </header>
    
    <main class="container">