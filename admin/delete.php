<?php
require '../connection/connect.php';
if ($_GET['delete_id']) {
    $id = $_GET['delete_id'];
    $sql = "DELETE FROM `categories` WHERE id = $id";
    $result = mysqli_query($db->con, $sql);
    if ($result) {
        header("Location: category.php");
        exit();
    } else {
        die(mysqli_error($db->con));
    }
} else {
    echo "No data found.";
}
