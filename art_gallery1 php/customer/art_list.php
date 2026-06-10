<?php
include "../db.php";
include 'header.php';

$searchQuery = $_GET['search'] ?? '';

$query = "SELECT arts.*, users.name, users.address FROM arts 
          INNER JOIN users ON arts.artist_id = users.user_id
          WHERE arts.status = 'approved'";

$types = '';
$params = [];

if (!empty($searchQuery)) {
    $query .= " AND (arts.art_name LIKE ? OR arts.description LIKE ? OR arts.price LIKE ? OR users.name LIKE ?)";
    $types .= 'ssss';
    $params = array_fill(0, 4, '%' . $searchQuery . '%');
}

$query .= " ORDER BY arts.art_name ASC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$artsResult = $stmt->get_result();
?>

<div class="container my-5 shadow rounded border card p-3">
    <h3 class="text-center mb-4">Browse Art Gallery</h3>

    <form method="GET" class="mb-4">
        <div class="d-flex justify-content-center flex-wrap gap-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control"
                    placeholder="Search by art name, description, price, or artist name"
                    value="<?= htmlspecialchars($searchQuery) ?>">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>



    <?php if (!empty($searchQuery)): ?>
    <h3 class="mb-4">Search Results for "<?= htmlspecialchars($searchQuery) ?>"</h3>
    <?php endif; ?>

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
                    <p class="card-text"><strong>Location:</strong> <?= htmlspecialchars($art['address']) ?></p>
                    <form method="get" action="cart.php">
                        <input type="hidden" name="art_id" value="<?= $art['art_id'] ?>">
                        <div class="text-center">
                            <button type="submit" name="add_to_cart" class="btn btn-sm">Add to Cart</button>
                            <div class="mt-3">
                                <h5>Share this artwork:</h5>
                                <?php
                                    $art_url = "http://localhost/art_gallery/art_list.php?id=" . $art['art_id'];
                                    ?>
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($art_url) ?>"
                                    target="_blank" class="btn btn-sm">Facebook</a>
                                <a href="https://twitter.com/intent/tweet?url=<?= urlencode($art_url) ?>&text=Check out this amazing art!"
                                    target="_blank" class="btn btn-sm text-white">Twitter</a>
                                <a href="https://api.whatsapp.com/send?text=Check%20this%20artwork:%20<?= urlencode($art_url) ?>"
                                    target="_blank" class="btn btn-sm">WhatsApp</a>
                                <a href="mailto:?subject=Check out this artwork&body=Take a look at this: <?= urlencode($art_url) ?>"
                                    class="btn btn-sm">Email</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>