<?php
// Connect to the database
$servername = "localhost";
$username = "root"; // Change this to your MySQL username
$password = ""; // Change this to your MySQL password
$dbname = "employee";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$msg = '';
$error = '';

// Add/Save Employee
if (isset($_POST['add'])) {
    $emp_id = $_POST['emp_id'];
    $emp_name = $_POST['emp_name'];
    $emp_address = $_POST['emp_address'];
    $emp_designation = $_POST['emp_designation'];
    $emp_salary = $_POST['emp_salary'];

    if (empty($emp_id) || empty($emp_name) || empty($emp_address) || empty($emp_designation) || empty($emp_salary)) {
        $error = "All fields are required.";
    } else {
        $sql = "INSERT INTO emp_table (Emp_ID, Emp_Name, Emp_Address, Emp_Designation, Emp_Salary)
                VALUES ('$emp_id', '$emp_name', '$emp_address', '$emp_designation', '$emp_salary')";

        if ($conn->query($sql) === TRUE) {
            $msg = "New record created successfully";
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Update Employee
if (isset($_POST['update'])) {
    $emp_id = $_POST['emp_id'];
    $emp_name = $_POST['emp_name'];
    $emp_address = $_POST['emp_address'];
    $emp_designation = $_POST['emp_designation'];
    $emp_salary = $_POST['emp_salary'];

    if (empty($emp_id) || empty($emp_name) || empty($emp_address) || empty($emp_designation) || empty($emp_salary)) {
        $error = "All fields are required.";
    } else {
        $sql = "UPDATE emp_table SET Emp_Name='$emp_name', Emp_Address='$emp_address', Emp_Designation='$emp_designation', Emp_Salary='$emp_salary' WHERE Emp_ID='$emp_id'";

        if ($conn->query($sql) === TRUE) {
            $msg = "Record updated successfully";
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Search Employee
if (isset($_POST['search'])) {
    $emp_id = $_POST['emp_id'];

    if (empty($emp_id)) {
        $error = "Employee ID is required to search.";
    } else {
        $sql = "SELECT * FROM emp_table WHERE Emp_ID='$emp_id'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $search = "ID: " . $row["Emp_ID"] . " <br> Name: " . $row["Emp_Name"] . " <br> Address: " . $row["Emp_Address"] . " <br> Designation: " . $row["Emp_Designation"] . " <br> Salary: " . $row["Emp_Salary"] . "<br>";
            }
        } else {
            $error = "No results found.";
        }
    }
}

// Delete Employee
if (isset($_POST['delete'])) {
    $emp_id = $_POST['emp_id'];

    if (empty($emp_id)) {
        $error = "Employee ID is required to delete.";
    } else {
        $sql = "DELETE FROM emp_table WHERE Emp_ID='$emp_id'";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                $msg = "Record deleted successfully";
            } else {
                $error = "ID: " . $emp_id . " is not found in Database";
            }
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Information</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            background-color: #D6D9DF;
            width: 30%;
            border: 1px solid grey;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px auto;

        }

        .form-container {
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #333;
            /* background-color: #66afe9;
            margin-top: 0;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            padding: 10px; */
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: #66afe9;
            outline: none;
            box-shadow: 0 0 5px rgba(102, 175, 233, 0.6);
        }

        .d-flex {
            display: flex;
            justify-content: space-between;
        }

        .btn {
            border: none;
            height: 35px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-add {
            background-color: #007bff;
            color: #fff;
        }

        .btn-add:hover {
            background-color: #0056b3;
        }

        .btn-update {
            background-color: #28a745;
            color: #fff;
        }

        .btn-update:hover {
            background-color: #218838;
        }

        .btn-search {
            background-color: #5ec2e0;
            color: #fff;
        }

        .btn-search:hover {
            background-color: #138496;
        }

        .btn-delete {
            background-color: #dc3545;
            color: #fff;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .success {
            color: #28a745;
        }

        .error {
            color: #c82333;
        }

        .search {
            background-color: #fff;
            padding: 5px;

            color: #17a2b8;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Employee Information</h2>
        <div class="form-container">
            <?php if (!empty($msg)) { ?>
                <div class="success"><?php echo $msg; ?></div>
            <?php } ?>

            <?php if (!empty($search)) { ?>
                <div class="search"><?php echo $search; ?></div>
            <?php } ?>

            <?php if (!empty($error)) { ?>
                <div class="error"><?php echo $error; ?></div>
            <?php } ?>
            <br>
            <form method="post" action="">
                <div>
                    <label for="emp_id" class="form-label">Employee ID</label>
                    <input type="text" class="form-control" id="emp_id" name="emp_id">
                </div>
                <div>
                    <label for="emp_name" class="form-label">Employee Name</label>
                    <input type="text" class="form-control" id="emp_name" name="emp_name">
                </div>
                <div>
                    <label for="emp_address" class="form-label">Employee Address</label>
                    <input type="text" class="form-control" id="emp_address" name="emp_address">
                </div>
                <div>
                    <label for="emp_designation" class="form-label">Employee Designation</label>
                    <input type="text" class="form-control" id="emp_designation" name="emp_designation">
                </div>
                <div>
                    <label for="emp_salary" class="form-label">Employee Salary</label>
                    <input type="number" class="form-control" id="emp_salary" name="emp_salary">
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-add" name="add">Add/Save</button>
                    <button type="submit" class="btn btn-update" name="update">Update</button>
                    <button type="submit" class="btn btn-search" name="search">Search</button>
                    <button type="submit" class="btn btn-delete" name="delete" onclick="return confirm('Are you sure you want to delete?');">Delete</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>