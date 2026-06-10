<?php
include '../db.php';
include 'header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid property ID.";
    exit;
}

$property_id = (int)$_GET['id'];

// Fetch property
$stmt = $conn->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->bind_param("i", $property_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Property not found.";
    exit;
}

$property = $result->fetch_assoc();

// Fetch images
$img_stmt = $conn->prepare("SELECT image_path FROM property_images WHERE property_id = ?");
$img_stmt->bind_param("i", $property_id);
$img_stmt->execute();
$img_result = $img_stmt->get_result();

$images = [];
while ($row = $img_result->fetch_assoc()) {
    $images[] = $row['image_path'];
}


?>



<main class="property-detail">
    <section class="hero">
        <div class="image-gallery">
            <img id="gallery-image" src="../agent/<?= htmlspecialchars($images[0] ?? 'placeholder.jpg') ?>"
                alt="Property Image">
            <div class="gallery-controls">
                <button type="button" class="gallery-btn" id="prev-image" aria-label="Previous image">
                    <span class="material-icons">chevron_left</span>
                </button>
                <button type="button" class="gallery-btn" id="next-image" aria-label="Next image">
                    <span class="material-icons">chevron_right</span>
                </button>
            </div>
        </div>

        <div class="property-info">
            <h1><?= htmlspecialchars($property['title']) ?></h1>
            <div class="property-price">$ <?= number_format($property['price']) ?></div>

            <div class="property-location">
                <span class="material-icons location-icon" aria-hidden="true">location_on</span>
                <?= htmlspecialchars($property['city']) ?>
            </div>

            <p class="property-description"><?= nl2br(htmlspecialchars($property['description'])) ?></p>

            <?php
            $listingType = strtolower(trim($property['listing_type']));
            $buttonLabel = ($listingType === 'rent') ? 'Book Now' : 'Buy Now';
            $targetPage = ($listingType === 'rent') ? 'rent.php' : 'buy.php';
            ?>
            <div class="text-center">

                <a href="<?= $targetPage ?>?id=<?= $property['id'] ?>"
                    class="btn"><?= htmlspecialchars($buttonLabel) ?></a>
            </div>
        </div>
    </section>

    <?php
    // Fetch agent details
    $agent_stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'agent'");
    $agent_stmt->bind_param("i", $property['agent_id']);
    $agent_stmt->execute();
    $agent_result = $agent_stmt->get_result();
    $agent = $agent_result->fetch_assoc();
    // Fetch average rating for agent
    $agent_id = $agent['id'];
    $rating_result = $conn->query("SELECT AVG(rating) AS avg_rating FROM agent_feedback WHERE agent_id = $agent_id");
    $avg_rating = round($rating_result->fetch_assoc()['avg_rating'] ?? 0, 1); // rounded to 1 decimal
    ?>

    <?php if ($agent): ?>
    <aside class="agent-contact" aria-labelledby="agent-heading">
        <div class="section-header">

            <h2 id="agent-heading">Agent Info</h2>
        </div>
        <div class="agent-card">
            <img src="../admin/uploads/agents/<?= htmlspecialchars($agent['photo'] ?? 'default-agent.png') ?>"
                alt="Agent Photo" class="agent-photo">
            <div class="agent-details">
                <h3><?= htmlspecialchars($agent['fullname']) ?></h3>
                <p><strong>Email:</strong> <a
                        href="mailto:<?= htmlspecialchars($agent['email']) ?>"><?= htmlspecialchars($agent['email']) ?></a>
                </p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($agent['phone']) ?></p>
                <br>
                <a href="send_message.php?agent_id=<?= $agent['id']; ?>" class="btn">Chat With Agent</a>
                <a href="feedback.php?agent_id=<?= $agent['id']; ?>" class="btn">Feedback</a>
            </div>
        </div>
        <?php if ($avg_rating > 0): ?>
        <p><strong>Rating:</strong>
            <?php
                    $full_stars = floor($avg_rating);
                    $half_star = ($avg_rating - $full_stars) >= 0.5;
                    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);

                    for ($i = 0; $i < $full_stars; $i++) echo '<i class="fas fa-star" style="color:#FFD700;"></i>';
                    if ($half_star) echo '<i class="fas fa-star-half-alt" style="color:#FFD700;"></i>';
                    for ($i = 0; $i < $empty_stars; $i++) echo '<i class="far fa-star" style="color:#ccc;"></i>';

                    echo " ({$avg_rating})";
                    ?>
        </p>
        <?php else: ?>
        <p><strong>Rating:</strong> No ratings yet.</p>
        <?php endif; ?>

    </aside>
    <?php endif; ?>

    <?php
    $feature_stmt = $conn->prepare("SELECT feature FROM property_features WHERE property_id = ?");
    $feature_stmt->bind_param("i", $property_id);
    $feature_stmt->execute();
    $feature_result = $feature_stmt->get_result();

    $features = [];
    while ($row = $feature_result->fetch_assoc()) {
        $features[] = $row['feature'];
    }
    ?>

    <?php if (!empty($features)): ?>
    <div class="features-list" aria-label="Property features">
        <?php foreach ($features as $feature): ?>
        <div class="feature-item">
            <span class="material-icons" aria-hidden="true">check_circle</span>
            <?= htmlspecialchars($feature) ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</main>

<script>
const images = <?= json_encode($images) ?>;
let currentIndex = 0;
const galleryImage = document.getElementById('gallery-image');

document.getElementById('prev-image').onclick = () => {
    currentIndex = (currentIndex - 1 + images.length) % images.length;
    galleryImage.src = "../agent/" + images[currentIndex];
};

document.getElementById('next-image').onclick = () => {
    currentIndex = (currentIndex + 1) % images.length;
    galleryImage.src = "../agent/" + images[currentIndex];
};
</script>
<?php include '../footer.php'; ?>

</body>

</html>