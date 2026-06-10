<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Homepage</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Admin Panel</h1>
        <nav>
            <ul>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="welcome-section">
            <h2>Welcome, Admin!</h2>
            <p>This is the homepage of the admin panel.</p>
        </section>
        <section class="quick-links-section">
            <h2>Quick Links</h2>
            <ul>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </section>
        <section class="overview-section">
            <h2>System Overview</h2>
            <p>Add any overview or statistics here.</p>
        </section>
        <section class="recent-activities-section">
            <h2>Recent Activities</h2>
            <ul>
                <li>User JohnDoe updated their profile</li>
                <li>New user JaneDoe registered</li>
                <li>Admin approved a post</li>
            </ul>
        </section>
    </main>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Your Company Name</p>
    </footer>
</body>
</html>
