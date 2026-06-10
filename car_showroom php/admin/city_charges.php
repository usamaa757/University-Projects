<?php
include '../db.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_city'])) {
    $city_name = $conn->real_escape_string(trim($_POST['city_name']));
    $delivery_charge = (int)$_POST['delivery_charge'];

    if (!empty($city_name) && $delivery_charge >= 0) {
        $insert = $conn->query("INSERT INTO cities (city_name, delivery_charge) VALUES ('$city_name', $delivery_charge)");
        if ($insert) {
            echo "<script>alert('City added successfully!');</script>";
        } else {
            echo "<script>alert('Error adding city.');</script>";
        }
    } else {
        echo "<script>alert('Please enter valid city name and delivery charge.');</script>";
    }
}

// Update existing city charge
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_city_charge'])) {
    $city_id = (int)$_POST['city_id'];
    $new_charge = (int)$_POST['delivery_charge'];
    if ($conn->query("UPDATE cities SET delivery_charge = $new_charge WHERE city_id = $city_id")) {
        echo "<script>alert('Delivery charge updated successfully.');</script>";
    } else {
        echo "<script>alert('Error updating delivery charge.');</script>";
    }
}
// Fetch cities
$cities = $conn->query("SELECT * FROM cities ORDER BY city_name ASC");
?>

<div class="container my-5">
    <div class="card shadow border-0 rounded-4">
        <h3 class="text-center mt-4">Manage City Delivery Charges</h3>
        <div class="card-body">


            <!-- Add City Form -->
            <h5 class="text-center mb-3">Add New City</h5>
            <form method="post" class="row g-3 justify-content-center align-items-center mb-5">
                <div class="col-md-4">
                    <input type="text" name="city_name" class="form-control" placeholder="City Name" required>
                </div>
                <div class="col-md-3">
                    <input type="number" name="delivery_charge" class="form-control" placeholder="Delivery Charge"
                        required>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="add_city" class="btn">Add City</button>
                </div>
            </form>

            <!-- Table of Cities -->
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>City Name</th>
                            <th>Delivery Charge</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($city = $cities->fetch_assoc()): ?>
                        <tr>
                            <form method="post">
                                <td><?= htmlspecialchars($city['city_name']) ?></td>
                                <td>
                                    <input type="hidden" name="city_id" value="<?= $city['city_id'] ?>">
                                    <input type="number" name="delivery_charge" value="<?= $city['delivery_charge'] ?>"
                                        class="form-control" required>
                                </td>
                                <td>
                                    <button type="submit" name="update_city_charge" class="btn btn-sm">Update</button>
                                </td>
                            </form>
                        </tr>
                        <?php endwhile; ?>
                        <?php if ($cities->num_rows == 0): ?>
                        <tr>
                            <td colspan="4">No cities found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>