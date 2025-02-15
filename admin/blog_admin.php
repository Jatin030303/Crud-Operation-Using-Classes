<?php
require '../session/session_third.php';
require '../connection/connect.php';
require '../header/header.php';
require '../class/BlogManager.php';
?>

<style>
    <?php require '../styles/blog_admin.css'; ?>
</style>

<h1>Welcome, <?= $_SESSION['email']; ?>!</h1>
<div class="container">
    <a href="category.php">
        <h1>Categories</h1>
    </a>
    <a href="blog_admin.php">
        <h1>Blogs</h1>
    </a>
</div>

<h2>Blog Panel</h2>
<div>
    <a href="blog_add.php"><button>Add Blog</button></a>
</div>
<table class="table">
    <thead>
        <tr>
            <th>Blog No</th>
            <th>Title</th>
            <th>Section</th>
            <th>Image</th>
            <th>Categories</th>
            <th>Operations</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (!empty($blogManager->blogs)) {
            foreach ($blogManager->blogs as $blogData) {
                $id = $blogData['id'];
                $title = $blogData['title'];
                $description = $blogData['description'];
                $image = $blogData['image'];
                $categoriesString = $blogManager->displayCategories($blogData['category_ids']);

                echo "<tr>
                    <td>{$id}</td>
                    <td>{$title}</td>
                    <td>{$description}</td>
                    <td><img src='../images/{$image}' alt='Blog Image' height='80' width='80'></td>
                    <td>{$categoriesString}</td>
                    <td>
                    <a href='blog_edit.php?edit_id={$id}'><button>Edit</button></a>
                    <a href='blog_delete.php?delete_id={$id}' onclick='return confirm(\"Are you sure you want to delete this blog?\");'><button>Delete</button></a>
                </td>
            </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No blogs available</td></tr>";
        }
        ?>
    </tbody>
</table>

<div class="logout">
    <a href="admin_data.php">Back</a><br>
    <h2><a href="admin_logout.php">Admin logout</a></h2>
</div>