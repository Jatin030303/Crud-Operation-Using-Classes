<?php
require '../session/session_third.php';
require '../connection/connect.php';
require '../header/header.php';
require '../class/BlogManager.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $blogManager->validateAndSave($_POST, $_FILES);
}

?>
<style>
    <?php require '../styles/blog_add.css'; ?>
</style>
<h1 id="heading">Hello <?php echo $_SESSION['email']; ?></h1>
<?php require '../header/display_header.php'; ?>
<h1>Add Blog</h1>
<form method="POST" action="blog_add.php" enctype="multipart/form-data">
    <label for="title">Title *</label><br>
    <input type="text" placeholder="Enter Title" maxlength="100" name="title" value="<?= $blogManager->old_title; ?>"><br>
    <span class="error"><?= $blogManager->titleErr; ?></span><br>

    <label for="section">Section *</label><br>
    <textarea name="section" placeholder="Write something" maxlength="1000" rows="5"><?= $blogManager->section; ?></textarea><br>
    <span class="error"><?= $blogManager->sectionErr; ?></span><br>

    <label for="image">Add Image *</label><br>
    <input type="file" name="image" accept="image/*"><br><br>
    <span class="error"><?= $blogManager->fileErr; ?></span><br>

    <h3>Select Categories *</h3>
    <div class="bolf">
        <?php

        if (!empty($blogManager->categories)) {

            $blogManager->displayCategoryCheckboxes($blogManager->categories);
        } else {
            echo '<p>No categories available.</p>';
        }
        ?>
        <span class="error"><?= $blogManager->categoryErr; ?></span><br><br>
    </div>

    <input type="submit" value="Add Blog">
    <a href="blog_admin.php">Back</a><br>
    <h2><a href="admin_logout.php"> Admin logout</a></h2>
</form>