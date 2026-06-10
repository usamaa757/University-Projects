<?php
require_once 'includes/config.php';
require_once 'includes/db_connection.php';

// Check if user is admin
if(!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

$pageTitle = "Admin Dashboard";
$adminId = $_SESSION['user_id'];

// Get statistics
$stats = [
    'total_users' => $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'],
    'total_files' => $conn->query("SELECT COUNT(*) as count FROM files")->fetch_assoc()['count'],
    'total_downloads' => $conn->query("SELECT SUM(download_count) as count FROM files")->fetch_assoc()['count'],
    'today_uploads' => $conn->query("SELECT COUNT(*) as count FROM files WHERE DATE(upload_date) = CURDATE()")->fetch_assoc()['count'],
    'pending_feedback' => $conn->query("SELECT COUNT(*) as count FROM feedback WHERE status = 'pending'")->fetch_assoc()['count'],
    'active_today' => $conn->query("SELECT COUNT(DISTINCT user_id) as count FROM user_activity_logs WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count']
];

// Get recent activity
$recentActivity = $conn->query("
    SELECT l.*, u.username, u.full_name 
    FROM user_activity_logs l 
    JOIN users u ON l.user_id = u.id 
    ORDER BY l.created_at DESC 
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);

// Get recent files
$recentFiles = $conn->query("
    SELECT f.*, u.username 
    FROM files f 
    JOIN users u ON f.uploaded_by = u.id 
    ORDER BY f.upload_date DESC 
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);

// Handle admin file upload
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['admin_file'])) {
    $fileType = $_POST['file_type'];
    $description = $_POST['description'];
    $file = $_FILES['admin_file'];
    
    // Validate file type
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedTypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'rar', 'exe', 'msi', 'jpg', 'png', 'txt', 'csv', 'xls', 'xlsx'];
    
    if(!in_array($fileExtension, $allowedTypes)) {
        $_SESSION['admin_message'] = "File type not allowed";
        $_SESSION['message_type'] = 'error';
    } elseif($file['size'] > 100 * 1024 * 1024) { // 100MB
        $_SESSION['admin_message'] = "File size exceeds maximum limit of 100MB";
        $_SESSION['message_type'] = 'error';
    } else {
        // Create directory if it doesn't exist
        $uploadDir = "uploads/" . $fileType . "/";
        if(!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate unique filename
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file['name']);
        $filepath = $uploadDir . $filename;
        
        // Move uploaded file
        if(move_uploaded_file($file['tmp_name'], $filepath)) {
            // Save to database
            $stmt = $conn->prepare("INSERT INTO files (filename, filepath, file_type, description, uploaded_by, file_size) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssii", $file['name'], $filepath, $fileType, $description, $adminId, $file['size']);
            
            if($stmt->execute()) {
                $_SESSION['admin_message'] = "File uploaded successfully!";
                $_SESSION['message_type'] = 'success';
                
                // Log activity
                $activityStmt = $conn->prepare("INSERT INTO user_activity_logs (user_id, activity_type, activity_details) VALUES (?, 'file_upload', ?)");
                $details = "Admin uploaded file: " . $file['name'];
                $activityStmt->bind_param("is", $adminId, $details);
                $activityStmt->execute();
                
            } else {
                $_SESSION['admin_message'] = "Error saving file information to database";
                $_SESSION['message_type'] = 'error';
            }
            $stmt->close();
        } else {
            $_SESSION['admin_message'] = "Error uploading file";
            $_SESSION['message_type'] = 'error';
        }
    }
    
    header("Location: admin_dashboard.php");
    exit();
}

// Handle user management actions
if(isset($_GET['action']) && isset($_GET['user_id'])) {
    $userId = intval($_GET['user_id']);
    $action = $_GET['action'];
    
    switch($action) {
        case 'make_admin':
            $conn->query("UPDATE users SET is_admin = TRUE WHERE id = $userId");
            $_SESSION['admin_message'] = "User promoted to admin";
            $_SESSION['message_type'] = 'success';
            break;
            
        case 'remove_admin':
            // Don't allow removing yourself
            if($userId != $adminId) {
                $conn->query("UPDATE users SET is_admin = FALSE WHERE id = $userId");
                $_SESSION['admin_message'] = "Admin privileges removed";
                $_SESSION['message_type'] = 'success';
            }
            break;
            
        case 'delete_user':
            // Don't allow deleting yourself
            if($userId != $adminId) {
                $conn->query("DELETE FROM users WHERE id = $userId");
                $_SESSION['admin_message'] = "User deleted";
                $_SESSION['message_type'] = 'success';
            }
            break;
            
        case 'block_user':
            $conn->query("UPDATE users SET status = 'blocked' WHERE id = $userId");
            $_SESSION['admin_message'] = "User blocked";
            $_SESSION['message_type'] = 'warning';
            break;
            
        case 'unblock_user':
            $conn->query("UPDATE users SET status = 'active' WHERE id = $userId");
            $_SESSION['admin_message'] = "User unblocked";
            $_SESSION['message_type'] = 'success';
            break;
    }
    
    header("Location: admin_dashboard.php");
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="admin-dashboard">
    <!-- Dashboard Header -->
    <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1><i class="fas fa-crown"></i> Admin Dashboard</h1>
        <div class="admin-actions">
            <a href="user_management.php" class="btn btn-secondary"><i class="fas fa-users"></i> Manage Users</a>
            <a href="file_management.php" class="btn btn-secondary"><i class="fas fa-file-alt"></i> Manage Files</a>
            <a href="system_logs.php" class="btn btn-secondary"><i class="fas fa-history"></i> View Logs</a>
        </div>
    </div>
    
    <!-- Admin Message -->
    <?php if(isset($_SESSION['admin_message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type'] === 'success' ? 'success' : ($_SESSION['message_type'] === 'error' ? 'error' : 'warning'); ?>" style="margin-bottom: 2rem;">
            <i class="fas fa-<?php echo $_SESSION['message_type'] === 'success' ? 'check-circle' : ($_SESSION['message_type'] === 'error' ? 'exclamation-circle' : 'exclamation-triangle'); ?>"></i> 
            <?php echo $_SESSION['admin_message']; ?>
        </div>
        <?php unset($_SESSION['admin_message'], $_SESSION['message_type']); ?>
    <?php endif; ?>
    
    <!-- Statistics Cards -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        <div class="stat-card" style="background: linear-gradient(135deg, var(--primary), var(--primary-dark)); padding: 1.5rem; border-radius: 10px;">
            <h3 style="color: white; margin-bottom: 0.5rem;">Total Users</h3>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 style="color: white; font-size: 2.5rem;"><?php echo $stats['total_users']; ?></h2>
                <i class="fas fa-users" style="font-size: 2rem; color: rgba(255,255,255,0.7);"></i>
            </div>
        </div>
        
        <div class="stat-card" style="background: linear-gradient(135deg, var(--secondary), #0da271); padding: 1.5rem; border-radius: 10px;">
            <h3 style="color: white; margin-bottom: 0.5rem;">Total Files</h3>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 style="color: white; font-size: 2.5rem;"><?php echo $stats['total_files']; ?></h2>
                <i class="fas fa-file-alt" style="font-size: 2rem; color: rgba(255,255,255,0.7);"></i>
            </div>
        </div>
        
        <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b, #d97706); padding: 1.5rem; border-radius: 10px;">
            <h3 style="color: white; margin-bottom: 0.5rem;">Total Downloads</h3>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 style="color: white; font-size: 2.5rem;"><?php echo $stats['total_downloads'] ?: 0; ?></h2>
                <i class="fas fa-download" style="font-size: 2rem; color: rgba(255,255,255,0.7);"></i>
            </div>
        </div>
        
        <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); padding: 1.5rem; border-radius: 10px;">
            <h3 style="color: white; margin-bottom: 0.5rem;">Active Today</h3>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 style="color: white; font-size: 2.5rem;"><?php echo $stats['active_today']; ?></h2>
                <i class="fas fa-chart-line" style="font-size: 2rem; color: rgba(255,255,255,0.7);"></i>
            </div>
        </div>
    </div>
    
    <!-- Admin Quick Actions -->
    <div class="card" style="margin-bottom: 2rem;">
        <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 1rem; margin-top: 1rem;">
            <button onclick="showUploadForm()" class="btn btn-primary">
                <i class="fas fa-upload"></i> Upload File
            </button>
            <a href="user_management.php" class="btn btn-secondary">
                <i class="fas fa-user-plus"></i> Add New User
            </a>
            <a href="system_settings.php" class="btn btn-secondary">
                <i class="fas fa-cog"></i> System Settings
            </a>
            <a href="reports.php" class="btn btn-secondary">
                <i class="fas fa-chart-bar"></i> Generate Reports
            </a>
        </div>
    </div>
    
    <!-- Admin File Upload Form (Hidden by default) -->
    <div id="uploadForm" style="display: none; margin-bottom: 2rem;">
        <div class="card">
            <h3><i class="fas fa-upload"></i> Admin File Upload</h3>
            <form method="POST" action="admin_dashboard.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Category</label>
                    <select name="file_type" class="form-control" required>
                        <option value="">Select Category</option>
                        <option value="handout">Handouts</option>
                        <option value="midterm">Mid Terms</option>
                        <option value="final">Final Terms</option>
                        <option value="software">Software</option>
                        <option value="project">Projects</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Choose File</label>
                    <input type="file" name="admin_file" class="form-control" required>
                    <small style="color: var(--text-muted);">Max size: 100MB. Allowed types: PDF, DOC, DOCX, PPT, PPTX, ZIP, RAR, EXE, MSI, JPG, PNG, TXT</small>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" placeholder="Describe the file content..." required rows="3"></textarea>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload File
                    </button>
                    <button type="button" onclick="hideUploadForm()" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Recent Files Section -->
    <div class="card" style="margin-bottom: 2rem;">
        <h3><i class="fas fa-history"></i> Recently Uploaded Files</h3>
        <div class="file-list" style="margin-top: 1rem;">
            <?php if(empty($recentFiles)): ?>
                <p style="text-align: center; color: var(--text-muted); padding: 2rem;">No files uploaded yet.</p>
            <?php else: ?>
                <?php foreach($recentFiles as $file): ?>
                <div class="file-item">
                    <div class="file-icon">
                        <i class="fas fa-file"></i>
                    </div>
                    <div class="file-info">
                        <h4><?php echo htmlspecialchars($file['filename']); ?></h4>
                        <div class="file-meta">
                            <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($file['username']); ?></span>
                            <span><i class="fas fa-folder"></i> <?php echo strtoupper($file['file_type']); ?></span>
                            <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($file['upload_date'])); ?></span>
                        </div>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="download.php?id=<?php echo $file['id']; ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-download"></i>
                        </a>
                        <a href="edit_file.php?id=<?php echo $file['id']; ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="delete_file.php?id=<?php echo $file['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this file?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div style="text-align: center; margin-top: 1rem;">
            <a href="file_management.php" class="btn btn-secondary">View All Files</a>
        </div>
    </div>
    
    <!-- Recent Activity Section -->
    <div class="card">
        <h3><i class="fas fa-stream"></i> Recent Activity</h3>
        <div class="activity-list" style="margin-top: 1rem; max-height: 300px; overflow-y: auto;">
            <?php if(empty($recentActivity)): ?>
                <p style="text-align: center; color: var(--text-muted); padding: 2rem;">No recent activity.</p>
            <?php else: ?>
                <?php foreach($recentActivity as $activity): ?>
                <div class="activity-item" style="padding: 1rem; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <strong><?php echo htmlspecialchars($activity['username']); ?></strong>
                        <span style="color: var(--text-muted); font-size: 0.9rem;">
                            <?php echo date('h:i A', strtotime($activity['created_at'])); ?>
                        </span>
                    </div>
                    <p style="color: var(--text-muted); margin: 0;">
                        <?php echo htmlspecialchars($activity['activity_details']); ?>
                    </p>
                    <small style="color: var(--text-muted);">
                        <i class="fas fa-<?php 
                            $icons = [
                                'login' => 'sign-in-alt',
                                'logout' => 'sign-out-alt',
                                'file_upload' => 'upload',
                                'file_download' => 'download',
                                'profile_update' => 'user-edit',
                                'feedback' => 'comment'
                            ];
                            echo $icons[$activity['activity_type']] ?? 'circle';
                        ?>"></i> 
                        <?php echo ucfirst(str_replace('_', ' ', $activity['activity_type'])); ?>
                    </small>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div style="text-align: center; margin-top: 1rem;">
            <a href="system_logs.php" class="btn btn-secondary">View All Activity</a>
        </div>
    </div>
</div>

<script>
function showUploadForm() {
    document.getElementById('uploadForm').style.display = 'block';
    window.scrollTo({top: document.getElementById('uploadForm').offsetTop - 100, behavior: 'smooth'});
}

function hideUploadForm() {
    document.getElementById('uploadForm').style.display = 'none';
}
</script>

<?php include 'includes/footer.php'; ?>