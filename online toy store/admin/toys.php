<?php require('../header.php'); 
if (($_SESSION['isAdmin']) == NULL) {
    header('location:adminlogin.php'); // redirect to login page if user details is not set in sessions
}?>
<style>
    /* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons that are used to open the tab content */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}
</style>
<script type="text/javascript">
function openCity(evt, cityName) {
  // Declare all variables
  var i, tabcontent, tablinks;

  // Get all elements with class="tabcontent" and hide them
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  // Get all elements with class="tablinks" and remove the class "active"
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  // Show the current tab, and add an "active" class to the button that opened the tab
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}
</script>

<!-- Tab links -->
<div class="tab">
  <button class="tablinks" onclick="openCity(event, 'London')">Add Toys</button>
  <button class="tablinks" onclick="openCity(event, 'Paris')">Update Toys</button>
  <button class="tablinks" onclick="openCity(event, 'Tokyo')">Delete Toys</button>
</div>

<!-- Tab content -->
<div id="London" class="tabcontent">
<div><h2>Add Toys</h2></div>
    <hr>
    <form action="uploadtoys.php" method="post" enctype="multipart/form-data" class="d-grid gap-2">
        <div class="col-lg-12 form-group ">
            <table>
                <tr>
                    <th>Toy ID:</th>
                    <td><input type="text" name="toyid" class="form-control" required></td>
                </tr>
                <tr>
                    <th>Toy Name:</th>
                    <td><input type="text" name="name" class="form-control" required></td>
                </tr>
                <tr>
                    <th>Toy Price:</th>
                    <td><input type="number" name="price" class="form-control" required></td>
                </tr>
                <tr>
                    <th>Toy Picture:</th>
                    <td><input type="file" name="fileToUpload" id="fileToUpload" class="form-control" required></td>
                </tr>
                <tr>
                    <td></td>
                    <td><button type="submit" name="submit" class="btn btn-outline-primary me-2">Add Toy</button></td>
                </tr>
            </table>
        </div>
    </form>
</div>

<div id="Paris" class="tabcontent">
        <h2>Update Toys</h2>
    <hr>
    <form method="post" action="" class="d-grid gap-1">
        <table>
            <tr>
                <th>Toy ID:</th>
                <td><input type="text" name="toyid" formmethod="post" class="form-control" placeholder="Enter Toy ID to search" required></td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit" name="submit" class="btn btn-sm btn-outline-primary me-2">Search</button></td>
            </tr>
        </table>
    </form>
    <hr>
</div>

<?php
require('../connectToMysql.php');
if (isset($_POST['toyid'])) {
    $toyid = $_POST["toyid"];
    $search = "SELECT * FROM toys WHERE toyid='$toyid'";
    $res = mysqli_query($connecti, $search);

    if ($row = mysqli_fetch_assoc($res)) { ?>
        <br><br>
        <div class="container col-lg-6 justify-content-center align-items-center">
            <h5>Following record found</h5><br>
            <form class="d-grid gap-2" method="post" action="uploadtoys1.php" enctype="multipart/form-data">
                <div class="col-lg-12 form-group d-grid gap-2">
                    <table>
                        <tr>
                            <th>Toy ID:</th>
                            <td><input type="text" name="toyid" class="form-control" value="<?php echo $row['toyid']; ?>" /></td>
                        </tr>
                        <tr>
                            <th>Toy Name:</th>
                            <td><input type="text" name="name" class="form-control" value="<?php echo $row['toyname']; ?>" /></td>
                        </tr>
                        <tr>
                            <th>Toy Price:</th>
                            <td><input type="text" name="price" class="form-control" value="<?php echo $row['toyprice']; ?>"></td>
                        </tr>
                        <tr>
                            <th>Toy Image:</th>
                            <td><input type="file" name="fileToUpload" id="fileToUpload" class="form-control"></td>
                            <td><img id="image" src="<?php echo $row['toypicture']; ?>" alt="" width="100px" /></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input type="submit" class="btn btn-outline-primary me-2" name="submit" value="Update"></td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
<?php
    } else {
        echo '<div class="container"><br><h5>Entered record does not exist.</h5></div>';
    }
} else {
    echo '<div class="container"><br><h5></h5></div>';
}
?>
</div>

<div id="Tokyo" class="tabcontent">
        <h2>Delete Toys</h2>

    <hr>
    <form method="post" action="" class="d-grid gap-2">
        <table>
            <tr>
                <th>Toy ID:</th>
                <td><input type="text" name="toyid" formmethod="post" class="form-control" placeholder="Enter Toy ID to search" required></td>
            </tr>
            <tr>
                <td></td>
                <td><button type="submit" name="submit" class="btn btn-sm btn-outline-primary me-2">Search</button></td>
            </tr>
        </table>
    </form>
    <hr>
</div>
<?php
require('../connectToMysql.php');
if (isset($_POST['toyid'])) {
    $toyid = $_POST["toyid"];
    $search = "SELECT * FROM toys WHERE toyid='$toyid'";
    $res = mysqli_query($connecti, $search);

    if ($row = mysqli_fetch_assoc($res)) {
        //Display Toy Record Table
        echo '<div class="container"><h5>Following record found</h5><br><br><form class="row" action="deletedtoys.php" method="post">
            <table  class="table table-bordered" align="center" width="50%" border="1">
            <tr>
                <th>Toy ID</th>
                <th>Toy Name</th>
                <th>Toy Price</th>
                <th>Toy Picture</th>
                <th>Actions</th>
            </tr>
            <tr> 
                <td><input type=hidden name="toyid[]" value="' . $row['toyid'] . '">' . $row['toyid'] . '</td>
                <td>' . $row['toyname'] . '</td>
                <td>Rs. ' . $row['toyprice'] . '</td>
                <td width="25%"><img src="' . $row['toypicture'] . '" width="150" height="150"></td>
                <td><button type="submit" value="submit" class="btn btn-danger me-2" required>Delete</button></td>
            </tr>
        </table>
        </form></div>';
    } else {
        echo '<br><div class="container"><h5>Entered record does not exist.</h5></div>';
    }
} else {
    echo '<br><div class="container"><h5></h5></div>';
}
?>
</div>

</div>
</div>


