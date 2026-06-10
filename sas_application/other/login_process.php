
    <?php
    // Include the database connection file
    include '../other/db_connection.php';

    // Start a session
    session_start();

    // Check if form data is set
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Define roles and respective dashboard pages
        $roles = [
            'students' => '../student/display_course.php',
            'teachers' => '../teacher/teacher_dashboard.php',
            'parents' => '../parent/parent_dashboard.php',
            'admin' => '../admin/admin_dashboard.php'
        ];

        foreach ($roles as $role => $redirect_page) {
            // SQL query with the status check
            $sql = "SELECT * FROM $role WHERE email = ? AND status= 'approved'";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $user_data = $result->fetch_assoc();

                    if (password_verify($password, $user_data['password'])) {
                        $_SESSION['user_id'] = $user_data['id'];
                        $_SESSION['role'] = $role;

                        header("Location: $redirect_page");
                        exit();
                    }
                }
                $stmt->close();
            } else {
                echo "Error in preparing the statement.";
            }
        }

        echo "Invalid email or password.";
    }

    $conn->close();
    ?>
