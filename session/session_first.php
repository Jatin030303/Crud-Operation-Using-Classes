<?php
session_start();
if (isset($_SESSION['name'])) {
    header("Location: ../user/user.php");
    exit();
}
