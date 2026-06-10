<?php
include "header.php";

include('../db_connection.php');


if (isset($_GET["ambulance_id"])) {

    $ambulance_id = $_GET['ambulance_id'];

    $sql_booking_id = "SELECT booking_id FROM ambulance_user_assignment WHERE ambulance_id = ?";
    $stmt = $conn->prepare($sql_booking_id);
    $stmt->bind_param("i", $ambulance_id);
    $stmt->execute();
    $stmt->bind_result($booking_id);
    $stmt->fetch();
    $stmt->close();

    // Fetch booking details using the booking ID
    if ($booking_id) {

        // Query to fetch booking and related disease information from patients
        $sql = "SELECT b.*, d.name AS disease_name, p.disease_id, p.patient_status
            FROM bookings b
            LEFT JOIN patients p ON b.patient_id = p.patient_id
            LEFT JOIN diseases d ON p.disease_id = d.disease_id
            WHERE b.booking_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $booking = $result->fetch_assoc();
        $stmt->close();

        // Fetch all diseases for the dropdown or other options
        $sql_diseases = "SELECT * FROM diseases";
        $result_diseases = $conn->query($sql_diseases);
        $diseases = [];
        while ($row = $result_diseases->fetch_assoc()) {
            $diseases[] = $row;
        }
    } else {
        header("Location: user_dashboard.php");
        exit();
    }
}
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $pickup_location = $_POST['pickup_point'];
    $disease_id = $_POST['disease_id'];
    $patient_status = $_POST['patient_status'];
    $booking_id = $_POST['booking_id'];

    // Fetch the patient_id associated with the booking_id
    $sql_fetch_patient = "SELECT patient_id FROM bookings WHERE booking_id = ? AND user_id = ?";
    $stmt_fetch_patient = $conn->prepare($sql_fetch_patient);
    $stmt_fetch_patient->bind_param("ii", $booking_id, $_SESSION['user_id']);
    $stmt_fetch_patient->execute();
    $stmt_fetch_patient->bind_result($patient_id);
    $stmt_fetch_patient->fetch();
    $stmt_fetch_patient->close();

    if ($patient_id) {
        // Update the patient's disease_id and status
        $sql_update_patient = "UPDATE patients
                               SET disease_id = ?, patient_status = ?
                               WHERE patient_id = ?";
        $stmt_update_patient = $conn->prepare($sql_update_patient);
        $stmt_update_patient->bind_param("isi", $disease_id, $patient_status, $patient_id);
        $update_patient_success = $stmt_update_patient->execute();
        $stmt_update_patient->close();

        // Update the booking's pickup point
        $sql_update_booking = "UPDATE bookings
                               SET pickup_point = ?
                               WHERE booking_id = ? AND user_id = ?";
        $stmt_update_booking = $conn->prepare($sql_update_booking);
        $stmt_update_booking->bind_param("sii", $pickup_location, $booking_id, $_SESSION['user_id']);
        $update_booking_success = $stmt_update_booking->execute();
        $stmt_update_booking->close();

        // Check for successful updates
        if ($update_patient_success && $update_booking_success) {
            $_SESSION['success'] = "Booking and patient details updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update booking or patient details.";
        }
    } else {
        $_SESSION['error'] = "Patient not found for this booking.";
    }

    $conn->close();
    header("Location: booking_status.php");
    exit();
}



?>


<!-- HTML Form for Editing Booking Details -->
<div class="container">
    <a href="booking_status.php?ambulance_id=<?php echo $ambulance_id; ?>" class="btn btn-secondary mt-3 mb-3">Back</a>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Booking Details</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success'])) : ?>
                        <div class="alert alert-success">
                            <?php echo $_SESSION['success'];
                            unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error'])) : ?>
                        <div class="alert alert-danger">
                            <?php echo $_SESSION['error'];
                            unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo $_SERVER['PHP_SELF'] . "?booking_id=" . urlencode($booking_id); ?>"
                        method="post">
                        <div class="form-group">
                            <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                            <label for="pickup_point">Pickup Location:</label>
                            <input type="text" class="form-control" id="pickup_point" name="pickup_point"
                                value="<?php echo htmlspecialchars($booking['pickup_point']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="disease_id">Disease:</label>
                            <select class="form-control" id="disease_id" name="disease_id" required>
                                <?php foreach ($diseases as $disease) : ?>
                                    <option value="<?php echo htmlspecialchars($disease['disease_id']); ?>"
                                        <?php echo ($disease['disease_id'] == $booking['disease_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($disease['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="patient_status">Patient Status:</label>
                            <input type="text" class="form-control" id="patient_status" name="patient_status"
                                value="<?php echo htmlspecialchars($booking['patient_status']); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Booking</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>