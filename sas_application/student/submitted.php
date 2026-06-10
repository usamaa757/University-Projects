<?php
session_start();
include '../other/db_connection.php';

// Fetch the message from the query parameter
$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submission Success</title>
</head>
<body>
    <p><?= $msg ?></p>
    <a href="view_quiz.php">Click</a> to go back
</body>
</html>
