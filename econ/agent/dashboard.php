<?php
include 'header.php';

$agentName = $_SESSION['fullname'] ?? 'Agent';
$agentPhoto = $_SESSION['photo'] ?? 'default.jpg';
$photoPath = "../admin/uploads/agents/" . $agentPhoto;
?>

<div class="profile-box">
    <img src="<?= htmlspecialchars($photoPath) ?>" alt="Profile Photo" class="profile-photo">
    <div class="section-header">
        <h1>Welcome, <?= htmlspecialchars($agentName) ?> 👋</h1>
    </div>
</div>
<main class="dashboard-main">
    <div class="card-grid">
        <div class="card">
            <a href="add_property.php">
                <div class="card-icon"><i class="fas fa-plus-circle"></i></div>
                <div class="card-title">Add Property</div>
            </a>
            <div class="card-desc">Add a new property to your listings.</div>
        </div>

        <div class="card">
            <a href="my_properties.php">
                <div class="card-icon"><i class="fas fa-home"></i></div>
                <div class="card-title">My Properties</div>
            </a>
            <div class="card-desc">View and manage your listed properties.</div>
        </div>

        <div class="card">
            <a href="feedback.php">
                <div class="card-icon"><i class="fas fa-comments"></i></div>
                <div class="card-title">Feedback</div>
            </a>
            <div class="card-desc">Read user feedback and suggestions.</div>
        </div>

        <div class="card">
            <a href="installments.php">
                <div class="card-icon"><i class="fas fa-credit-card"></i></div>
                <div class="card-title">Purchased Property</div>
            </a>
            <div class="card-desc">Check installment purchase history.</div>
        </div>

        <div class="card">
            <a href="rented_property.php">
                <div class="card-icon"><i class="fas fa-key"></i></div>
                <div class="card-title">Rentals</div>
            </a>
            <div class="card-desc">See properties currently rented.</div>
        </div>

        <div class="card">
            <a href="terminated_rentals.php">
                <div class="card-icon"><i class="fas fa-ban"></i></div>
                <div class="card-title">Terminated Rentals</div>
            </a>
            <div class="card-desc">Check rentals that have ended.</div>
        </div>
    </div>
</main>

<style>

</style>

<?php include '../footer.php'; ?>

</body>

</html>