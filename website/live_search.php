<?php
// live_search.php
require_once 'includes/config.php';

// Get search parameters
$search = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Define upload directories
$uploadDirs = [
    'handout' => 'uploads/handout/',
    'midterm' => 'uploads/midterm/',
    'final' => 'uploads/finals/',
    'software' => 'uploads/software/',
    'project' => 'uploads/project/'
];

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
    }
    return $bytes . ' bytes';
}

// Get all files based on category
$allFiles = [];

if ($category && isset($uploadDirs[$category])) {
    $allFiles = getFilesFromDirectory($uploadDirs[$category], $category);
} else {
    foreach ($uploadDirs as $cat => $dir) {
        $categoryFiles = getFilesFromDirectory($dir, $cat);
        $allFiles = array_merge($allFiles, $categoryFiles);
    }
}

// Apply search filter
if ($search) {
    $allFiles = array_filter($allFiles, function($file) use ($search) {
        $searchInFilename = strpos(strtolower($file['original_name']), $search) !== false;
        $searchInDescription = strpos(strtolower($file['description']), $search) !== false;
        $searchInCategory = strpos(strtolower($file['category']), $search) !== false;
        $searchInExtension = strpos(strtolower($file['extension']), $search) !== false;
        return $searchInFilename || $searchInDescription || $searchInCategory || $searchInExtension;
    });
}

// Generate HTML for files
$filesHtml = '';

if (empty($allFiles)) {
    $filesHtml = '
        <div class="no-results card">
            <i class="fas fa-file-alt"></i>
            <h3>No files found</h3>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
                ' . ($search || $category ? 'No files match your search criteria.' : 'No files have been uploaded yet.') . '
            </p>
            ' . ($search || $category ? '<a href="download.php" class="btn btn-primary"><i class="fas fa-redo"></i> View All Files</a>' : '') . '
        </div>
    ';
} else {
    foreach ($allFiles as $file) {
        // File icon mapping
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
            'csv' => 'file-csv',
            'js' => 'file-code',
            'php' => 'file-code',
            'html' => 'file-code',
            'css' => 'file-code',
            'py' => 'file-code',
            'java' => 'file-code',
            'cpp' => 'file-code'
        ];
        
        $fileIcon = $iconMap[$file['extension']] ?? ($file['category'] === 'software' ? 'file-code' : ($file['category'] === 'project' ? 'project-diagram' : 'file'));
        
        $filesHtml .= '
            <div class="file-item">
                <div class="file-icon">
                    <i class="fas fa-' . $fileIcon . '"></i>
                </div>
                <div class="file-info">
                    <h4>' . htmlspecialchars($file['original_name']) . '</h4>
                    <p style="color: var(--text-muted); margin: 0.5rem 0;">
                        ' . htmlspecialchars($file['description']) . '
                    </p>
                    <div class="file-meta">
                        <span><i class="fas fa-folder"></i> ' . ucfirst($file['category']) . '</span>
                        <span><i class="fas fa-user"></i> ' . htmlspecialchars($file['uploaded_by']) . '</span>
                        <span><i class="fas fa-calendar"></i> ' . date('M d, Y', strtotime($file['upload_date'])) . '</span>
                        <span><i class="fas fa-download"></i> ' . $file['downloads'] . ' downloads</span>
                        <span><i class="fas fa-hdd"></i> ' . formatFileSize($file['size']) . '</span>
                        <span><i class="fas fa-file"></i> ' . strtoupper($file['extension']) . '</span>
                    </div>
                </div>
                <a href="download.php?download=' . urlencode($file['filename']) . '&cat=' . urlencode($file['category']) . '" 
                   class="btn btn-primary download-btn"
                   data-filename="' . htmlspecialchars($file['original_name']) . '"
                   data-category="' . htmlspecialchars($file['category']) . '">
                    <i class="fas fa-download"></i> Download
                </a>
            </div>
        ';
    }
}

// Calculate stats
$stats = [
    'count' => count($allFiles),
    'category' => $category ? ucfirst($category) : '',
    'search' => $search,
    'totalSize' => formatFileSize(array_sum(array_column($allFiles, 'size'))),
    'totalDownloads' => array_sum(array_column($allFiles, 'downloads'))
];

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'filesHtml' => $filesHtml,
    'stats' => $stats
]);
?>