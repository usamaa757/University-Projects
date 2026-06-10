<?php

include 'header.php';
include 'db.php';

$search_results = [];

if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
    $query = $conn->real_escape_string(trim($_GET['query']));

    $sql = "SELECT * FROM art_items WHERE status = 'approved' AND art_name LIKE '%$query%' OR description LIKE '%$query%' OR price LIKE '%$query%'";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $search_results[] = $row;
    }
}
?>

<div class="container mt-4 border rounded shadow p-3">


    <?php if (isset($_GET['query'])): ?>
        <h5>Search Results for: <strong><?= htmlspecialchars($_GET['query']); ?></strong></h5>

        <?php if (count($search_results) > 0): ?>
            <div class="row">
                <?php foreach ($search_results as $art): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow">
                            <img src="<?= $base_url . 'seller/' . $art['image']; ?>" class="card-img-top"
                                alt="<?= htmlspecialchars($art['art_name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($art['art_name']); ?></h5>
                                <p class="card-text"><?= htmlspecialchars(substr($art['description'], 0, 100)); ?>...</p>
                                <p class="text-muted">Price: $<?= number_format($art['price'], 2); ?></p>
                                <a href="reviews.php?art_id=<?= $art['art_id']; ?>" class="btn btn-primary btn-sm">Reviews</a>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">No artworks found matching your search.</div>
        <?php endif; ?>
    <?php endif; ?>
</div>