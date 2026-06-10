<?php
include '../db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You need to be logged in to vote.'); window.location.href='login.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment_id']) && isset($_POST['vote'])) {
    $comment_id = intval($_POST['comment_id']);
    $vote_type = $_POST['vote'];
    $user_id = $_SESSION['user_id'];

    // Check if the user has already voted on this comment
    $stmt = $conn->prepare("SELECT vote_type FROM comment_votes WHERE user_id = ? AND comment_id = ?");
    $stmt->bind_param("ii", $user_id, $comment_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // User has already voted, check the current vote type
        $stmt->bind_result($existing_vote_type);
        $stmt->fetch();

        if ($existing_vote_type === $vote_type) {
            // If the user is trying to vote the same type again (upvote -> upvote or downvote -> downvote)
            echo "<script>alert('You have already voted this way on this comment.'); window.history.back();</script>";
            exit;
        } else {
            // User is switching their vote (upvote -> downvote or downvote -> upvote)
            // Remove the existing vote
            $stmt = $conn->prepare("DELETE FROM comment_votes WHERE user_id = ? AND comment_id = ?");
            $stmt->bind_param("ii", $user_id, $comment_id);
            $stmt->execute();
            $stmt->close();

            // Insert the new vote
            $stmt = $conn->prepare("INSERT INTO comment_votes (user_id, comment_id, vote_type) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $user_id, $comment_id, $vote_type);
            $stmt->execute();
            $stmt->close();

            // Update the vote counts
            if ($vote_type == 'upvote') {
                $stmt = $conn->prepare("UPDATE comments SET upvotes = upvotes + 1, downvotes = downvotes - 1 WHERE comment_id = ?");
            } else {
                $stmt = $conn->prepare("UPDATE comments SET upvotes = upvotes - 1, downvotes = downvotes + 1 WHERE comment_id = ?");
            }
            $stmt->bind_param("i", $comment_id);
            $stmt->execute();
            $stmt->close();

            echo "<script>alert('Your vote has been updated.'); window.history.back();</script>";
            exit;
        }
    } else {
        // User has not voted, insert the new vote
        $stmt = $conn->prepare("INSERT INTO comment_votes (user_id, comment_id, vote_type) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $comment_id, $vote_type);
        $stmt->execute();
        $stmt->close();

        // Update the vote count for the comment
        if ($vote_type == 'upvote') {
            $stmt = $conn->prepare("UPDATE comments SET upvotes = upvotes + 1 WHERE comment_id = ?");
        } else {
            $stmt = $conn->prepare("UPDATE comments SET downvotes = downvotes + 1 WHERE comment_id = ?");
        }
        $stmt->bind_param("i", $comment_id);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Your vote has been recorded.'); window.history.back();</script>";
        exit;
    }
}