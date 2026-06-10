<?php
require_once 'includes/config.php';
require_once 'includes/db_connection.php';

$pageTitle = "Downloads";

// Get category from URL
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';

// Define upload directories
$uploadDirs = [
    'handout' => 'uploads/handouts/',
    'midterm' => 'uploads/midterms/',
    'final' => 'uploads/finals/',
    'software' => 'uploads/software/',
    'project' => 'uploads/projects/'
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
        $searchInCategory = strpos(strtolower($file['category']), $search) !== false;
        $searchInExtension = strpos(strtolower($file['extension']), $search) !== false;
        return $searchInFilename || $searchInDescription || $searchInCategory || $searchInExtension;
    });
}
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
    
    <!-- Live Search Container -->
    <div class="search-container card" style="margin: 2rem 0; padding: 1.5rem;">
        <div style="margin-bottom: 1rem;">
            <div class="search-box" style="position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                <input type="text" 
                       id="liveSearchInput" 
                       class="form-control" 
                       placeholder="Search files by name, description, category, or file type..." 
                       value="<?php echo htmlspecialchars($search); ?>"
                       style="padding-left: 3rem; width: 100%;"
                       autocomplete="off">
                <?php if($search): ?>
                <button id="clearSearch" class="btn btn-sm" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted);">
                    <i class="fas fa-times"></i>
                </button>
                <?php endif; ?>
            </div>
            <div id="searchStats" style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.5rem; display: none;">
                <i class="fas fa-spinner fa-spin"></i> Searching...
            </div>
        </div>
        
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <select name="category" id="categorySelect" class="form-control" style="width: 200px;">
                <option value="">All Categories</option>
                <option value="handout" <?php echo $category === 'handout' ? 'selected' : ''; ?>>Handouts</option>
                <option value="midterm" <?php echo $category === 'midterm' ? 'selected' : ''; ?>>Mid Terms</option>
                <option value="final" <?php echo $category === 'final' ? 'selected' : ''; ?>>Final Terms</option>
                <option value="software" <?php echo $category === 'software' ? 'selected' : ''; ?>>Software</option>
                <option value="project" <?php echo $category === 'project' ? 'selected' : ''; ?>>Projects</option>
            </select>
            <a href="download.php" class="btn btn-secondary">Reset All</a>
        </div>
    </div>
    
    <div class="categories" style="margin: 2rem 0;">
        <a href="javascript:void(0)" onclick="filterCategory('')" class="category-btn <?php echo !$category ? 'active' : ''; ?>">All Files</a>
        <a href="javascript:void(0)" onclick="filterCategory('handout')" class="category-btn <?php echo $category === 'handout' ? 'active' : ''; ?>">Handouts</a>
        <a href="javascript:void(0)" onclick="filterCategory('midterm')" class="category-btn <?php echo $category === 'midterm' ? 'active' : ''; ?>>Mid Terms</a>
        <a href="javascript:void(0)" onclick="filterCategory('final')" class="category-btn <?php echo $category === 'final' ? 'active' : ''; ?>>Final Terms</a>
        <a href="javascript:void(0)" onclick="filterCategory('software')" class="category-btn <?php echo $category === 'software' ? 'active' : ''; ?>>Software</a>
        <a href="javascript:void(0)" onclick="filterCategory('project')" class="category-btn <?php echo $category === 'project' ? 'active' : ''; ?>>Projects</a>
    </div>
    
    <!-- Files Statistics -->
    <div id="statsContainer" class="stats-bar card" style="margin: 1rem 0; padding: 1rem; background-color: rgba(255,255,255,0.03); display: none;">
        <!-- Stats will be loaded dynamically -->
    </div>
    
    <!-- Files Container -->
    <div id="filesContainer">
        <?php include 'includes/files_list.php'; ?>
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

#searchStats {
    min-height: 20px;
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
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.no-results {
    text-align: center;
    padding: 3rem;
    color: var(--text-muted);
}

.no-results i {
    font-size: 3rem;
    margin-bottom: 1rem;
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
// Global variables
let searchTimeout;
let currentCategory = '<?php echo $category; ?>';
let currentSearch = '<?php echo $search; ?>';

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const liveSearchInput = document.getElementById('liveSearchInput');
    const categorySelect = document.getElementById('categorySelect');
    const clearSearchBtn = document.getElementById('clearSearch');
    
    // Live search functionality
    if(liveSearchInput) {
        liveSearchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            currentSearch = this.value.trim();
            
            // Show loading indicator
            const searchStats = document.getElementById('searchStats');
            if(searchStats) {
                searchStats.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
                searchStats.style.display = 'block';
            }
            
            // Debounce search
            searchTimeout = setTimeout(() => {
                performSearch();
            }, 300);
        });
        
        // Clear search button
        if(clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function() {
                liveSearchInput.value = '';
                currentSearch = '';
                performSearch();
            });
        }
        
        // Enter key to search
        liveSearchInput.addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });
    }
    
    // Category change
    if(categorySelect) {
        categorySelect.addEventListener('change', function() {
            currentCategory = this.value;
            performSearch();
        });
    }
    
    // If there's already a search term, update UI
    if(currentSearch) {
        updateCategoryButtons();
        showStats();
    }
});

function filterCategory(category) {
    currentCategory = category;
    document.getElementById('categorySelect').value = category;
    updateCategoryButtons();
    performSearch();
}

function updateCategoryButtons() {
    // Update active state of category buttons
    const categoryBtns = document.querySelectorAll('.category-btn');
    categoryBtns.forEach(btn => {
        const btnCategory = btn.textContent.toLowerCase().replace(' ', '');
        if((currentCategory === '' && btnCategory === 'allfiles') || 
           (currentCategory === btnCategory.replace(' ', ''))) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
}

function performSearch() {
    const searchStats = document.getElementById('searchStats');
    
    // Update URL without page reload
    const params = new URLSearchParams();
    if(currentSearch) params.set('search', currentSearch);
    if(currentCategory) params.set('category', currentCategory);
    
    const queryString = params.toString();
    const newUrl = queryString ? `download.php?${queryString}` : 'download.php';
    window.history.replaceState(null, '', newUrl);
    
    // Show/hide clear button
    const clearSearchBtn = document.getElementById('clearSearch');
    if(clearSearchBtn) {
        clearSearchBtn.style.display = currentSearch ? 'block' : 'none';
    }
    
    // Send AJAX request for search
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `live_search.php?search=${encodeURIComponent(currentSearch)}&category=${encodeURIComponent(currentCategory)}`, true);
    
    xhr.onload = function() {
        if(xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            
            // Update files container
            document.getElementById('filesContainer').innerHTML = response.filesHtml;
            
            // Update stats
            updateStats(response.stats);
            
            // Update search stats
            if(searchStats) {
                if(currentSearch || currentCategory) {
                    searchStats.innerHTML = `<i class="fas fa-check"></i> Found ${response.stats.count} file(s)`;
                    setTimeout(() => {
                        searchStats.style.display = 'none';
                    }, 2000);
                } else {
                    searchStats.style.display = 'none';
                }
            }
        }
    };
    
    xhr.send();
}

function updateStats(stats) {
    const statsContainer = document.getElementById('statsContainer');
    
    if(stats.count > 0) {
        statsContainer.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="color: var(--text-muted);">
                    <i class="fas fa-chart-bar"></i> 
                    Showing ${stats.count} file(s)
                    ${stats.category ? 'in <span style="color: var(--primary);">' + stats.category + '</span>' : ''}
                    ${stats.search ? 'matching "<span style="color: var(--primary);">' + stats.search + '</span>"' : ''}
                </div>
                <div style="display: flex; gap: 1rem;">
                    <span style="color: var(--text-muted);">
                        <i class="fas fa-hdd"></i> 
                        Total size: ${stats.totalSize}
                    </span>
                    <span style="color: var(--text-muted);">
                        <i class="fas fa-download"></i> 
                        Total downloads: ${stats.totalDownloads}
                    </span>
                </div>
            </div>
        `;
        statsContainer.style.display = 'block';
    } else {
        statsContainer.style.display = 'none';
    }
}

function showStats() {
    const statsContainer = document.getElementById('statsContainer');
    if(statsContainer) {
        statsContainer.style.display = 'block';
    }
}

function trackDownload(filename, category) {
    // Optional: Send AJAX request to track download
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'track_download.php?file=' + encodeURIComponent(filename) + '&category=' + encodeURIComponent(category), true);
    xhr.send();
    
    // Continue with normal download
    return true;
}

// Handle download clicks on dynamically loaded content
document.addEventListener('click', function(e) {
    if(e.target.closest('.download-btn')) {
        const btn = e.target.closest('.download-btn');
        const filename = btn.getAttribute('data-filename');
        const category = btn.getAttribute('data-category');
        trackDownload(filename, category);
    }
});
</script>

<?php include 'includes/footer.php'; ?>