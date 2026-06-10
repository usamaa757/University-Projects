<?php
include 'header.php'; // Keep your user header
include '../db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = "";
$error = "";

// Handle topic creation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = intval($_POST['category_id']);
    $tags = array_map('trim', explode(',', $_POST['tags']));

    if ($title && $content && $category_id) {
        $stmt = $conn->prepare("INSERT INTO discussion_topics (user_id, title, content, category_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issi", $user_id, $title, $content, $category_id);

        if ($stmt->execute()) {
            $topic_id = $stmt->insert_id;

            // Handle tags
            foreach ($tags as $tag) {
                if (!empty($tag)) {
                    $safe_tag = $conn->real_escape_string($tag);
                    $conn->query("INSERT IGNORE INTO tags (tag_name) VALUES ('$safe_tag')");

                    $tag_result = $conn->query("SELECT tag_id FROM tags WHERE tag_name = '$safe_tag'");
                    if ($tag_row = $tag_result->fetch_assoc()) {
                        $tag_id = $tag_row['tag_id'];
                        $conn->query("INSERT INTO topic_tags (topic_id, tag_id) VALUES ($topic_id, $tag_id)");
                    }
                }
            }

            $msg = "Topic created successfully.";
        } else {
            $error = "Error creating topic.";
        }

        $stmt->close();
    } else {
        $error = "All fields are required.";
    }
}

// Fetch categories
$categories = $conn->query("SELECT category_id, category_name FROM categories");
?>

<!-- Include EasyMDE -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
<script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>

<div class="container mt-4">
    
    <div class="row justify-content-center">
     
        <div class="col-md-8  border p-4 rounded shadow-sm">
               <h4 class="text-center mb-3">Create New Topic</h4>

    <?php if (!empty($msg)): ?>
        <p class="text-success"><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>

    
    <?php if (!empty($error)): ?>
        <p class="text-danger"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
            <form method="POST" class="">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Select Category --</option>
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                            <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tags (comma-separated)</label>
                    <input type="text" name="tags" class="form-control" placeholder="e.g. school, exams, advice">
                </div>

                <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea name="content" id="markdown-editor" rows="5"></textarea>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-dark px-4">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    .EasyMDEContainer .CodeMirror {
        min-height: 300px; /* or any height you prefer */
        max-height: 300px;
    }
</style>
<script>
    new EasyMDE({
        element: document.getElementById("markdown-editor"),
        spellChecker: false,
        placeholder: "Write your discussion content here...",
        toolbar: [
            "bold", "italic", "heading", "|", 
            "quote", "unordered-list", "ordered-list", "|", 
            "link", "image", "preview", "side-by-side", "fullscreen", "guide"
        ],
    });
</script>
