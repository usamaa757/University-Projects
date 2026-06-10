<?php

include("../db_connection.php");
include("header.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_GET['part_id'])) {
        $part_id = intval($_GET['part_id']);
        $payment_method = $_POST['payment_method'];

        // Fetch part details
        $stmt = $conn->prepare("SELECT * FROM auto_parts WHERE part_id = ?");
        $stmt->bind_param("i", $part_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $part = $result->fetch_assoc();

        if ($part) {
            // Assume user is logged in and buyer_id is stored in session
            $buyer_id = $_SESSION['buyer_id']; 
            $total_price = $part['price'];

            // Insert into orders table
            $stmt = $conn->prepare("INSERT INTO orders (buyer_id, total, payment_method) VALUES (?, ?, ?)");
            $stmt->bind_param("ids", $buyer_id, $total_price, $payment_method);
            if ($stmt->execute()) {
                $order_id = $stmt->insert_id;

                // Insert into order_items table
                $stmt = $conn->prepare("INSERT INTO order_items (order_id, part_id, price) VALUES (?, ?, ?)");
                $stmt->bind_param("iid", $order_id, $part_id, $part['price']);
                if ($stmt->execute()) {
                    if ($payment_method == 'online_payment') {
                        // Redirect to online payment gateway
                        header("Location: online_payment.php?order_id={$order_id}");
                        exit();
                    } else {
                        echo ' <div class="container col-4 mt-3">
                    <a href="products_list.php" class="btn btn-primary mb-2">Back</a>
                    <p class="alert alert-success">Your order has been placed. Please prepare for cash on delivery.</p>
                   </div>';
                       
                    }
                } else {
                    echo ' <div class="container col-3 mt-3">
                    <a href="products_list.php" class="btn btn-primary">Back</a>
                    <p class="alert alert-danger">Failed to add items to the order. Please try again.</p>
                   </div>';
                    // Handle error - inserting into order_items failed
                }
            } else {
                echo ' <div class="container col-3 mt-3">
                <a href="products_list.php" class="btn btn-primary">Back</a>
                <p class="alert alert-danger">Failed to create order. Please try again.</p>
               </div>';
               
            }
        } else {
            echo ' <div class="container col-3 mt-3">
            <a href="products_list.php" class="btn btn-primary">Back</a>
            <p class="alert alert-danger">Part not found. Please try again.</p>
           </div>';
        }

        // Close statement
        $stmt->close();
    } else {
        echo ' <div class="container col-3 mt-3">
            <a href="products_list.php" class="btn btn-primary">Back</a>
            <p class="alert alert-danger">Invalid request. No part selected.</p>
           </div>';
    }

  
}
