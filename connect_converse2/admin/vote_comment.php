<?php
include '../db.php';
session_start();

$msg = '';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment_id']) && isset($_POST['vote'])) {
    $comment_id = intval($_POST['comment_id']);
    $topic_id = intval($_POST['topic_id']);
    $vote_type = $_POST['vote'];
    $user_id = $_SESSION['user_id'];

    // Check if user has already voted
    $result = mysqli_query($conn, "SELECT vote_type FROM comment_votes WHERE user_id = $user_id AND comment_id = $comment_id");

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $existing_vote_type = $row['vote_type'];

        if ($existing_vote_type === $vote_type) {

            $error = "You have already voted this way on this comment.";
            header("Location: view_topics.php?topic_id=$topic_id&error=" . urlencode($error));
        } else {
            // Remove old vote
            mysqli_query($conn, "DELETE FROM comment_votes WHERE user_id = $user_id AND comment_id = $comment_id");

            // Insert new vote
            mysqli_query($conn, "INSERT INTO comment_votes (user_id, comment_id, vote_type) VALUES ($user_id, $comment_id, '$vote_type')");

            // Update counts
            if ($vote_type === 'upvote') {
                mysqli_query($conn, "UPDATE comments SET upvotes = upvotes + 1, downvotes = downvotes - 1 WHERE comment_id = $comment_id");
            } else {
                mysqli_query($conn, "UPDATE comments SET upvotes = upvotes - 1, downvotes = downvotes + 1 WHERE comment_id = $comment_id");
            }

            $msg = "Your vote has been updated.";
            header("Location: view_topics.php?topic_id=$topic_id&msg=" . urlencode($msg));
        }
    } else {
        // New vote
        mysqli_query($conn, "INSERT INTO comment_votes (user_id, comment_id, vote_type) VALUES ($user_id, $comment_id, '$vote_type')");

        if ($vote_type === 'upvote') {
            mysqli_query($conn, "UPDATE comments SET upvotes = upvotes + 1 WHERE comment_id = $comment_id");
        } else {
            mysqli_query($conn, "UPDATE comments SET downvotes = downvotes + 1 WHERE comment_id = $comment_id");
        }

        $msg = "Your vote has been recorded.";
        header("Location: view_topics.php?topic_id=$topic_id&msg=" . urlencode($msg));
    }
}
