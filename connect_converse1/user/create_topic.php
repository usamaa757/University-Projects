<?php
include 'header.php';
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in first.'); window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle topic creation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = intval($_POST['category_id']);
    $tags = array_map('trim', explode(',', $_POST['tags']));

    // Insert topic
    $stmt = $conn->prepare("INSERT INTO discussion_topics (user_id, title, content, category_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $user_id, $title, $content, $category_id);

    if ($stmt->execute()) {
        $topic_id = $stmt->insert_id;

        // Insert tags
        foreach ($tags as $tag) {
            if ($tag !== '') {
                // Insert tag if not exists
                $conn->query("INSERT IGNORE INTO tags (tag_name) VALUES ('$tag')");

                // Get tag_id
                $tag_result = $conn->query("SELECT tag_id FROM tags WHERE tag_name = '$tag'");
                if ($tag_row = $tag_result->fetch_assoc()) {
                    $tag_id = $tag_row['tag_id'];

                    // Link tag to topic
                    $conn->query("INSERT INTO topic_tags (topic_id, tag_id) VALUES ($topic_id, $tag_id)");
                }
            }
        }

        echo "<script>alert('Topic created successfully!'); window.location.href='topic_list.php';</script>";
    } else {
        echo "<script>alert('Error creating topic');</script>";
    }
    $stmt->close();
}

// Fetch categories
$categories = $conn->query("SELECT category_id, category_name FROM categories");
?>

<!-- EasyMDE CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
<script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-dark text-white text-center">
                    <h4>Create New Topic Discussion</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Topic Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Select Category --</option>
                                <?php while ($cat = $categories->fetch_assoc()): ?>
                                    <option value="<?= $cat['category_id'] ?>">
                                        <?= htmlspecialchars($cat['category_name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tags (comma-separated)</label>
                            <input type="text" name="tags" class="form-control"
                                placeholder="e.g. university, exam, Ramadan">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea id="markdown-editor" name="content"></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success px-4">Create Topic</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize EasyMDE
    new EasyMDE({
        element: document.getElementById("markdown-editor"),
        spellChecker: false,
        toolbar: [
            "bold", "italic", "heading", "|",
            "quote", "unordered-list", "ordered-list", "|",
            "link", "image", "code", "|",
            "preview", "side-by-side", "fullscreen", "|",
            "guide"
        ],
    });
</script>