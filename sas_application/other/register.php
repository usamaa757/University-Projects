<?php
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #b5dd7c;
            
            
        }

        h2 {
            margin-top: 5px;
            text-align: center;
        }

        form {
            max-width: 400px;
            margin:  auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .radio-option {
            display: flex;
            align-items: center;
            margin-left: 140px;
        }

        .radio-option input[type="radio"] {
            margin-right: 10px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color:  #0f582d;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <br><br><br>

    <form action="register_process.php" method="POST">
        <h2>Register Yourself</h2><br>
        <label for="role"><b>Please first select the role you are applying for:</b></label><br><br><br>
        <div class="radio-option">
            <input type="radio" id="student" name="role" value="student" required>
            <label for="student">Student</label>
        </div><br>
        <div class="radio-option">
            <input type="radio" id="teacher" name="role" value="teacher" required>
            <label for="teacher">Teacher</label>
        </div><br>
        <div class="radio-option">
            <input type="radio" id="parent" name="role" value="parent" required>
            <label for="parent">Parent</label>
        </div>
        <br><br>
        <input type="submit" value="Submit">
    </form>
</body>
</html>
