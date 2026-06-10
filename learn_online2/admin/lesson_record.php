<?php
session_start();

    require('header.php');
?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Lesson's Record</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>S No</th>
                                    <th>Course Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include "../include_files/db_connection.php";

                                $query = "SELECT DISTINCT courses.course_id, courses.course_name 
                                          FROM lessons 
                                          INNER JOIN courses ON lessons.course_id = courses.course_id
                                          GROUP BY courses.course_id, courses.course_name";

                                $result = mysqli_query($conn, $query);

                                if ($result) {
                                    $fetch_lesson = array();
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $fetch_lesson[] = $row;
                                    }

                                    mysqli_free_result($result);
                                } else {
                                    echo "<tr><td colspan='3'>Error: " . mysqli_error($conn) . "</td></tr>";
                                }

                                mysqli_close($conn);

                                if ($fetch_lesson) {
                                    $sNo = 1;
                                    foreach ($fetch_lesson as $row) {
                                        echo "<tr>";
                                        echo "<td>" . $sNo++ . "</td>";
                                        echo "<td>" . $row['course_name'] . "</td>";
                                        echo "<td>";
                                        echo "<button class='btn btn-success btn-sm' onclick=\"window.location.href='lesson_list.php?course_id=" . $row['course_id'] . "'\">View Lesson List</button>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3'>No lesson recorded</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

