<?php

include 'header.php';
include '../db.php';

$search_results = [];

if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
    $query = $conn->real_escape_string(trim($_GET['query']));

    $sql = "SELECT * FROM art_items WHERE art_name LIKE '%$query%' OR description LIKE '%$query%'";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $search_results[] = $row;
    }
}
?>

<div class="container mt-4">
    <h3 class="mb-3">Search Artwork</h3>

    <form method="GET" action="">
        <div class="input-group mb-4">
            <input type="text" name="query" class="form-control" placeholder="Enter artwork name or keyword..."
                value="<?= htmlspecialchars($_GET['query'] ?? '') ?>">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

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
                                <a href="art_details.php?art_id=<?php echo $art['art_id']; ?>" class="btn btn-sm
                        btn-outline-success">View
                                    Details</a>
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