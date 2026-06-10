<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: user-login.php");
    exit();
}

include 'db_connect.php';

$student_id = $_SESSION['student_id'];

// Fetch countries and cities
$sql_country = "SELECT * FROM countries";
$result_country = $conn->query($sql_country);

$sql_city = "SELECT * FROM cities";
$result_city = $conn->query($sql_city);

// Fetch existing personal information
$query = "SELECT * FROM stud_admission WHERE student_id = ? AND status = 'pending'";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $cnic = !empty($_POST['cnic']) ? $_POST['cnic'] : $row['cnic'];
    $full_name = !empty($_POST['full_name']) ? $_POST['full_name'] : $row['full_name'];
    $gender = !empty($_POST['gender']) ? $_POST['gender'] : $row['gender'];
    $dob = !empty($_POST['dob']) ? $_POST['dob'] : $row['dob'];
    $nationality = !empty($_POST['nationality']) ? $_POST['nationality'] : $row['nationality'];
    $country = !empty($_POST['country']) ? $_POST['country'] : $row['country'];
    $city = !empty($_POST['city']) ? $_POST['city'] : $row['city'];
    $postal_address = !empty($_POST['postal_address']) ? $_POST['postal_address'] : $row['postal_address'];
    $residential_address = !empty($_POST['residential_address']) ? $_POST['residential_address'] : $row['residential_address'];

    // Handle photograph upload
    $upload_dir = 'uploads/';
    $photograph_path = $row['photograph']; // Keep existing photograph path by default
    if ($_FILES['photograph']['error'] == UPLOAD_ERR_OK) {
        $photograph_path = $upload_dir . uniqid() . '_' . basename($_FILES['photograph']['name']);
        if (!move_uploaded_file($_FILES['photograph']['tmp_name'], $photograph_path)) {
            echo "<script>alert('Failed to upload photograph.');</script>";
            exit;
        }
    }

    // Update personal information in the database
    $update_query = "UPDATE stud_admission SET 
                     cnic = ?, 
                     full_name = ?, 
                     gender = ?, 
                     dob = ?, 
                     nationality = ?, 
                     country = ?, 
                     city = ?, 
                     postal_address = ?, 
                     residential_address = ?, 
                     photograph = ?
                     WHERE student_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssssssssi", $cnic, $full_name, $gender, $dob, $nationality, $country, $city, $postal_address, $residential_address, $photograph_path, $student_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script>
                alert('Form updated successfully!');
                window.location.href = 'view_your_form.php';
              </script>";
    } else {
        echo "<script>alert('No changes were made.');</script>";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Personal Information</title>
  
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        form {
            margin: 0;
        }

        fieldset {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }

        legend {
            font-weight: bold;
            color: #007bff;
            padding: 0 10px;
            background-color: #e9ecef;
            border-radius: 5px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            color: #333;
        }

        input[type="text"],
        input[type="date"],
        input[type="file"],
        select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button[type="submit"] {
            padding: 10px 20px;
            margin: 20px 0;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }

        a.btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #6c757d;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
        }

        a.btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Edit Personal Information</h1>
        <form method="post" enctype="multipart/form-data">
            <fieldset>
                <legend>Personal Details</legend>
                <label for="cnic">CNIC:</label>
                <input type="text" name="cnic" value="<?php echo htmlspecialchars($row['cnic']); ?>"><br>
                <label for="full_name">Full Name:</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($row['full_name']); ?>"><br>
                <label for="gender">Gender:</label>
                <input type="text" name="gender" value="<?php echo htmlspecialchars($row['gender']); ?>"><br>
                <label for="dob">Date of Birth:</label>
                <input type="date" name="dob" value="<?php echo htmlspecialchars($row['dob']); ?>"><br>
                <label for="nationality">Nationality:</label>
                <input type="text" name="nationality" value="<?php echo htmlspecialchars($row['nationality']); ?>"><br>
                
                <label for="country">Select Country</label>
                <select name="country" id="country">
                    <option value="">Select Country</option>
                    <?php
                    if ($result_country->num_rows > 0) {
                        while ($country_row = $result_country->fetch_assoc()) {
                            $selected = ($row['country'] == $country_row['country_name']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($country_row['country_name']) . '" ' . $selected . '>' . htmlspecialchars($country_row['country_name']) . '</option>';
                        }
                    } else {
                        echo '<option value="">No countries available</option>';
                    }
                    ?>
                </select><br>

                <label for="city">Select City</label>
                <select name="city" id="city">
                    <option value="">Select City</option>
                    <?php
                    if ($result_city->num_rows > 0) {
                        while ($city_row = $result_city->fetch_assoc()) {
                            $selected = ($row['city'] == $city_row['city_name']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($city_row['city_name']) . '" ' . $selected . '>' . htmlspecialchars($city_row['city_name']) . '</option>';
                        }
                    } else {
                        echo '<option value="">No cities available</option>';
                    }
                    ?>
                </select><br>
                
                <label for="postal_address">Postal Address:</label>
                <input type="text" name="postal_address" value="<?php echo htmlspecialchars($row['postal_address']); ?>"><br>
                <label for="residential_address">Residential Address:</label>
                <input type="text" name="residential_address" value="<?php echo htmlspecialchars($row['residential_address']); ?>"><br>
                <label for="photograph">Profile Picture:</label>
                <input type="file" name="photograph"><br>
            </fieldset>
            <button type="submit">Update</button>
        </form>
        <a href="view_your_form.php" class="btn">Back to Application</a>
    </div>
</body>
</html>
