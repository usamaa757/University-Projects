<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "tracking_seeds");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert data
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $color = $_POST['color'];
    $cost = $_POST['cost'];

    $sql = "INSERT INTO seeds (Title, Category, Color, CostPerKg) 
            VALUES ('$title', '$category', '$color', '$cost')";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'> Record added successfully!</p>";
    } else {
        echo "<p style='color:red;'> Error: " . $conn->error . "</p>";
    }
}

// Search
$search_result = null;
if (isset($_POST['search'])) {
    $category = $_POST['search_category'];
    $title = $_POST['search_title'];

    $sql = "SELECT * FROM seeds WHERE Category='$category' AND Title='$title'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $search_result = $result->fetch_assoc();
    } else {
        $search_result = "No record found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Seeds Record Portal</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background: url('background.jpg') no-repeat center center fixed;
        background-size: cover;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 50%;
        margin: auto;
        background: rgba(255, 255, 255, 0.38);
        padding: 20px;
        margin-top: 30px;
        border-radius: 10px;
    }

    h2,
    h3,
    h4 {
        text-align: center;
    }

    form {
        margin: 20px 0;
    }

    input,
    select {
        padding: 10px;
        margin: 8px;
        width: 90%;
        border-radius: 5px;
        border: none;
        background: rgba(255, 255, 255, 0.81);
    }

    button {
        padding: 10px 20px;
        margin: 10px;
        border: none;
        border-radius: 5px;
        background: #27ae60;
        color: white;
        cursor: pointer;
    }

    button.reset {
        background: #c0392b;
    }

    .result {
        margin-top: 20px;
        background: #ecf0f1;
        padding: 15px;
        border-radius: 5px;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>🌱 Seeds Record Tracking Portal</h2>

        <!-- Task 1: Data Entry Form -->
        <h3>Add Seed Record</h3>
        <form method="post">
            <input type="text" name="title" placeholder="Seed Title" required><br>
            <input type="text" name="category" placeholder="Category" required><br>
            <input type="text" name="color" placeholder="Color"><br>
            <input type="number" step="0.01" name="cost" placeholder="Cost per Kg"><br>
            <button type="submit" name="submit">Submit</button>
            <button type="reset" class="reset">Reset</button>
        </form>

        <!-- Task 2: Search -->
        <h3>Search Seed Record</h3>
        <form method="post">
            <input type="text" name="search_category" placeholder="Enter Category" required><br>
            <input type="text" name="search_title" placeholder="Enter Title" required><br>
            <button type="submit" name="search">Search</button>
        </form>

        <?php if ($search_result): ?>
        <div class="result">
            <?php if (is_array($search_result)): ?>
            <p><strong>Seed ID:</strong> <?= $search_result['SeedId'] ?></p>
            <p><strong>Title:</strong> <?= $search_result['Title'] ?></p>
            <p><strong>Category:</strong> <?= $search_result['Category'] ?></p>
            <p><strong>Color:</strong> <?= $search_result['Color'] ?></p>
            <p><strong>Cost per Kg:</strong> <?= $search_result['CostPerKg'] ?></p>
            <?php else: ?>
            <p><?= $search_result ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</body>

</html>