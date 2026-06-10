<?php
include "header.php";
include "../db_connection.php"; // Include database connection

$student_name = $_SESSION['student_name'];
$student_id = $_SESSION['student_id'];
$degree = $_SESSION['degree'];
$semester = $_SESSION['semester'];

// Fetch subjects for the student based on student_id
$query = "SELECT s.subject_name, s.subject_id 
          FROM subjects s 
          INNER JOIN student_subjects ss ON s.subject_id = ss.subject_id 
          WHERE ss.student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$subjects = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="container-xxl py-2">
    <div class="container">
        <!-- Reduced padding on the left -->
        <div class="wow fadeInUp text-center" data-wow-delay="0.1s">
            <h1 class="mb-3 text-primary">Hello, <?php echo htmlspecialchars($student_name); ?>!</h1>
            <p class="section-title text-secondary">
                <span class="d-block">Degree: <?php echo htmlspecialchars($degree); ?></span>
                <span class="d-block">Semester: <?php echo htmlspecialchars($semester); ?></span>
            </p>
        </div>

        <!-- Updated HTML with Bootstrap and custom CSS -->
        <div class="container py-5">
            <div class="row row-cols-1 row-cols-md-2 g-3 subject-container">
                <!-- Changed row-cols to 2 and reduced gap -->
                <?php foreach ($subjects as $subject): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm" style="width: 90%; margin-left: auto; margin-right: auto;">
                        <!-- Increased card width and centered -->
                        <div>
                            <h3 class="card-title bg-primary p-2 text-white">
                                <?php echo htmlspecialchars($subject['subject_name']); ?>
                            </h3>
                        </div>
                        <div class="card-body text-center">
                            <div class="d-flex justify-content-around my-4 text-dark">

                                <div class="inline text-center">
                                    <a href="mid_term.php?subject_id=<?php echo $subject['subject_id']; ?>">
                                        <img class="icon" src="../img/note.png" alt="Quiz Icon">
                                    </a>
                                    <p>Mid Term</p>
                                </div>
                                <div class="inline text-center">
                                    <a href="final_term.php?subject_id=<?php echo $subject['subject_id']; ?>">
                                        <img class="icon" src="../img/note.png" alt="Lecture Icon">
                                    </a>
                                    <p>Final Term</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>
</body>

</html>