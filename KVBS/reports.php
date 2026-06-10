<?php
include("header.php");
include("db_connect.php");

$message = "";
$error = "";

// Fetch filters
$vaccine_res = mysqli_query($conn, "SELECT vaccine_name FROM vaccines ORDER BY vaccine_name ASC");
$city_res = mysqli_query($conn, "SELECT DISTINCT city FROM users ORDER BY city ASC");

$vaccine = $_GET['vaccine'] ?? '';
$city = $_GET['city'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// Build query
$sql = "SELECT c.child_name, c.dob, c.gender, b.vaccine_name, b.vaccinated_at, 
               u.full_name AS parent_name, u.city AS parent_city
        FROM bookings b
        JOIN children c ON b.child_id = c.id
        JOIN users u ON b.parent_id = u.id
        WHERE b.status='completed'";

if (!empty($vaccine)) $sql .= " AND b.vaccine_name='" . mysqli_real_escape_string($conn, $vaccine) . "'";
if (!empty($city)) $sql .= " AND u.city='" . mysqli_real_escape_string($conn, $city) . "'";
if (!empty($start_date) && !empty($end_date))
    $sql .= " AND b.vaccinated_at BETWEEN '$start_date' AND '$end_date'";

$sql .= " ORDER BY b.vaccinated_at DESC";
$report_res = mysqli_query($conn, $sql);

// Weekly summary
$weekly_res = mysqli_query($conn, "
    SELECT YEAR(vaccinated_at) AS year, WEEK(vaccinated_at) AS week, COUNT(*) AS total
    FROM bookings WHERE status='completed'
    GROUP BY YEAR(vaccinated_at), WEEK(vaccinated_at)
    ORDER BY year DESC, week DESC
");

// Monthly summary
$monthly_res = mysqli_query($conn, "
    SELECT YEAR(vaccinated_at) AS year, MONTH(vaccinated_at) AS month, COUNT(*) AS total
    FROM bookings WHERE status='completed'
    GROUP BY YEAR(vaccinated_at), MONTH(vaccinated_at)
    ORDER BY year DESC, month DESC
");
?>

<div class="report-container">
    <a class="back-btn" href="dashboard.php"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>

    <h2><i class="fa-solid fa-chart-line"></i> Vaccination Reports</h2>

    <form method="GET" class="report-filters">
        <div class="filter-group">
            <label><i class="fa-solid fa-syringe"></i> Vaccine:</label>
            <select name="vaccine">
                <option value="">All</option>
                <?php while ($v = mysqli_fetch_assoc($vaccine_res)) { ?>
                <option value="<?php echo htmlspecialchars($v['vaccine_name']); ?>"
                    <?php if ($vaccine == $v['vaccine_name']) echo "selected"; ?>>
                    <?php echo htmlspecialchars($v['vaccine_name']); ?>
                </option>
                <?php } ?>
            </select>
        </div>

        <div class="filter-group">
            <label><i class="fa-solid fa-city"></i> City:</label>
            <select name="city">
                <option value="">All</option>
                <?php while ($c = mysqli_fetch_assoc($city_res)) { ?>
                <option value="<?php echo htmlspecialchars($c['city']); ?>"
                    <?php if ($city == $c['city']) echo "selected"; ?>>
                    <?php echo htmlspecialchars($c['city']); ?>
                </option>
                <?php } ?>
            </select>
        </div>

        <div class="filter-group">
            <label><i class="fa-solid fa-calendar-day"></i> Start Date:</label>
            <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
        </div>

        <div class="filter-group">
            <label><i class="fa-solid fa-calendar-check"></i> End Date:</label>
            <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
        </div>

        <button type="submit"><i class="fa-solid fa-magnifying-glass-chart"></i> Generate Report</button>
    </form>

    <h3><i class="fa-solid fa-children"></i> Vaccinated Kids</h3>
    <table>
        <thead>
            <tr>
                <th><i class="fa-solid fa-child"></i> Child</th>
                <th><i class="fa-solid fa-calendar"></i> DOB</th>
                <th><i class="fa-solid fa-venus-mars"></i> Gender</th>
                <th><i class="fa-solid fa-syringe"></i> Vaccine</th>
                <th><i class="fa-solid fa-clock"></i> Vaccinated At</th>
                <th><i class="fa-solid fa-user"></i> Parent</th>
                <th><i class="fa-solid fa-city"></i> City</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($report_res) > 0) {
                while ($row = mysqli_fetch_assoc($report_res)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['child_name']); ?></td>
                <td><?php echo htmlspecialchars($row['dob']); ?></td>
                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                <td><?php echo htmlspecialchars($row['vaccine_name']); ?></td>
                <td><?php echo htmlspecialchars($row['vaccinated_at']); ?></td>
                <td><?php echo htmlspecialchars($row['parent_name']); ?></td>
                <td><?php echo htmlspecialchars($row['parent_city']); ?></td>
            </tr>
            <?php }
            } else { ?>
            <tr>
                <td colspan="7" style="text-align:center;">No records found.</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <h3><i class="fa-solid fa-calendar-week"></i> Weekly Summary</h3>
    <table>
        <tr>
            <th>Year</th>
            <th>Week</th>
            <th>Total Vaccinated</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($weekly_res)) { ?>
        <tr>
            <td><?php echo $row['year']; ?></td>
            <td><?php echo $row['week']; ?></td>
            <td><?php echo $row['total']; ?></td>
        </tr>
        <?php } ?>
    </table>

    <h3><i class="fa-solid fa-calendar-days"></i> Monthly Summary</h3>
    <table>
        <tr>
            <th>Year</th>
            <th>Month</th>
            <th>Total Vaccinated</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($monthly_res)) { ?>
        <tr>
            <td><?php echo $row['year']; ?></td>
            <td><?php echo $row['month']; ?></td>
            <td><?php echo $row['total']; ?></td>
        </tr>
        <?php } ?>
    </table>
</div>

<?php include('footer.php'); ?>