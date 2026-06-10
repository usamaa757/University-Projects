<?php
include('header.php');
include('db_connection.php');

// Fetch all hospitals
$sql = "SELECT * FROM hospitals";
$result = $conn->query($sql);

$hospitals = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hospitals[] = $row;
    }
}

$conn->close();
?>

<section class="contact py-5">
    <div class="container">
        <h2 class="mb-4">Hospitals' nformation</h2>
        <div class="row">
            <?php foreach ($hospitals as $hospital) : ?>
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h4><?php echo $hospital['name']; ?></h4>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-map-marker-alt"></i> <?php echo $hospital['address']; ?></li>
                                <li><i class="fas fa-phone"></i> <?php echo $hospital['phone']; ?></li>
                                <li><i class="fas fa-envelope"></i> <a href="mailto:<?php echo $hospital['email']; ?>"><?php echo $hospital['email']; ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>