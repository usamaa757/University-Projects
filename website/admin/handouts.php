<?php
include 'header.php'; // PHP: Includes the header

// Specify the upload directory
$uploadDir = 'C:Xampp/htdocs/website/admin/handouts/';

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

// Handle file deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteFiles'])) {
    $filesToDelete = $_POST['files'] ?? [];
    if (empty($filesToDelete)) {
        $errorMessage = "No files selected for deletion.";
    } else {
        foreach ($filesToDelete as $filePath) {
            if (file_exists($filePath)) {
                unlink($filePath); // Delete the file
            }
        }

        // Refresh the duplicate files after deletion
        $duplicateFiles = findDuplicateFilesBySizeAndContent($uploadDir);
        $successMessage = "Selected file(s) have been deleted successfully.";
    }
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

            <!-- Message for deletion success or no files selected -->
            <div class="m-2">
                <?php if (isset($successMessage)) {
                    echo '<div class="text text-success">' . htmlspecialchars($successMessage) . '</div>';
                } ?>
                <?php if (isset($errorMessage)) {
                    echo '<div class="text text-danger">' . htmlspecialchars($errorMessage) . '</div>';
                } ?>
            </div>

            <!-- Select All Checkbox and Delete Button -->
            <div class="d-flex justify-content-between align-items-center mx-5 my-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="selectAllFiles" onclick="toggleSelectAll()">
                    <label class="form-check-label" for="selectAllFiles">Select All Files</label>
                </div>
                <button type="submit" name="deleteFiles" class="btn btn-danger btn-sm">Delete Selected Files</button>
            </div>

            <!-- Button to find duplicates -->
            <div class="text-center mt-3">
                <button type="submit" name="findDuplicates" class="btn btn-info btn-sm">Find Duplicates</button>
            </div>

            <!-- Display duplicate files -->
            <?php if (!empty($duplicateFiles)): ?>
                <div class="p-3 mt-4" style="border-radius: 10px; background-color: #f8f9fa;">
                    <h4 class="text-center">Duplicate Files Found</h4>
                    <div class="row justify-content-center" id="fileList" style="max-height: 400px; overflow-y: auto;">
                        <form method="post">
                            <?php foreach ($duplicateFiles as $index => $duplicateGroup): ?>
                                <div class="col-12">
                                    <h5>Duplicate group (<?= count($duplicateGroup) ?> files)</h5>
                                    <!-- Select All Checkbox for Group -->
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="selectAllGroup<?= $index ?>"
                                            onclick="toggleGroupSelection(<?= $index ?>)">
                                        <label class="form-check-label" for="selectAllGroup<?= $index ?>">Select All</label>
                                    </div>

                                    <?php foreach ($duplicateGroup as $file): ?>
                                        <div class="form-check">
                                            <input type="checkbox" name="files[]" value="<?= htmlspecialchars($file); ?>"
                                                class="form-check-input group<?= $index ?>" id="file<?= md5($file); ?>">
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

            <!-- Display all files -->
            <div class="p-3" style="border-radius: 10px; background-color: #f8f9fa;">
                <div class="row justify-content-center" id="fileList" style="max-height: 400px; overflow-y: auto;">
                    <?php if (!empty($files)): ?>
                        <?php foreach ($files as $fileName): ?>
                            <div class="col-md-2 mb-4 file-card">
                                <div class="card shadow-sm border-light h-100 position-relative">
                                    <div class="form-check position-absolute" style="top: 0px; left: 5px; z-index: 1;">
                                        <input type="checkbox" name="files[]"
                                            value="<?= htmlspecialchars($uploadDir . $fileName); ?>" class="form-check-input"
                                            id="fileCheckbox<?= md5($fileName); ?>">
                                        <label class="form-check-label" for="fileCheckbox<?= md5($fileName); ?>"></label>
                                    </div>
                                    <div class="card-body text-center mx-2">
                                        <a href="<?= $uploadDir . htmlspecialchars($fileName); ?>"
                                            class="card-link color-primary file-name" download style="text-decoration: none;">
                                            <?= htmlspecialchars(basename($fileName)); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

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
    // Toggle selection of all files
    function toggleSelectAll() {
        const selectAllCheckbox = document.getElementById('selectAllFiles');
        const checkboxes = document.querySelectorAll('input[name="files[]"]');

        // Check or uncheck all checkboxes
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = selectAllCheckbox.checked;
        });
    }

    // Toggle selection of all files in a duplicate group
    function toggleGroupSelection(groupIndex) {
        const checkboxes = document.querySelectorAll('.group' + groupIndex);
        const selectAllCheckbox = document.getElementById('selectAllGroup' + groupIndex);

        // Check or uncheck all checkboxes in the group based on the Select All checkbox
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = selectAllCheckbox.checked;
        });
    }
    // Live Search Functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        const query = this.value.toLowerCase(); // Get the search query in lowercase
        const fileItems = document.querySelectorAll('.file-card'); // Select all the file cards

        fileItems.forEach(function(item) {
            const fileName = item.querySelector('.file-name').textContent
                .toLowerCase(); // Get file name from each card

            // Show or hide the item based on whether it matches the search query
            if (fileName.includes(query)) {
                item.style.display = ''; // Show item if matches
            } else {
                item.style.display = 'none'; // Hide item if doesn't match
            }
        });
    });
</script>

<style>
    .file-card {
        display: block;
        /* Make sure they are visible by default */
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
        padding: 5px 10px;
        font-size: 0.875rem;
    }
</style>