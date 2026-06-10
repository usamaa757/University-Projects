<?php
include("config.php");
include("navbar.php");
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


$user_id = $_SESSION['user_id'];

?>
<div class="container">

    <h3>Your Furniture List</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM furniture");
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['name']}</td>
                    <td>\${$row['price']}</td>
                    <td>{$row['description']}</td>
                    <td class='action-links'>
                        <a href='furniture_edit.php?id={$row['id']}'>Edit</a>
                        <a href='furniture.php?delete={$row['id']}' onclick=\"return confirm('Delete this item?');\">Delete</a>
                    </td>
                  </tr>";
        }
        ?>
    </table>
</div>

</body>

</html>