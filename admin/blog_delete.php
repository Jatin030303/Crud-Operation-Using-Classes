<?php
require '../session/session_third.php';
require '../connection/connect.php';

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];


    $sql = "DELETE FROM `blog` WHERE id = $id";
    $result = mysqli_query($db->con, $sql);

    if ($result) {
        header("location:blog_admin.php");
        exit();
    } else {
        die(mysqli_error($con));
    }
}
