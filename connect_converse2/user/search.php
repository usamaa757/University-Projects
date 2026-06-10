<?php
include 'header.php';
include '../db.php';
$where = "WHERE 1";



if (!empty($_GET['keyword'])) {
    $keyword = $conn->real_escape_string($_GET['keyword']);
    $where .= " AND (d.title LIKE '%$keyword%' OR d.content LIKE '%$keyword%' OR u.name LIKE '%$keyword%')";
}

if (!empty($_GET['category_id'])) {
    $category_id = (int)$_GET['category_id'];
    $where .= " AND d.category_id = $category_id";
}
if (!empty($_GET['tag_id'])) {
    $tag_id = (int)$_GET['tag_id'];
    $where .= " AND g.tag_id = $tag_id";
}
if (!empty($_GET['date'])) {
    $date = $conn->real_escape_string($_GET['date']);
    $where .= " AND DATE(d.created_at) = '$date'";
}

$query = "
    SELECT d.topic_id, d.title, d.created_at, u.name, u.user_id, c.category_name, t.tag_name, g.tag_id
    FROM discussion_topics d
    JOIN users u ON d.user_id = u.user_id
    JOIN topic_tags g ON d.topic_id = g.topic_id
    LEFT JOIN tags t ON g.tag_id = t.tag_id
    LEFT JOIN categories c ON d.category_id = c.category_id
    $where
    ORDER BY d.created_at DESC
";




?>

<div class="container mt-5">
    <!-- Search & Filter Form -->
    <form method="GET" action="search.php" class="mb-4">
        <div class="row g-3 align-items-end shadow p-3">
            <div class="col-md-4">
                <label class="form-label">Search Keyword</label>
                <input type="text" name="keyword" class="form-control" placeholder="Search discussions, posts, users..."
                    value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
            </div>

            <div class="col-md-2">
                <label class="form-label">Filter by Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    <?php
                    $catResult = $conn->query("SELECT * FROM categories");
                    while ($cat = $catResult->fetch_assoc()) {
                        $selected = (isset($_GET['category_id']) && $_GET['category_id'] == $cat['category_id']) ? 'selected' : '';
                        echo "<option value='{$cat['category_id']}' $selected>" . htmlspecialchars($cat['category_name']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Filter by Tag</label>
                <select name="tag_id" class="form-select">
                    <option value="">All Tags</option>
                    <?php
                    $catResult = $conn->query("SELECT * FROM tags");
                    while ($cat = $catResult->fetch_assoc()) {
                        $selected = (isset($_GET['tag_id']) && $_GET['tag_id'] == $cat['tag_id']) ? 'selected' : '';
                        echo "<option value='{$cat['tag_id']}' $selected>" . htmlspecialchars($cat['tag_name']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Filter by Date</label>
                <input type="date" name="date" class="form-control"
                    value="<?= isset($_GET['date']) ? $_GET['date'] : '' ?>">
            </div>

            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-dark">Search</button>
            </div>
        </div>
    </form>




    <?php if (!empty($_GET['keyword']) || !empty($_GET['category_id']) || !empty($_GET['tag_id']) || !empty($_GET['date'])):
        $result = $conn->query($query);

        if ($result->num_rows > 0): ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">Search Results</h3>
            </div>
            <div class="row">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="card mb-3 shadow-sm border-0 topic-card hover-shadow">
                        <div class="card-body">
                            <h5 class="card-title mb-1">
                                <p class="text-dark text-decoration-none fw-semibold">
                                    <?= htmlspecialchars($row['title']) ?>
                                </p>
                            </h5>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">
                                    Posted by
                                    <a href="view_profile.php?user_id=<?= $row['user_id'] ?>"
                                        class="text-decoration-none text-primary">
                                        <?= htmlspecialchars($row['name']) ?>
                                    </a>
                                    on <?= date('d M Y, h:i A', strtotime($row['created_at'])) ?>
                                </small>
                                <a href="view_topics.php?topic_id=<?= htmlspecialchars($row['topic_id']) ?>"
                                    class="btn btn-sm btn-outline-secondary">View Thread</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">No discussions found matching your criteria.</div>
        <?php endif; ?>


    <?php endif; ?>
</div>