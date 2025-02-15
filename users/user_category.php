<?php
require '../session/session_third.php';
require '../connection/connect.php';
require '../header/header.php';
require '../class/CategoryManager.php';


?>

<style>
    <?php require '../styles/user_category.css'; ?>
</style>

<h1 id="heading">Hello <?= $_SESSION['email']; ?></h1>
<div class="container">
    <div>
        <a href="user_category.php">
            <h1 class="h1">Category</h1>
        </a>
    </div>
    <div>
        <a href="blog.php">
            <h1 class="h1">Blog</h1>
        </a>
    </div>
</div>
<h1>User Panel</h1>
<a href="user.php" class="he">Back</a><br>
<table class="table">
    <thead>
        <tr>
            <th scope="col">S.No</th>
            <th scope="col">Category Name</th>
            <th scope="col">Description</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($categoryManager->hasCategories()) {
            $sno = 1; // Initialize serial number
            $categoryManager->displayCategoriesWithSerialNumbers(null, 0, $sno);
        } else {
            echo '<tr><td colspan="3">No Categories</td></tr>';
        }
        ?>
    </tbody>
</table>

<h2><a href="logout.php">Logout</a></h2>