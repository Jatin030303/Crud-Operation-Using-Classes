<?php
require '../session/session_third.php';
require '../connection/connect.php';
require '../header/header.php';
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
    <?php require '../styles/category_add.css'; ?>
</style>

<h1 id="heading">Hello <?php echo $_SESSION['email']; ?></h1>
<?php require '../header/display_header.php'; ?>

<h1 id="id1">Add Category and Subcategory</h1>
<form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
    <label for="category_name">Category Name</label><br>
    <input type="text" maxlength="50" name="category_name" placeholder="Enter category name" value="<?= $categoryManager->category_name ?>"><br>
    <span class="color"><?= $categoryManager->error ?></span><br>

    <label for="description">Description</label><br>
    <textarea name="description" maxlength="300" rows="3" placeholder="Enter a description (optional)"></textarea><br>

    <label for="parent_id">Parent Category</label><br>
    <select name="parent_id">
        <option value="">None (Main Category)</option>
        <?php $categoryManager->displayCategoriesWithIndentation(); ?>
    </select><br><br>

    <button type="submit">Add Category</button>
    <a href="category.php">Back</a><br>
    <h2><a href="admin_logout.php"> Admin logout</a></h2>
</form>