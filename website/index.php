<?php
require_once 'includes/config.php';
require_once 'includes/db_connection.php';
$pageTitle = "Home";
?>
<?php include 'includes/header.php'; ?>

<section class="hero">
    <h1>Academic Resource Portal</h1>
    <p>A comprehensive platform for students and faculty to share, access, and manage academic resources including handouts, exam papers, software, and project files.</p>
    
    <?php if(!isset($_SESSION['user_id'])): ?>
        <div style="margin-top: 2rem;">
            <a href="register.php" class="btn btn-primary" style="margin-right: 1rem;">Get Started</a>
            <a href="login.php" class="btn btn-secondary">Login</a>
        </div>
    <?php endif; ?>
</section>

<section class="features">
    <h2 style="text-align: center; margin-bottom: 2rem; color: var(--primary);">Features</h2>
    <div class="card-grid">
        <div class="card">
            <div class="card-icon">
                <i class="fas fa-file-upload"></i>
            </div>
            <h3>File Upload</h3>
            <p>Upload academic resources categorized into handouts, midterms, finals, software, and projects with proper metadata.</p>
        </div>
        
        <div class="card">
            <div class="card-icon">
                <i class="fas fa-file-download"></i>
            </div>
            <h3>Organized Downloads</h3>
            <p>Download resources with proper categorization, search functionality, and user ratings.</p>
        </div>
        
        <div class="card">
            <div class="card-icon">
                <i class="fas fa-comments"></i>
            </div>
            <h3>Feedback System</h3>
            <p>Provide feedback on resources and platform features to help improve the system.</p>
        </div>
        
        <div class="card">
            <div class="card-icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <h3>Portfolio Integration</h3>
            <p>Create and showcase your academic portfolio with skills, projects, and achievements.</p>
        </div>
    </div>
</section>

<section class="stats">
    <div class="card" style="text-align: center;">
        <h2>Platform Statistics</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; margin-top: 2rem;">
            <?php
            // Get statistics from database
            $stats = [
                'Users' => $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'],
                'Files' => $conn->query("SELECT COUNT(*) as count FROM files")->fetch_assoc()['count'],
                'Downloads' => $conn->query("SELECT SUM(download_count) as count FROM files")->fetch_assoc()['count'],
                'Feedback' => $conn->query("SELECT COUNT(*) as count FROM feedback")->fetch_assoc()['count']
            ];
            
            foreach($stats as $label => $count):
            ?>
            <div class="stat-item">
                <h3 style="font-size: 2.5rem; color: var(--secondary);"><?php echo $count; ?></h3>
                <p style="color: var(--text-muted);"><?php echo $label; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>