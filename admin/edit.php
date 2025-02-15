<?php
require '../session/session_third.php';
require "../header/header.php";
require '../connection/connect.php';
require '../class/CategoryManager.php';

if (isset($_GET['edit_id'])) {
    $id = $_GET['edit_id'];
    $categoryManager = new CategoryManager($db->con, $id);
} else {
    $categoryManager = new CategoryManager($db->con);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoryManager->validateCategoryData($_POST); // Validate and save the data
}
?>

<style>
    <?php require '../styles/category_edit.css'; ?>
</style>

<h1>Edit Category</h1>
<h1 id="heading">Hello <?= htmlspecialchars($_SESSION['email']); ?></h1>

<form method="POST" action="">
    <label for="category_name">Category Name</label><br>
    <input type="text" maxlength="50" name="category_name" placeholder="Enter category name"
        value="<?= htmlspecialchars($categoryManager->category_name); ?>"><br>
    <span class="color"><?= htmlspecialchars($categoryManager->error); ?></span><br>

    <label for="description">Description</label><br>
    <textarea name="description" maxlength="255" rows="3" placeholder="Enter a description (optional)">
        <?= htmlspecialchars($categoryManager->description); ?>
    </textarea><br>

    <label for="parent_id">Parent Category</label><br>
    <select name="parent_id">
        <option value="">None (Main Category)</option>
        <?php
        $categoryManager->displayCategoriesWithIndentation();
        ?>
    </select><br>

    <button type="submit">Update</button><br>
    <a href="category.php">Back</a>
    <h2><a href="admin_logout.php">Admin logout</a></h2>
</form>