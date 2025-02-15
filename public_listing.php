<?php
include './connection/connect.php';
include './header/header.php';
include './class/CategoryManager.php';




?>

<style>
    .Cat {
        text-align: center;
        color: skyblue;
    }

    .Home {
        margin-top: 60px;
        text-decoration: none;
    }
</style>

<h1 class="Cat">Public Listing Categories</h1>
<ol>
    <?php

    $categoryManager->display_public_Categories(null, $categoryManager->categories);
    ?>
</ol>
<h3 class="Home"><a href="signup.php" style="text-decoration: none;">Go to registration page</a></h3>