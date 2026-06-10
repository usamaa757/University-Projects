<?php session_start();
require('../connectToMysql.php');

//Add toys
if (isset($_POST['addtoys'])) {
    $target_dir = "C:/xampp/htdocs/project2/public/images/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
    // Check if image file is a actual image or not
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
        //"File is an image"
        $uploadOk = 1;
    } else {
        echo "File is not an image." . $check["mime"] . ".";
        $uploadOk = 0;
    }
    if ($uploadOk == 1) {
        $toyid = $_POST['toyid'];
        $toyname = $_POST['name'];
        $price = $_POST['price'];
        $cat = $_POST['category'];
        $image = basename($_FILES["fileToUpload"]["name"]);

        $sql = "INSERT INTO toys(toyid, toyname, toyprice, toypicture,catid) VALUES ( '$toyid', '$toyname', '$price', 'images/$image', '$cat')";

        if (mysqli_query($connecti, $sql)) {
            header('Refresh:0; url=addtoys.php');
            echo '<script>alert("Record inserted successfully!");</script>';
        } else {
            header('Refresh:0; url=addtoys.php');
            echo '<script>alert("Failed to insert record!");</script>';
        }
    } else {
        echo '<script>alert("Error uploading image!");</script>';
    }
    exit();
}
//Update toys
if (isset($_POST['updatetoys'])) {
    if (isset($_FILES["fileToUpload"]) && !empty($_FILES["fileToUpload"]["name"])) {
        $target_dir = "C:/xampp/htdocs/project2/public/images/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        // Check if image file is a actual image or not
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check !== false) {
            //"File is an image"
            $uploadOk = 1;
        } else {
            echo "File is not an image." . $check["mime"] . ".";
            $uploadOk = 0;
        }
    }
    if (isset($_FILES["fileToUpload"]) && !empty($_FILES["fileToUpload"]["name"])) {
        if ($uploadOk == 1) {
            $toyid = $_POST['toyid'];
            $toyname = $_POST['name'];
            $price = $_POST['price'];
            $image = basename($_FILES["fileToUpload"]["name"]);

            $sql = "UPDATE toys SET toyid='$toyid', toyname='$toyname', toyprice='$price', toypicture='images/$image' WHERE toyid='$toyid'";

            if (mysqli_query($connecti, $sql)) {
                header('Refresh:0; url=updatetoys.php');
                echo '<script>alert("Record updated successfully!");</script>';
            } else {
                header('Refresh:0; url=updatetoys.php');
                echo '<script>alert("Failed to update record!");</script>';
            }
        }
    } else {
        $toyid = $_POST['toyid'];
        $toyname = $_POST['name'];
        $price = $_POST['price'];
        $sql = "UPDATE toys SET toyid='$toyid', toyname='$toyname', toyprice='$price' WHERE toyid='$toyid' ";

        if (mysqli_query($connecti, $sql)) {
            header('Refresh:0; url=updatetoys.php');
            echo '<script>alert("Record updated successfully!");</script>';
        } else {
            header('Refresh:0; url=updatetoys.php');
            echo '<script>alert("Failed to update record!");</script>';
        }
    }
    exit();
}
//Delete toys
if (isset($_POST['deletetoys'])) {
    $toyid = $_POST['toyid'];

    $sql = "DELETE FROM toys WHERE toyid='$toyid'";
    if (mysqli_query($connecti, $sql)) {
        header('Refresh:0; url=deletetoys.php');
        echo '<script>alert("Toy Deleted Successfully!");</script>';
    } else {
        header('Refresh:0; url=deletetoys.php');
        echo '<script>alert("Failed to delete!");</script>';
    }
}
?>