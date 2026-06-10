<?php
include("db_connect.php");
include("header.php");



$user_id = $_SESSION['user_id'];

// Fetch user info
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($user_query);

// Fetch user’s bookings
$bookings_query = mysqli_query($conn, "SELECT * FROM bookings WHERE user_id='$user_id' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Parent Dashboard - KVBS</title>
    <style>
    .logout {
        background: red;
        color: white;
        padding: 8px 15px;
        border-radius: 5px;
        text-decoration: none;
    }

    .logout:hover {
        background: darkred;
    }
    </style>
</head>

<body>

    <header>
        <h1>Welcome, <?php echo htmlspecialchars($user['full_name']); ?> 👋</h1>
    </header>

    <div class="dashboard-container">
        <div class="dashboard-links buttons">
            <a href="book_vaccine.php">Book Vaccination Visit</a>
            <a href="profile.php">Edit Profile</a>
            <a href="booking_list.php" class="card">View My Bookings</a>

            <a class="logout" href="logout.php">Logout</a>
        </div>

        <h2>Your Information</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>

        <h2>Your Bookings</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Vaccine</th>
                <th>Preferred Date</th>
                <th>Preferred Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php if (mysqli_num_rows($bookings_query) > 0) {
                while ($booking = mysqli_fetch_assoc($bookings_query)) { ?>
            <tr>
                <td><?php echo $booking['id']; ?></td>
                <td><?php echo htmlspecialchars($booking['vaccine_name']); ?></td>
                <td><?php echo htmlspecialchars($booking['preferred_date']); ?></td>
                <td><?php echo htmlspecialchars($booking['preferred_time']); ?></td>
                <td>
                    <?php
                            if ($booking['status'] == 'pending') echo "<span class= 'status pending'>Pending</span>";
                            elseif ($booking['status'] == 'confirmed') echo "<span class= 'status confirmed'>Confirmed</span>";
                            elseif ($booking['status'] == 'rejected') echo "<span class= 'status rejected'>Rejected</span>";
                            ?>
                </td>
                <td>
                    <?php if ($booking['status'] == 'pending') { ?>
                    <a class="action edit" href="update_booking.php?id=<?php echo $booking['id']; ?>">Edit</a>
                    <a class="action delete" href="?delete=<?php echo $booking['id']; ?>"
                        onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel</a>
                    <?php } else { ?>
                    <span style="color:gray;">No Action</span>
                    <?php } ?>
                </td>
            </tr>
            <?php }
            } else { ?>
            <tr>
                <td colspan="6" style="text-align:center;">No bookings yet.</td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <?php

    include('footer.php');

    ?>
</body>

</html>