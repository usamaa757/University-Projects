<?php
include "../db.php";
include 'header.php';

$searchQuery = $_GET['search'] ?? '';
?>

<div class="container my-5 shadow rounded border card p-3">
    <h3 class="text-center mb-4">Browse Art Gallery</h3>

    <!-- 🔍 Search Form -->
    <form method="GET" class="mb-4">
        <div class="d-flex justify-content-center flex-wrap gap-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control"
                    placeholder="Search by art name, description, price, or artist name"
                    value="<?= htmlspecialchars($searchQuery) ?>">
            </div>
            <button type="submit" class="btn">Search</button>
        </div>
    </form>

    <?php
    // Prepare the base query with an inner join to fetch artist_name from users
    $query = "SELECT arts.*, users.name FROM arts 
              INNER JOIN users ON arts.artist_id = users.user_id
              WHERE arts.status = 'approved'";

    $types = '';
    $params = [];

    // Check if there's any search query
    if (!empty($searchQuery)) {
        $query .= " AND (arts.art_name LIKE ? OR arts.description LIKE ? OR arts.price LIKE ? OR users.name LIKE ?)";
        $types .= 'ssss';
        $params[] = '%' . $searchQuery . '%';
        $params[] = '%' . $searchQuery . '%';
        $params[] = '%' . $searchQuery . '%';
        $params[] = '%' . $searchQuery . '%';
    }

    $query .= " ORDER BY arts.art_name ASC";

    // Prepare and execute the query
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $artsResult = $stmt->get_result();

    if (!empty($searchQuery)):
        if ($artsResult->num_rows > 0): ?>
    <h3 class="mb-4">Search Results for "<?= htmlspecialchars($searchQuery) ?>"</h3>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php while ($art = $artsResult->fetch_assoc()): ?>
        <div class="col">
            <div class="card shadow-sm">
                <img src="<?= htmlspecialchars($art['image']) ?>" class="card-img-top"
                    alt="<?= htmlspecialchars($art['art_name']) ?>" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($art['art_name']) ?></h5>
                    <p class="card-text"><strong>Price:</strong> Rs. <?= number_format($art['price']) ?></p>
                    <p class="card-text"><?= htmlspecialchars($art['description']) ?></p>
                    <p class="card-text"><strong>Artist:</strong> <?= htmlspecialchars($art['name']) ?></p>
                    <a href="reviews.php?art_id=<?= $art['art_id']; ?>" class="btn btn-sm">Review</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <p class="text-muted">No artworks found matching your search.</p>
    <?php endif;
    else:
        // Display all grouped by art_name
        $artNamesResult = $conn->query("SELECT DISTINCT art_name FROM arts WHERE status = 'approved' ORDER BY art_name ASC");
        while ($artRow = $artNamesResult->fetch_assoc()):
            $artName = $artRow['art_name'];
            echo "<h3>" . htmlspecialchars($artName) . "</h3>";

            $artQuery = $conn->prepare("SELECT arts.*, users.name FROM arts 
                                        INNER JOIN users ON arts.artist_id = users.user_id 
                                        WHERE arts.art_name = ? AND arts.status = 'approved'");
            $artQuery->bind_param("s", $artName);
            $artQuery->execute();
            $groupedResult = $artQuery->get_result();

            echo "<div class='row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4'>";
            while ($art = $groupedResult->fetch_assoc()):
            ?>
    <div class="col">
        <div class="card shadow-sm">
            <img src="<?= htmlspecialchars($art['image']) ?>" class="card-img-top"
                alt="<?= htmlspecialchars($art['art_name']) ?>" style="height: 200px; object-fit: cover;">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($art['art_name']) ?></h5>
                <p class="card-text"><strong>Price:</strong> Rs. <?= number_format($art['price']) ?></p>
                <p class="card-text"><?= htmlspecialchars($art['description']) ?></p>
                <p class="card-text"><strong>Artist:</strong> <?= htmlspecialchars($art['name']) ?></p>
                <a href="reviews.php?art_id=<?= $art['art_id']; ?>" class="btn btn-sm">Review</a>
            </div>
        </div>
    </div>
    <?php endwhile;
            echo "</div>";
        endwhile;
    endif;
    ?>
</div>