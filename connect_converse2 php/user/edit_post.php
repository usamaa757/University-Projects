<?php
include 'header.php';
include '../db.php';

$msg = "";
$error = "";

$post = [];
$tagString = "";
$categories = [];

if (!isset($_GET['topic_id']) || !is_numeric($_GET['topic_id'])) {
    $error = "Invalid topic ID.";
} else {
    $topic_id = (int) $_GET['topic_id'];

    // Fetch post
    $result = $conn->query("SELECT * FROM discussion_topics WHERE topic_id = $topic_id");
    if ($result && $result->num_rows > 0) {
        $post = $result->fetch_assoc();
    } else {
        $error = "Post not found.";
    }

    // Fetch tags
    $tagNames = [];
    $tagsQuery = $conn->query("SELECT tag_name FROM tags WHERE tag_id IN (
        SELECT tag_id FROM topic_tags WHERE topic_id = $topic_id
    )");
    if ($tagsQuery) {
        while ($row = $tagsQuery->fetch_assoc()) {
            $tagNames[] = $row['tag_name'];
        }
        $tagString = implode(', ', $tagNames);
    }

    // Fetch categories
    $catResult = $conn->query("SELECT category_id, category_name FROM categories");
    if ($catResult) {
        while ($row = $catResult->fetch_assoc()) {
            $categories[] = $row;
        }
    }

    // Form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $category_id = (int) $_POST['category_id'];
        $tags = trim($_POST['tags']);

        if (empty($title) || empty($content) || empty($category_id)) {
            $error = "All fields are required.";
        } else {
            $title = $conn->real_escape_string($title);
            $content = $conn->real_escape_string($content);
            $tags = $conn->real_escape_string($tags);

            $update = $conn->query("UPDATE discussion_topics 
                SET title = '$title', content = '$content', category_id = $category_id 
                WHERE topic_id = $topic_id");

            if ($update) {
                // (Optional) You can update tags in topic_tags here
                $msg = "Post updated successfully.";
                $post['title'] = $title;
                $post['content'] = $content;
                $post['category_id'] = $category_id;
                $tagString = $tags;
            } else {
                $error = "Failed to update post.";
            }
        }
    }
}
?>

<!-- EasyMDE Styles -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8 border p-4 rounded shadow-sm">
            <h4 class="text-center mb-3">Edit Topic</h4>

            <?php if (!empty($msg)): ?>
                <p class="text-success text-center"><?= htmlspecialchars($msg) ?></p>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <p class="text-danger text-center"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if (!empty($post)): ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($post['title']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['category_id'] ?>" <?= ($cat['category_id'] == $post['category_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['category_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tags (comma-separated)</label>
                        <input type="text" name="tags" class="form-control" value="<?= htmlspecialchars($tagString) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="content" id="markdown-editor"><?= htmlspecialchars_decode($post['content']) ?></textarea>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success px-4">Update</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- EasyMDE Scripts -->
<script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
<script>
    if (document.getElementById("markdown-editor")) {
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
    }
</script>

<style>
    .EasyMDEContainer .CodeMirror {
        min-height: 300px;
        max-height: 300px;
    }
</style>