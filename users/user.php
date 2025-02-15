<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}
require '../connection/connect.php';
require '../header/header.php';
?>

<style>
    <?php require '../styles/user.css' ?>
</style>

<h1 class="heading">User Dashboard</h1>
<h2 class="heading">Hello <?php echo $_SESSION['email']; ?></h2>
<?php require "../header/user_display.php"  ?>

<h3><a href="logout.php">Logout </a><br></h3>