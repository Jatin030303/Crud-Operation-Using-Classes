<?php

require '../session/session_third.php';
require '../connection/connect.php';
require '../header/header.php';
require '../class/CategoryManager.php';
?>

<style>
    <?php require '../styles/category.css'; ?>
</style>

<h1 id="heading">Hello <?php echo $_SESSION['email'] ?></h1>
<?php require '../header/display_header.php'; ?>
<h1>Category Panel</h1>
<a href="add.php"><button>Add</button></a>
<table class="table">
    <thead>
        <tr>
            <th scope="col">S.No</th>
            <th scope="col">Category Name</th>
            <th scope="col">Description</th>
            <th scope="col">Created</th>
            <th scope="col">Updated</th>
            <th scope="col">Operation</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($categoryManager->hasCategories()) {
            $sno = 1;
            $categoryManager->displayCategories(null, 0, $sno); // values assigned
        } else {
            echo '<tr><td colspan="6">No Categories</td></tr>';
        }
        ?>
    </tbody>
</table>
<a href="admin_data.php">Back</a><br>
<h2><a href="admin_logout.php">Admin logout</a></h2>