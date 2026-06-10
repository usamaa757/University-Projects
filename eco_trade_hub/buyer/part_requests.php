<?php
include("../db_connection.php");
include("header.php");

if (!isset($_SESSION['buyer_id'])) {
    header("Location: ../login.php");
    exit();
}

$buyer_id = $_SESSION['buyer_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $part_name = $_POST['part_name'];
    $part_description = $_POST['part_description'];

    $stmt = $conn->prepare("INSERT INTO part_requests (buyer_id, part_name, part_description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $buyer_id, $part_name, $part_description);

    if ($stmt->execute()) {
        $success_message = "Request submitted successfully!";
    } else {
        $error_message = "Error submitting request.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Part</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="border shadow bg-white rounded">
                    <h3 class="text-center heading-bg bg-dark text-white p-2">Part Request</h3>
                    <div class="p-4">
                        <?php if (isset($success_message)) : ?>
                            <div class="alert alert-success"><?php echo $success_message; ?></div>
                        <?php endif; ?>
                        <?php if (isset($error_message)) : ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="part_name">Part Name</label>
                                <input type="text" class="form-control" id="part_name" name="part_name" required>
                            </div>
                            <div class="form-group">
                                <label for="part_description">Part Description</label>
                                <textarea class="form-control" id="part_description" name="part_description" rows="3"></textarea>
                            </div>
                            <div class="text-center">
                            <button type="submit" class="btn btn-primary">Submit Request</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</body>

</html>