<?php session_start();
require('../connectToMysql.php');

//Add Category
if (isset($_POST['addcat'])) {
    $catid = $_POST['catid'];
    $catname = $_POST['catname'];

    $sql = "INSERT INTO category(catid, catname) VALUES ( '$catid', '$catname')";
    if (mysqli_query($connecti, $sql)) {
        header('Refresh:0; url=addcat.php');
        echo '<script>alert("Record inserted successfully!");</script>';
    } else {
        header('Refresh:0; url=addcat.php');
        echo '<script>alert("Failed to insert record!");</script>';
    }
    exit();
}
//Update Category
if (isset($_POST['updatecat'])) {
    $catid = $_POST['catid'];
    $catname = $_POST['catname'];

    $sql = "UPDATE category SET catid='$catid', catname='$catname' WHERE catid='$catid' ";

    if (mysqli_query($connecti, $sql)) {
        header('Refresh:0; url=addcat.php');
        echo '<script>alert("Record updated successfully!");</script>';
    } else {
        header('Refresh:0; url=addcat.php');
        echo '<script>alert("Failed to update record!");</script>';
    }
    exit();
}
//Delete Category
if (isset($_POST['deletecat'])) {
    $catid = $_POST['catid'];

    $sql = "DELETE FROM category WHERE catid='$catid'";
    if (mysqli_query($connecti, $sql)) {
        header('Refresh:0; url=addcat.php');
        echo '<script>alert("Record Deleted Successfully!");</script>';
    } else {
        header('Refresh:0; url=addcat.php');
        echo '<script>alert("Failed to delete!");</script>';
    }
}
?>