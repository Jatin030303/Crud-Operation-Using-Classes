<?php
include './connection/connect.php';
include './header/header.php';
include './class/BlogManager.php';

$categoryId = $_GET['category_id'] ?? 0;

$blogManager = new BlogManager($db->con);

// Fetch categories and blogs
$categoryResults = $blogManager->getAllCategories();
$categories = [];
while ($row = $categoryResults->fetch_assoc()) {
    $categories[$row['parent_id']][] = $row;
}
$parentCategories = $blogManager->findParentCategories($categories, $categoryId);
$blogsResult = $blogManager->getBlogsByCategory($categoryId);
?>

<style>
    <?php include './styles/blog_public.css'; ?>
</style>

<h1 style="color: skyblue;">Blog Public</h1>
<h3><a href="public_listing.php">Back</a></h3>

<?php

$blogManager->display_Public_Categories($categories, $categoryId, $parentCategories);
?>

<div class="boxes">
    <ul>
        <?php
        if ($blogsResult->num_rows > 0) {
            while ($row = $blogsResult->fetch_assoc()) {
                echo '<li>';
                echo '<h1>Title -</h1>';
                echo '<h2>' . $row['title'] . '</h2>';
                echo '<h1>Description -</h1>';
                echo '<p>' . $row['description'] . '</p>';
                echo '<h1>Image -</h1>';
                echo '<img src="./images/' . $row['image'] . '" alt="Blog Image">';
                echo '</li>';
            }
        } else {
            echo '<p>No blogs available for this category.</p>';
        }
        ?>
    </ul>
</div>