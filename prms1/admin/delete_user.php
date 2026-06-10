<?php
include '../config/database.php';

$id = $_GET['id'] ?? 0;
$type = $_GET['type'] ?? '';

if (!$id || !$type) {
    die("Invalid request.");
}

switch ($type) {
    case 'doctor':
        $stmt = $conn->prepare("DELETE FROM doctors WHERE id = ?");
        $redirect = 'manage_doctor.php';
        break;

    case 'receptionist':
        $stmt = $conn->prepare("DELETE FROM receptionists WHERE id = ?");
        $redirect = 'manage_receptionist.php';
        break;

    case 'patient':
        $stmt = $conn->prepare("DELETE FROM patients WHERE id = ?");
        $redirect = 'view_patient.php';
        break;

    default:
        die("Unknown user type.");
}

// Bind and execute
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: $redirect");
    exit();
} else {
    echo "Error deleting $type.";
}