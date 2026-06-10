<?php
require_once 'includes/config.php';
require_once 'includes/db_connection.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$pageTitle = "Dashboard";
$userId = $_SESSION['user_id'];

// Get file counts by category for quick access
$categories = ['handout', 'midterm', 'final', 'software', 'project'];
$categoryData = [];

foreach($categories as $category) {
    $count = $conn->query("SELECT COUNT(*) as count FROM files WHERE file_type = '$category'")->fetch_assoc()['count'];
    $categoryData[$category] = [
        'count' => $count,
        'name' => ucwords(str_replace('_', ' ', $category))
    ];
}

// Get recently added files (for all users)
$recentFiles = $conn->query("
    SELECT f.*, u.username 
    FROM files f 
    JOIN users u ON f.uploaded_by = u.id 
    ORDER BY f.upload_date DESC 
    LIMIT 8
")->fetch_all(MYSQLI_ASSOC);

// Get popular downloads
$popularFiles = $conn->query("
    SELECT f.*, u.username 
    FROM files f 
    JOIN users u ON f.uploaded_by = u.id 
    ORDER BY f.download_count DESC 
    LIMIT 6
")->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<div class="member-dashboard">
    <!-- Welcome Banner -->
    <div class="welcome-banner card" style="background: linear-gradient(135deg, var(--darker-bg), var(--dark-bg)); border: none; margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
            <div style="flex: 1;">
                <h1 style="color: var(--primary); margin-bottom: 0.5rem;">
                    Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?: $_SESSION['username']); ?>!
                </h1>
                <p style="color: var(--text-muted); font-size: 1.1rem;">
                    Access academic resources including handouts, exam papers, software, and projects
                </p>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 3rem; color: var(--secondary); margin-bottom: 0.5rem;">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <a href="download.php" class="btn btn-primary">
                    <i class="fas fa-search"></i> Browse All Files
                </a>
            </div>
        </div>
    </div>
    
    <!-- Quick Access Categories -->
    <div class="card" style="margin-bottom: 2rem;">
        <h3><i class="fas fa-bolt"></i> Quick Access Categories</h3>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Select a category to explore available resources</p>
        
        <div class="category-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
            <?php foreach($categoryData as $key => $category): ?>
            <a href="download.php?category=<?php echo $key; ?>" 
               class="category-card" 
               style="display: block; background-color: var(--card-bg); border: 1px solid var(--border); border-radius: 10px; padding: 1.5rem; text-decoration: none; transition: all 0.3s; text-align: center;">
                
                <div style="font-size: 2.5rem; color: var(--primary); margin-bottom: 1rem;">
                    <?php switch($key): 
                        case 'handout': ?>
                            <i class="fas fa-book"></i>
                        <?php break; ?>
                        <?php case 'midterm': ?>
                            <i class="fas fa-file-signature"></i>
                        <?php break; ?>
                        <?php case 'final': ?>
                            <i class="fas fa-graduation-cap"></i>
                        <?php break; ?>
                        <?php case 'software': ?>
                            <i class="fas fa-laptop-code"></i>
                        <?php break; ?>
                        <?php case 'project': ?>
                            <i class="fas fa-project-diagram"></i>
                        <?php break; ?>
                    <?php endswitch; ?>
                </div>
                
                <h4 style="color: var(--text); margin-bottom: 0.5rem;"><?php echo $category['name']; ?></h4>
                <p style="color: var(--text-muted); margin: 0; font-size: 0.9rem;">
                    <?php echo $category['count']; ?> files available
                </p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Recently Added Files -->
    <div class="card" style="margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3><i class="fas fa-clock"></i> Recently Added</h3>
            <a href="download.php?sort=newest" class="btn btn-secondary btn-sm">View All</a>
        </div>
        
        <?php if(empty($recentFiles)): ?>
            <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                <i class="fas fa-file-alt" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                <p>No files uploaded yet</p>
            </div>
        <?php else: ?>
            <div class="file-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
                <?php foreach($recentFiles as $file): ?>
                <div class="file-card" style="background-color: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: 8px; padding: 1rem; transition: all 0.3s;">
                    <div style="display: flex; align-items: flex-start; gap: 1rem;">
                        <div style="width: 50px; height: 50px; background-color: var(--primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
                            <i class="fas fa-file"></i>
                        </div>
                        <div style="flex: 1;">
                            <h4 style="color: var(--text); margin-bottom: 0.25rem; font-size: 1rem;">
                                <?php echo htmlspecialchars(substr($file['filename'], 0, 30)); ?>
                                <?php if(strlen($file['filename']) > 30): ?>...<?php endif; ?>
                            </h4>
                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.5rem;">
                                <span style="background-color: var(--primary); color: white; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem;">
                                    <?php echo strtoupper($file['file_type']); ?>
                                </span>
                                <span style="color: var(--text-muted); font-size: 0.8rem;">
                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($file['username']); ?>
                                </span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: var(--text-muted); font-size: 0.8rem;">
                                    <?php echo date('M d', strtotime($file['upload_date'])); ?>
                                </span>
                                <a href="download.php?id=<?php echo $file['id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Popular Downloads -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3><i class="fas fa-fire"></i> Most Popular Downloads</h3>
            <a href="download.php?sort=popular" class="btn btn-secondary btn-sm">View All</a>
        </div>
        
        <?php if(empty($popularFiles)): ?>
            <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                <i class="fas fa-chart-line" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                <p>No downloads yet</p>
            </div>
        <?php else: ?>
            <div class="popular-list" style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border);">
                            <th style="padding: 1rem; text-align: left; color: var(--text-muted);">File Name</th>
                            <th style="padding: 1rem; text-align: left; color: var(--text-muted);">Category</th>
                            <th style="padding: 1rem; text-align: left; color: var(--text-muted);">Uploaded By</th>
                            <th style="padding: 1rem; text-align: left; color: var(--text-muted;">Downloads</th>
                            <th style="padding: 1rem; text-align: left; color: var(--text-muted);">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($popularFiles as $file): ?>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 1rem; color: var(--text);">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-file" style="color: var(--primary);"></i>
                                    <?php echo htmlspecialchars(substr($file['filename'], 0, 25)); ?>
                                    <?php if(strlen($file['filename']) > 25): ?>...<?php endif; ?>
                                </div>
                            </td>
                            <td style="padding: 1rem; color: var(--text-muted);">
                                <span style="background-color: rgba(59, 130, 246, 0.1); color: var(--primary); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem;">
                                    <?php echo strtoupper($file['file_type']); ?>
                                </span>
                            </td>
                            <td style="padding: 1rem; color: var(--text-muted);">
                                <?php echo htmlspecialchars($file['username']); ?>
                            </td>
                            <td style="padding: 1rem; color: var(--text-muted); text-align: center;">
                                <span style="color: var(--secondary); font-weight: bold;">
                                    <i class="fas fa-download"></i> <?php echo $file['download_count']; ?>
                                </span>
                            </td>
                            <td style="padding: 1rem;">
                                <a href="download.php?id=<?php echo $file['id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Quick Search -->
    <div class="card" style="margin-top: 2rem; background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(16, 185, 129, 0.1));">
        <div style="text-align: center; padding: 2rem;">
            <h3 style="color: var(--primary); margin-bottom: 1rem;">
                <i class="fas fa-search"></i> Can't find what you're looking for?
            </h3>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
                Use our advanced search to find specific files or resources
            </p>
            <form action="download.php" method="GET" style="max-width: 500px; margin: 0 auto;">
                <div style="display: flex; gap: 0.5rem;">
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Search files by name or keyword..."
                           style="flex: 1;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.category-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary) !important;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.file-card:hover {
    transform: translateY(-3px);
    border-color: var(--primary) !important;
}

.popular-list::-webkit-scrollbar {
    height: 6px;
}

.popular-list::-webkit-scrollbar-track {
    background: var(--border);
    border-radius: 3px;
}

.popular-list::-webkit-scrollbar-thumb {
    background: var(--primary);
    border-radius: 3px;
}

@media (max-width: 768px) {
    .category-grid {
        grid-template-columns: 1fr !important;
    }
    
    .file-grid {
        grid-template-columns: 1fr !important;
    }
    
    .popular-list {
        font-size: 0.9rem;
    }
    
    .popular-list table {
        min-width: 600px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>