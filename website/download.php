<?php
require_once 'includes/config.php';
require_once 'includes/db_connection.php';

$pageTitle = "Downloads";

// Handle download BEFORE any output
if (isset($_GET['download']) && isset($_GET['cat'])) {
    $fileToDownload = $_GET['download'];
    $categoryForDownload = $_GET['cat'];
    
    $uploadDirs = [
        'handout' => 'uploads/handout/',
        'midterm' => 'uploads/midterm/',
        'finalterm' => 'uploads/finalterm/',
        'software' => 'uploads/software/',
        'project' => 'uploads/project/'
    ];
    
    if ($categoryForDownload && isset($uploadDirs[$categoryForDownload])) {
        $filePath = $uploadDirs[$categoryForDownload] . $fileToDownload;
        
        if (file_exists($filePath)) {
            // Increment download count in metadata file
            $fileInfo = pathinfo($fileToDownload);
            $metaFile = $uploadDirs[$categoryForDownload] . $fileInfo['filename'] . '.meta';
            
            if (file_exists($metaFile)) {
                $metaContent = file_get_contents($metaFile);
                $metaData = json_decode($metaContent, true);
                $metaData['download_count'] = ($metaData['download_count'] ?? 0) + 1;
                file_put_contents($metaFile, json_encode($metaData, JSON_PRETTY_PRINT));
            }
            
            // Get original filename from metadata
            $originalName = $fileToDownload;
            if (file_exists($metaFile)) {
                $metaContent = file_get_contents($metaFile);
                $metaData = json_decode($metaContent, true);
                if (isset($metaData['original_name'])) {
                    $originalName = $metaData['original_name'];
                }
            }
            
            // Force download with proper headers
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($originalName) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            
            // Clear output buffer
            if (ob_get_length()) ob_clean();
            flush();
            
            readfile($filePath);
            exit;
        } else {
            $_SESSION['error'] = "File not found or has been removed.";
            header("Location: download.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Invalid download request.";
        header("Location: download.php");
        exit();
    }
}

// Get category from URL (for display purposes)
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';

// Define upload directories
$uploadDirs = [
    'handout' => 'uploads/handout/',
    'midterm' => 'uploads/midterm/',
    'finalterm' => 'uploads/finalterm/',
    'software' => 'uploads/software/',
    'project' => 'uploads/project/'
];

// Function to get files from directory
function getFilesFromDirectory($directory, $category = '') {
    $files = [];
    
    if (!is_dir($directory)) {
        return $files;
    }
    
    $fileList = scandir($directory);
    
    foreach ($fileList as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $filePath = $directory . $file;
        $fileInfo = pathinfo($filePath);
        
        // Skip .meta files
        if (isset($fileInfo['extension']) && $fileInfo['extension'] === 'meta') {
            continue;
        }
        
        // Check if metadata file exists
        $metaFile = $directory . $fileInfo['filename'] . '.meta';
        $metadata = [];
        
        if (file_exists($metaFile)) {
            $metaContent = file_get_contents($metaFile);
            $metadata = json_decode($metaContent, true);
        }
        
        $files[] = [
            'filename' => $file,
            'original_name' => $metadata['original_name'] ?? $file,
            'filepath' => $filePath,
            'category' => $category,
            'description' => $metadata['description'] ?? $fileInfo['filename'],
            'size' => $metadata['file_size'] ?? filesize($filePath),
            'modified' => filemtime($filePath),
            'extension' => isset($fileInfo['extension']) ? strtolower($fileInfo['extension']) : '',
            'downloads' => $metadata['download_count'] ?? 0,
            'uploaded_by' => $metadata['uploaded_by'] ?? 'Admin',
            'upload_date' => $metadata['upload_date'] ?? date('Y-m-d H:i:s', filemtime($filePath))
        ];
    }
    
    // Sort by modification date (newest first)
    usort($files, function($a, $b) {
        return $b['modified'] <=> $a['modified'];
    });
    
    return $files;
}

// Function to format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        return $bytes . ' bytes';
    } elseif ($bytes == 1) {
        return '1 byte';
    } else {
        return '0 bytes';
    }
}

// Get all files based on category
$allFiles = [];

if ($category && isset($uploadDirs[$category])) {
    // Get files from specific category directory
    $allFiles = getFilesFromDirectory($uploadDirs[$category], $category);
} else {
    // Get files from all directories
    foreach ($uploadDirs as $cat => $dir) {
        $categoryFiles = getFilesFromDirectory($dir, $cat);
        $allFiles = array_merge($allFiles, $categoryFiles);
    }
}

// Apply search filter if provided
if ($search) {
    $allFiles = array_filter($allFiles, function($file) use ($search) {
        $searchInFilename = strpos(strtolower($file['original_name']), $search) !== false;
        $searchInDescription = strpos(strtolower($file['description']), $search) !== false;
        return $searchInFilename || $searchInDescription;
    });
}

// Calculate stats
$totalSize = array_sum(array_column($allFiles, 'size'));
$totalDownloads = array_sum(array_column($allFiles, 'downloads'));
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <h1><i class="fas fa-download"></i> Download Files</h1>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-error" style="margin: 1rem 0;">
            <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success" style="margin: 1rem 0;">
            <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <!-- Simple Live Search -->
    <div class="search-container card" style="margin: 2rem 0; padding: 1.5rem;">
        <div style="margin-bottom: 1rem;">
            <div class="search-box" style="position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                <input type="text" 
                       id="liveSearchInput" 
                       class="form-control" 
                       placeholder="Search files by name or description..." 
                       value="<?php echo htmlspecialchars($search); ?>"
                       style="padding-left: 3rem; width: 100%;"
                       autocomplete="off">
                <?php if($search): ?>
                <button id="clearSearch" class="btn btn-sm" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted);">
                    <i class="fas fa-times"></i>
                </button>
                <?php endif; ?>
            </div>
        </div>
        
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <select id="categorySelect" class="form-control" style="width: 200px;">
                <option value="">All Categories</option>
                <option value="handout" <?php echo $category === 'handout' ? 'selected' : ''; ?>>Handouts</option>
                <option value="midterm" <?php echo $category === 'midterm' ? 'selected' : ''; ?>>Mid Terms</option>
                <option value="finalterm" <?php echo $category === 'finalterm' ? 'selected' : ''; ?>>Final Terms</option>
                <option value="software" <?php echo $category === 'software' ? 'selected' : ''; ?>>Software</option>
                <option value="project" <?php echo $category === 'project' ? 'selected' : ''; ?>>Projects</option>
            </select>
            <a href="download.php" class="btn btn-secondary">Reset All</a>
        </div>
    </div>
    
    <!-- Category Buttons -->
    <div class="categories" style="margin: 2rem 0;">
        <a href="download.php" class="category-btn <?php echo !$category ? 'active' : ''; ?>">All Files</a>
        <a href="download.php?category=handout" class="category-btn <?php echo $category === 'handout' ? 'active' : ''; ?>">Handouts</a>
        <a href="download.php?category=midterm" class="category-btn <?php echo $category === 'midterm' ? 'active' : ''; ?>">Mid Terms</a>
        <a href="download.php?category=finalterm" class="category-btn <?php echo $category === 'finalterm' ? 'active' : ''; ?>">Final Terms</a>
        <a href="download.php?category=software" class="category-btn <?php echo $category === 'software' ? 'active' : ''; ?>">Software</a>
        <a href="download.php?category=project" class="category-btn <?php echo $category === 'project' ? 'active' : ''; ?>">Projects</a>
    </div>
    
    <!-- Statistics -->
    <?php if(!empty($allFiles)): ?>
    <div class="stats-bar card" style="margin: 1rem 0; padding: 1rem; background-color: rgba(255,255,255,0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="color: var(--text-muted);">
                <i class="fas fa-chart-bar"></i> 
                Showing <?php echo count($allFiles); ?> file(s)
                <?php if($category): ?>
                    in <span style="color: var(--primary);"><?php echo ucfirst($category); ?></span>
                <?php endif; ?>
                <?php if($search): ?>
                    matching "<span style="color: var(--primary);"><?php echo htmlspecialchars($search); ?></span>"
                <?php endif; ?>
            </div>
            <div style="display: flex; gap: 1rem;">
                <span style="color: var(--text-muted);">
                    <i class="fas fa-hdd"></i> 
                    Total size: <?php echo formatFileSize($totalSize); ?>
                </span>
                <span style="color: var(--text-muted);">
                    <i class="fas fa-download"></i> 
                    Total downloads: <?php echo $totalDownloads; ?>
                </span>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Files List -->
    <div class="file-list">
        <?php if(empty($allFiles)): ?>
            <div class="card" style="text-align: center; padding: 3rem;">
                <i class="fas fa-file-alt" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                <h3>No files found</h3>
                <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
                    <?php if($search || $category): ?>
                        No files match your search criteria.
                    <?php else: ?>
                        No files have been uploaded yet.
                    <?php endif; ?>
                </p>
                <?php if($search || $category): ?>
                    <a href="download.php" class="btn btn-primary">
                        <i class="fas fa-redo"></i> View All Files
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach($allFiles as $file): ?>
            <div class="file-item">
                <div class="file-icon">
                    <i class="fas fa-<?php 
                        $extension = $file['extension'];
                        $iconMap = [
                            'pdf' => 'file-pdf',
                            'doc' => 'file-word',
                            'docx' => 'file-word',
                            'ppt' => 'file-powerpoint',
                            'pptx' => 'file-powerpoint',
                            'xls' => 'file-excel',
                            'xlsx' => 'file-excel',
                            'zip' => 'file-archive',
                            'rar' => 'file-archive',
                            '7z' => 'file-archive',
                            'exe' => 'file-code',
                            'msi' => 'file-code',
                            'jpg' => 'file-image',
                            'jpeg' => 'file-image',
                            'png' => 'file-image',
                            'gif' => 'file-image',
                            'txt' => 'file-alt',
                            'csv' => 'file-csv'
                        ];
                        echo $iconMap[$extension] ?? ($file['category'] === 'software' ? 'file-code' : ($file['category'] === 'project' ? 'project-diagram' : 'file'));
                    ?>"></i>
                </div>
                <div class="file-info">
                    <h4><?php echo htmlspecialchars($file['original_name']); ?></h4>
                    <p style="color: var(--text-muted); margin: 0.5rem 0;">
                        <?php echo htmlspecialchars($file['description']); ?>
                    </p>
                    <div class="file-meta">
                        <span><i class="fas fa-folder"></i> <?php echo ucfirst($file['category']); ?></span>
                        <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($file['uploaded_by']); ?></span>
                        <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($file['upload_date'])); ?></span>
                        <span><i class="fas fa-download"></i> <?php echo $file['downloads']; ?> downloads</span>
                        <span><i class="fas fa-hdd"></i> <?php echo formatFileSize($file['size']); ?></span>
                        <span><i class="fas fa-file"></i> <?php echo strtoupper($file['extension']); ?></span>
                    </div>
                </div>
                <a href="download.php?download=<?php echo urlencode($file['filename']); ?>&cat=<?php echo urlencode($file['category']); ?>" 
                   class="btn btn-primary">
                    <i class="fas fa-download"></i> Download
                </a>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.search-box {
    position: relative;
}

#liveSearchInput:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}

.category-btn {
    cursor: pointer;
}

#clearSearch {
    cursor: pointer;
}

#clearSearch:hover {
    color: var(--danger);
}

.file-meta {
    display: flex;
    gap: 1rem;
    color: var(--text-muted);
    font-size: 0.9rem;
    flex-wrap: wrap;
}

.file-meta span {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.stats-bar {
    border-left: 4px solid var(--primary);
}

@media (max-width: 768px) {
    .file-item {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .file-meta {
        justify-content: center;
    }
    
    .search-container .form-control {
        width: 100% !important;
        margin-bottom: 0.5rem;
    }
    
    .search-container > div {
        flex-direction: column;
    }
    
    .categories {
        overflow-x: auto;
        white-space: nowrap;
        padding-bottom: 0.5rem;
    }
}
</style>

<script>
// Simple Live Search without AJAX - just filter existing content
document.addEventListener('DOMContentLoaded', function() {
    const liveSearchInput = document.getElementById('liveSearchInput');
    const categorySelect = document.getElementById('categorySelect');
    const clearSearchBtn = document.getElementById('clearSearch');
    const fileItems = document.querySelectorAll('.file-item');
    
    // Search functionality
    if(liveSearchInput) {
        liveSearchInput.addEventListener('input', function() {
            filterFiles();
        });
        
        // Clear search button
        if(clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function() {
                liveSearchInput.value = '';
                filterFiles();
                // Redirect to clear all filters
                window.location.href = 'download.php';
            });
        }
        
        // Enter key to submit search
        liveSearchInput.addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                e.preventDefault();
                submitSearch();
            }
        });
    }
    
    // Category change redirect
    if(categorySelect) {
        categorySelect.addEventListener('change', function() {
            submitSearch();
        });
    }
    
    function filterFiles() {
        const searchTerm = liveSearchInput.value.toLowerCase().trim();
        
        fileItems.forEach(item => {
            const fileName = item.querySelector('h4').textContent.toLowerCase();
            const fileDescription = item.querySelector('p').textContent.toLowerCase();
            
            if(searchTerm === '' || fileName.includes(searchTerm) || fileDescription.includes(searchTerm)) {
                item.style.display = 'grid';
            } else {
                item.style.display = 'none';
            }
        });
        
        // Update visible count
        updateVisibleCount();
    }
    
    function updateVisibleCount() {
        const visibleItems = document.querySelectorAll('.file-item[style="display: grid"]').length;
        const totalItems = fileItems.length;
        
        // Update stats if you want to show live count
        // You could add a counter element to show "Showing X of Y files"
    }
    
    function submitSearch() {
        const searchTerm = liveSearchInput.value.trim();
        const category = categorySelect.value;
        
        let url = 'download.php?';
        const params = [];
        
        if(searchTerm) {
            params.push('search=' + encodeURIComponent(searchTerm));
        }
        
        if(category) {
            params.push('category=' + encodeURIComponent(category));
        }
        
        if(params.length > 0) {
            url += params.join('&');
        } else {
            url = 'download.php';
        }
        
        window.location.href = url;
    }
    
    // Initialize filter if there's a search term
    if(liveSearchInput.value) {
        filterFiles();
    }
});
</script>

<?php include 'includes/footer.php'; ?>