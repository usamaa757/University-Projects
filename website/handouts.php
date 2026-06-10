<?php
include 'header.php'; // PHP: Includes the header

// PHP: Specify directory for uploads
$uploadDir = 'admin/handouts/';

// PHP: Fetch files from the local directory
$files = array_diff(scandir($uploadDir), array('.', '..')); // Exclude '.' and '..'
?>
<br><br><br><br><br>
<div class="container-fluid">
    <div class="shadow rounded border">
        <div class="bg-primary p-2">
            <h3 class="text-center text-white mb-0">Handouts</h3>
        </div>

        <div class="form-group text-center mx-5 my-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Search for files..."
                style="border-radius: 15px; padding: 15px;">
        </div>

        <div class="p-3" style="border-radius: 10px; background-color: #f8f9fa;">
            <div class="row justify-content-center" id="fileList" style="max-height: 400px; overflow-y: auto;">
                <?php
                if (!empty($files)) {
                    foreach ($files as $fileName) {
                        $filePath = htmlspecialchars($uploadDir . $fileName);
                        ?>
                <div class="col-md-2 mb-4">
                    <div class="card shadow-sm border-light h-100">
                        <div class="card-body text-center">
                            <a href="<?php echo $filePath; ?>" class="card-link color-primary" download
                                style="text-decoration: none;">
                                <?php echo htmlspecialchars(basename($fileName)); ?>
                            </a>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo "<div class='col-12 text-center'><p>No files found in the directory.</p></div>";
                }
                ?>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('searchInput').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    const fileList = document.getElementById('fileList');
    const items = fileList.getElementsByClassName('col-md-2');

    for (let item of items) {
        const itemText = item.textContent.toLowerCase();
        item.style.display = itemText.includes(query) ? "" : "none";
    }
});
</script>

<!-- HTML and CSS: Styling for cards and container -->
<style>
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
</style>