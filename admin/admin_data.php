<?php
require '../session/session_third.php';
require '../connection/connect.php';
require '../header/header.php';
?>
<style>
    <?php require '../styles/heading.css'; ?>
</style>

<h1 id="heading">Hello <?php echo $_SESSION['email'] ?></h1>
<div class="container">
    <?php require '../header/display_header.php'; ?>
    <ul>
</div>
<h2><a href="admin_logout.php">Admin logout</a></h2>