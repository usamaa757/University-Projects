<?php
include 'header.php'; // Include header

// Local directory for uploads
$uploadDir = 'admin/softwares/';
$localFiles = array_diff(scandir($uploadDir), array('.', '..')); // Exclude '.' and '..'

// Cloud files array
$cloudFiles = [
    ['name' => 'MS Office 2007', 'url' => 'https://drive.google.com/file/d/1nRgSj-b-MQXqvDRIpMcx48QncElUEEd5/view?usp=drive_link'],
    ['name' => 'MS Office 2010', 'url' => 'https://drive.google.com/file/d/1l66zmP1xPKwhpjfb9qiIUa7sYM0PEY6O/view?usp=drive_link'],
    ['name' => 'MS Office 2019', 'url' => 'https://drive.google.com/file/d/1fokK0Rdbk9cFK7dw9TPHzAW8vfT6Px-V/view?usp=drive_link'],
    ['name' => 'Free Download Manager FDM', 'url' => 'https://drive.google.com/file/d/1p_JAHS22GTnjjpKspjjnR6Fx76fbxVPq/view?usp=drive_link'],
    ['name' => 'Inpage 2014', 'url' => 'https://drive.google.com/file/d/1nSGacXqx2S0-Bl4i1p3yrh8T3YH5Hygb/view?usp=drive_link'],
    ['name' => 'Internet Download Manager IDM', 'url' => 'https://drive.google.com/file/d/1mCxUuBlPD66O114RXmQN692fddf76JTa/view?usp=drive_link'],
    ['name' => 'IO_Bit Uninstaller', 'url' => 'https://drive.google.com/file/d/1pTD8tdJ9kgiwHjOEgg8vr82BjIZEV_kC/view?usp=drive_link'],
    ['name' => 'MathType 7', 'url' => 'https://drive.google.com/file/d/1_ZJlzF2y-Gh6to0xCxMvN_ZeBt412Dmy/view?usp=drive_link'],
    ['name' => 'MathType 6.9', 'url' => 'https://drive.google.com/file/d/1lvbVzvcF5hUwOWY-p2_WAEUn6pZWELM6/view?usp=drive_link'],
    ['name' => 'Win + Office Activator', 'url' => 'https://drive.google.com/file/d/1lgCJit6rmZyInAJ4_cbkv1PUiBFGpznl/view?usp=drive_link'],
    ['name' => 'Win USB Setup', 'url' => 'https://drive.google.com/file/d/1TZqHNxDefWImry6L3keXSZUOFW9IOyny/view?usp=drive_link'],
    ['name' => 'Xampp', 'url' => 'https://drive.google.com/file/d/1514rx6PFSjj8HfWRc3eLiL6wv42UTyzW/view?usp=drive_link'],
    ['name' => 'Skype', 'url' => 'https://drive.google.com/file/d/1QQktBtY9A0d5rK-YAyfQfFbK-Td6493J/view?usp=drive_link'],
];
?>
<br><br><br><br><br>
<div class="container-fluid">
    <div class="shadow rounded border">
        <div class="bg-primary p-2">
            <h3 class="text-center text-white mb-0">Softwares</h3>
        </div>

        <div class="form-group text-center mx-5 my-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Search for files..."
                style="border-radius: 15px; padding: 15px;">
        </div>

        <div class="p-3" style="border-radius: 10px; background-color: #f8f9fa;">
            <div class="row justify-content-center" id="fileList" style="max-height: 400px; overflow-y: auto;">
                <?php
                // Display local files
                if (!empty($localFiles)) {
                    foreach ($localFiles as $fileName) {
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
                }

                // Display cloud files
                foreach ($cloudFiles as $file) {
                    $fileName = htmlspecialchars($file['name']);
                    $fileUrl = htmlspecialchars($file['url']);
                    ?>
                    <div class="col-md-2 mb-4">
                        <div class="card shadow-sm border-light h-100">
                            <div class="card-body text-center">
                                <a href="<?php echo $fileUrl; ?>" class="card-link color-primary" download target="_blank"
                                    style="text-decoration: none;">
                                    <?php echo $fileName; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php
                }

                // Message if no files found
                if (empty($localFiles) && empty($cloudFiles)) {
                    echo "<div class='col-12 text-center'><p>No files found in the directory or cloud.</p></div>";
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