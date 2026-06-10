<?php
include 'header.php'; // PHP: Includes the header

// Specify the upload directory
$uploadDir = 'E://WhatsApp Documents/';

// Function to find duplicate files by size and content
function findDuplicateFilesBySizeAndContent($directory)
{
    $fileSizes = [];
    $duplicateFiles = [];

    // Recursively scan the directory and its subdirectories
    $iterator = new RecursiveDirectoryIterator($directory, RecursiveIteratorIterator::SELF_FIRST);

    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $filePath = $file->getPathname();
            $fileSize = filesize($filePath);
            $fileHash = md5_file($filePath); // Using md5 to compare content

            // Group files by size and hash
            $key = $fileSize . '_' . $fileHash;
            if (!isset($fileSizes[$key])) {
                $fileSizes[$key] = [];
            }

            $fileSizes[$key][] = $filePath;
        }
    }

    // Identify and collect duplicate files by size and hash
    foreach ($fileSizes as $key => $files) {
        if (count($files) > 1) {
            $duplicateFiles[] = $files; // Multiple files with the same size and content
        }
    }

    return $duplicateFiles;
}

// Handle file deletion for selected files
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteFiles'])) {
    $filesToDelete = $_POST['files'] ?? [];
    foreach ($filesToDelete as $filePath) {
        if (file_exists($filePath)) {
            unlink($filePath); // Delete the file
        }
    }

    // Refresh the duplicate files after deletion
    $duplicateFiles = findDuplicateFilesBySizeAndContent($uploadDir);
} elseif (isset($_POST['findDuplicates'])) {
    // Find duplicates when the button is clicked
    $duplicateFiles = findDuplicateFilesBySizeAndContent($uploadDir);
} else {
    $duplicateFiles = [];
}

// Fetch the list of files in the handouts directory
$files = array_diff(scandir($uploadDir), array('.', '..')); // Exclude '.' and '..'
?>
<br><br><br><br><br>
<div class="container-fluid">
    <form method="post">
        <div class="shadow rounded border">
            <div class="bg-primary p-2">
                <h3 class="text-center text-white mb-0">Handouts</h3>
            </div>

            <!-- Search bar -->
            <div class="form-group text-center mx-5 my-3">
                <input type="text" id="searchInput" class="form-control" placeholder="Search for files..."
                    style="border-radius: 15px; padding: 15px;">
            </div>

            <!-- Message for deletion success -->
            <div class="m-2 text-success">
                <?php if (isset($successMessage)) {
                    echo '<div class="text-success fade show" role="alert">' . htmlspecialchars($successMessage) . '</div>';
                } ?>
            </div>

            <!-- Select All and Delete Button -->
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <label class="form-check-label" for="selectAllFiles">
                        <input type="checkbox" id="selectAllFiles" class="form-check-input mr-2"> Select All
                    </label>
                    <button type="submit" name="deleteFiles" class="btn btn-danger btn-sm ml-2">Delete Selected
                        Files</button>
                </div>
                <button type="submit" name="findDuplicates" class="btn btn-info btn-sm">Find Duplicates</button>
            </div>

            <!-- Display duplicate files -->
            <?php if (!empty($duplicateFiles)): ?>
                <div class="p-3 mt-4" style="border-radius: 10px; background-color: #f8f9fa;">
                    <h4 class="text-center">Duplicate Files Found</h4>
                    <div class="row justify-content-center" id="fileList" style="max-height: 400px; overflow-y: auto;">
                        <form method="post">
                            <?php foreach ($duplicateFiles as $duplicateGroup): ?>
                                <div class="col-12">
                                    <h5>Duplicate group (<?= count($duplicateGroup) ?> files)</h5>
                                    <?php foreach ($duplicateGroup as $file): ?>
                                        <div class="form-check">
                                            <input type="checkbox" name="files[]" value="<?= htmlspecialchars($file); ?>"
                                                class="form-check-input fileCheckbox" id="file<?= md5($file); ?>">
                                            <label class="form-check-label" for="file<?= md5($file); ?>">
                                                <?= basename($file); ?> - <?= $file; ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Display the files in the handouts directory -->
            <div class="p-3" style="border-radius: 10px; background-color: #f8f9fa;">
                <h4 class="text-center">All Files in Handouts Directory</h4>
                <div class="row justify-content-center" id="fileList" style="max-height: 400px; overflow-y: auto;">
                    <?php if (!empty($files)): ?>
                        <form method="post">
                            <?php foreach ($files as $fileName):
                            ?>

                                <div class="col-md-2 mb-4">
                                    <div class="card shadow-sm border-light h-100 position-relative">
                                        <div class="card-body text-center">
                                            <a href="<?= $uploadDir . htmlspecialchars($fileName); ?>"
                                                class="card-link color-primary" download style="text-decoration: none;">
                                                <?= htmlspecialchars(basename($fileName)); ?>
                                            </a>
                                            <div class="form-check mt-2">
                                                <input type="checkbox" name="files[]"
                                                    value="<?= $uploadDir . htmlspecialchars($fileName); ?>"
                                                    class="form-check-input fileCheckbox" id="file<?= md5($fileName); ?>">
                                                <label class="form-check-label" for="file<?= md5($fileName); ?>">
                                                    Select to delete
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <button type="submit" name="deleteFiles" class="btn btn-danger btn-sm mt-3">Delete Selected
                                Files</button>
                        </form>
                    <?php else: ?>
                        <div class="col-12 text-center">
                            <p>No files found in the directory.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
    // Search bar functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const fileList = document.getElementById('fileList');
        const items = fileList.getElementsByClassName('col-md-2');

        for (let item of items) {
            const itemText = item.textContent.toLowerCase();
            item.style.display = itemText.includes(query) ? "" : "none";
        }
    });

    // Select All functionality
    document.getElementById('selectAllFiles').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.fileCheckbox');
        for (const checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    });

    document.getElementById('selectAllFilesHandouts').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.fileCheckbox');
        for (const checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    });
</script>

<style>
    .row {
        display: flex;
        flex-wrap: wrap;
        /* Ensure cards wrap in the row */
    }

    .col-md-2 {
        flex: 0 0 20%;
        /* Each card takes up 20% of the row width */
        max-width: 20%;
        /* Prevents columns from growing too large */
        margin-bottom: 20px;
    }

    .card {
        min-height: 200px;
        /* Example fixed height for cards */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .card {
        flex-grow: 1;
        margin: 5px;
        /* Add some spacing around each card */
    }

    .card {
        transition: transform 0.2s;
    }

    .card:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .container {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .alert {
        background-color: #f8f9fa;
        color: #333;
    }

    .d-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-sm {
        padding: 5px 15px;
        font-size: 0.875rem;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-sm:hover {
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
    }

    .form-check-input {
        margin-left: 10px;
    }

    .form-check-label {
        margin-left: 5px;
    }

    .form-check {
        margin-top: 5px;
    }

    .fileCheckbox {
        margin-left: 10px;
    }
</style>