<?php
include("header.php");
include("../db_connection.php");

// Check if the user is logged in and is a seller
if (!isset($_SESSION['seller_id'])) {
    header("Location: ../login.php");
    exit();
}
$seller_id = $_SESSION['seller_id'];
$sql = "SELECT reviews.*, buyers.buyer_name
        FROM reviews
        JOIN buyers ON reviews.buyer_id = buyers.buyer_id
        WHERE reviews.seller_id = ?
        ORDER BY reviews.review_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();
?>


<div class="container mt-3">

    
        <div class="border shadow bg-white rounded">
            <h3 class="text-center heading-bg bg-dark text-white p-2">Feadback & Review</h3>
            <div class="p-2">
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <div class="review">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th> Name </th>
                                    <th>Rating</th>
                                    <th> Review </th>
                                    <th> Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <td><?php echo htmlspecialchars($row['buyer_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['rating']); ?>/5</td>
                                <td><?php echo htmlspecialchars($row['review_text']); ?></td>
                                <td><?php echo htmlspecialchars($row['review_date']); ?></td>
                            </tbody>
                        </table>
                        <h4> </h4>
                        <p></p>
                        <small></small>
                    </div>
                <?php endwhile; ?>
            </div>

    </div>
</div>
</body>

</html>