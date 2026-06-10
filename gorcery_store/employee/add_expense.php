<?php
include('header.php');
include('../db_connection.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $expense_type = $_POST['expense_type'];
    $amount = $_POST['amount'];
    $expense_date = date('Y-m-d'); // Current date
    $description = $_POST['description'];

    // Insert the expense record into the database
    $query = "INSERT INTO expenses (expense_type, amount, expense_date, description) 
              VALUES ('$expense_type', '$amount', '$expense_date', '$description')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Expense recorded successfully!');</script>";
    } else {
        echo "<script>alert('Error recording expense.');</script>";
    }
}
?>


<!-- Expense Recording Form -->
<div class="container mt-5 rounded shadow border p-0" style="max-width: 500px;">
    <h3 class="bg-dark text-center text-white p-2">Record A Expene</h3>
    <div class="p-4">
        <form method="POST" action="add_expense.php">
            <div class="form-group">
                <label for="expense_type">Expense Type</label>
                <input type="text" class="form-control" id="expense_type" name="expense_type" required>
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Record Expense</button>
        </form>
    </div>
</div>
</body>

</html>