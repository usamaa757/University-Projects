<?php
include("db_connect.php");
include("header.php");


$message = "";
$error = "";

$worker_id = $_SESSION['user_id'];

$sql = "
SELECT 
    c.child_name, 
    c.dob, 
    c.gender, 
    b.vaccine_name, 
    b.vaccinated_at,
    u.full_name AS parent_name,
    u.city AS parent_city
FROM bookings b
JOIN children c ON b.child_id = c.id
JOIN users u ON b.parent_id = u.id
WHERE b.status = 'completed'
ORDER BY b.vaccinated_at DESC
";

$result = mysqli_query($conn, $sql);

?>
<div class="management-container">
    <h2>Vaccinated Child Details</h2>

    <table>
        <tr>
            <th>Child Name</th>
            <th>DOB</th>
            <th>Gender</th>
            <th>Vaccine Given</th>
            <th>Vaccinated At</th>
            <th>Parent Name</th>
            <th>Parent City</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['child_name']); ?></td>
            <td><?php echo htmlspecialchars($row['dob']); ?></td>
            <td><?php echo htmlspecialchars($row['gender']); ?></td>
            <td><?php echo htmlspecialchars($row['vaccine_name']); ?></td>
            <td><?php echo htmlspecialchars($row['vaccinated_at']); ?></td>
            <td><?php echo htmlspecialchars($row['parent_name']); ?></td>
            <td><?php echo htmlspecialchars($row['parent_city']); ?></td>
        </tr>
        <?php } ?>
    </table>
</div>

<?php

include('footer.php');

?>