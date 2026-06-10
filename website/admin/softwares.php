<?php
include 'header.php'; // Include header

// Local directory for uploads
$uploadDir = 'softwares';
$localFiles = array_diff(scandir($uploadDir), array('.', '..')); // Exclude '.' and '..'

// Cloud files array
$cloudFiles = [
    ['name' => 'MS Office 2007', 'url' => 'https://drive.usercontent.google.com/download?id=1nRgSj-b-MQXqvDRIpMcx48QncElUEEd5&export=download&authuser=0&confirm=t&uuid=fe4a917a-095f-43c9-bd0f-09d6e4b575a2&at=AENtkXZDUHiJY36s0tBBKg1twebd%3A1732567909249'],
    ['name' => 'MS Office 2010', 'url' => 'https://drive.google.com/file/d/1l66zmP1xPKwhpjfb9qiIUa7sYM0PEY6O/view?usp=drive_link'],
    ['name' => 'MS Office 2019', 'url' => 'https://drive.google.com/file/d/1l66zmP1xPKwhpjfb9qiIUa7sYM0PEY6O/view?usp=drive_link'],
    ['name' => 'Free Download Manager FDM', 'url' => 'https://drive.google.com/file/d/1p_JAHS22GTnjjpKspjjnR6Fx76fbxVPq/view?usp=drive_link'],
    ['name' => 'Inpage 2014', 'url' => 'https://drive.google.com/file/d/1nSGacXqx2S0-Bl4i1p3yrh8T3YH5Hygb/view?usp=drive_link'],
    ['name' => 'Internet Download Manager IDM', 'url' => 'https://drive.google.com/file/d/1mCxUuBlPD66O114RXmQN692fddf76JTa/view?usp=drive_link'],
    ['name' => 'IO_Bit Uninstaller', 'url' => 'https://drive.google.com/file/d/1pTD8tdJ9kgiwHjOEgg8vr82BjIZEV_kC/view?usp=drive_link'],
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
                                <a href="<?php echo $fileUrl; ?>" class="card-link color-primary" download
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