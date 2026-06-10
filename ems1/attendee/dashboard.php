<?php
include 'header.php';
include '../db.php';

// Fetch attendee data
$attendee_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM attendees WHERE attendee_id = ?");
$stmt->bind_param("i", $attendee_id);
$stmt->execute();
$result = $stmt->get_result();
$attendee = $result->fetch_assoc();
?>

<style>
body {
    background-color: #f4f6f9;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.dashboard {
    max-width: 1100px;
    margin: 60px auto;
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
}

.welcome-card {
    background: #ffffff;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
    text-align: center;
}

.welcome-card h2 {
    margin-bottom: 10px;
    font-size: 28px;
    color: #2c3e50;
}

.welcome-card p {
    color: #7f8c8d;
    font-size: 15px;
}

.action-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

.card-link {
    background-color: #fff;
    border-radius: 16px;
    padding: 25px;
    text-align: center;
    text-decoration: none;
    color: #34495e;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
}

.card-link:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.card-link i {
    font-size: 40px;
    color: #3498db;
    margin-bottom: 15px;
}

.card-link h4 {
    font-size: 18px;
    margin-bottom: 5px;
}

.card-link p {
    font-size: 14px;
    color: #7f8c8d;
}
</style>

<div class="dashboard">
    <!-- Left: Welcome Info -->
    <div class="welcome-card">
        <h2>Welcome, <?= htmlspecialchars($attendee['username']) ?> 👋</h2>
        <p>Your personalized event space</p>
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User Icon" width="120">
    </div>

    <!-- Right: Actions -->
    <div class="action-cards">
        <a href="event_list.php" class="card-link">
            <i class="bi bi-calendar-event-fill"></i>
            <h4>Browse Events</h4>
            <p>Discover and join upcoming events</p>
        </a>

        <a href="view_rsvp_events.php" class="card-link">
            <i class="bi bi-check-circle-fill"></i>
            <h4>My RSVPs</h4>
            <p>View the events you've registered for</p>
        </a>


    </div>
</div>

</body>

</html>