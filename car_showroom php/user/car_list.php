<?php
include "../db.php";
include 'header.php';

$selectedBrand = $_GET['brand'] ?? '';
$selectedModel = $_GET['model'] ?? '';

// Fetch all unique car brands
$brandsResult = $conn->query("SELECT DISTINCT brand FROM cars ORDER BY brand ASC");

// Fetch models based on selected brand
$modelsResult = null;
if (!empty($selectedBrand)) {
    $stmt = $conn->prepare("SELECT DISTINCT model FROM cars WHERE brand = ?");
    $stmt->bind_param("s", $selectedBrand);
    $stmt->execute();
    $modelsResult = $stmt->get_result();
}
?>


<div class="container my-5 shadow rounded border card p-3">
    <h3 class="text-center mb-4">Browse Cars</h3>

    <form method="GET" class="row g-3 mb-4">
        <div class="d-flex justify-content-center flex-wrap gap-3">

            <div class="col-md-4">
                <select name="brand" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Select Brand --</option>
                    <?php
                    // Reset pointer for brand dropdown
                    $brandsResult->data_seek(0);
                    while ($row = $brandsResult->fetch_assoc()) {
                        $selected = ($row['brand'] === $selectedBrand) ? 'selected' : '';
                        echo "<option value=\"" . htmlspecialchars($row['brand']) . "\" $selected>" . htmlspecialchars($row['brand']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-4">
                <select name="model" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Select Model --</option>
                    <?php
                    if ($modelsResult) {
                        while ($row = $modelsResult->fetch_assoc()) {
                            $selected = ($row['model'] === $selectedModel) ? 'selected' : '';
                            echo "<option value=\"" . htmlspecialchars($row['model']) . "\" $selected>" . htmlspecialchars($row['model']) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
    </form>
    <?php
    // Prepare filtered query
    $query = "SELECT * FROM cars WHERE status = 'available'";
    $params = [];
    $types = "";

    if (!empty($selectedBrand)) {
        $query .= " AND brand = ?";
        $types .= "s";
        $params[] = $selectedBrand;
    }

    if (!empty($selectedModel)) {
        $query .= " AND model = ?";
        $types .= "s";
        $params[] = $selectedModel;
    }

    $query .= " ORDER BY brand, model ASC";

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $carsResult = $stmt->get_result();

    if ((!empty($selectedBrand) || !empty($selectedModel)) && $carsResult->num_rows > 0):
    ?>
    <h3 class="mb-4">Available Cars</h3>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php while ($car = $carsResult->fetch_assoc()): ?>
        <div class="col">

            <div class="card shadow-sm">
                <img src="<?= htmlspecialchars($car['image']) ?>" class="card-img-top"
                    alt="<?= htmlspecialchars($car['model']) ?>" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title">
                        <?= htmlspecialchars($car['brand']) . " - " . htmlspecialchars($car['model']) ?></h5>
                    <p class="card-text"><strong>Price:</strong> Rs. <?= number_format($car['price']) ?></p>
                    <p class="card-text"><?= htmlspecialchars($car['features']) ?></p>
                    <?php
                            if ($car['status'] == 'available') { ?>
                    <form method="get" action="car_purchase.php">
                        <input type="hidden" name="car_id" value="<?= $car['car_id'] ?>">
                        <div class="text-center">

                            <button type="submit" name="add_to_cart" class="btn btn-sm">Add to Cart</button>
                        </div>
                    </form>
                    <?php
                            } else {
                                echo 'Not available';
                            } ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php elseif (!empty($selectedBrand) || !empty($selectedModel)): ?>
    <p class="mt-4 text-muted">No cars found for the selected criteria.</p>
    <?php endif; ?>

    <?php
    // Show grouped by brand only when no filter applied
    if (empty($selectedBrand) && empty($selectedModel)):
        $brandsResult->data_seek(0);
        while ($brandRow = $brandsResult->fetch_assoc()):
            $brand = $brandRow['brand'];
            echo "<h3>" . htmlspecialchars($brand) . "</h3>";

            $modelQuery = $conn->prepare("SELECT * FROM cars WHERE brand = ? AND status = 'available'");
            $modelQuery->bind_param("s", $brand);
            $modelQuery->execute();
            $modelsResult = $modelQuery->get_result();

            echo "<div class='row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4'>";
            while ($car = $modelsResult->fetch_assoc()):
    ?>
    <div class="col">
        <div class="card shadow-sm">
            <img src="<?= htmlspecialchars($car['image']) ?>" class="card-img-top"
                alt="<?= htmlspecialchars($car['model']) ?>" style="height: 200px; object-fit: cover;">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($car['model']) ?></h5>
                <p class="card-text"><strong>Price:</strong> Rs. <?= number_format($car['price']) ?></p>
                <p class="card-text"><?= htmlspecialchars($car['features']) ?></p>
                <form method="get" action="car_purchase.php">
                    <input type="hidden" name="car_id" value="<?= $car['car_id'] ?>">
                    <div class="text-center">

                        <button type="submit" name="add_to_cart" class="btn btn-sm">Add to Cart</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endwhile;
            echo "</div>";
        endwhile;
    endif;
    ?>
</div>