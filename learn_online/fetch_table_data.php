<?php

function fetchTableData($tableName)
{
   include 'db_connection.php';

    // Fetch data from the database
    $mysql = "SELECT * FROM $tableName";
    $query_result = mysqli_query($conn, $mysql);

    // Create array to store fetched data from table
    $fetch_data = array();

    // Initialize serial number
  
    if (mysqli_num_rows($query_result) > 0) {
        while ($row = mysqli_fetch_assoc($query_result)) {
          
            $fetch_data[] = $row;
           
        }
    }

    // Close database connection
    mysqli_close($conn);

    return $fetch_data;
}

?>
