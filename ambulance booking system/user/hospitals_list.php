<!-- hospital_list.php -->
<?php


include('header.php');
include('../db_connection.php');

// Fetch hospitals
$sql = "SELECT * FROM hospitals ORDER BY name ASC";
$result = $conn->query($sql);

$hospitals = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hospitals[] = $row;
    }
}

$conn->close();
?>

<div class="container">
    <a href="user_dashboard.php" class="btn btn-secondary mt-3 mb-3">Back to Dashboard</a>
    <div class="row justify-content-center">

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Hospital List</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Specialties</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hospitals as $hospital) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($hospital['name']); ?></td>
                                <td><?php echo htmlspecialchars($hospital['email']); ?></td>
                                <td><?php echo htmlspecialchars($hospital['phone']); ?></td>
                                <td><?php echo htmlspecialchars($hospital['address']); ?></td>
                                <td><?php echo htmlspecialchars($hospital['specialties']); ?></td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>