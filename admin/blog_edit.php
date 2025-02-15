<?php
require '../session/session_third.php';
require '../connection/connect.php';
require '../header/header.php';
require '../class/BlogManager.php';

if (isset($_GET['edit_id'])) {
    $id = $_GET['edit_id'];
    $blogManager = new BlogManager($db->con, $id);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $blogManager->UpdateBlog($_POST, $_FILES);
    }
} else {
    echo "No blog ID provided.";
    exit();
}
?>

<style>
    <?php require '../styles/blog_edit.css'; ?>
</style>

<h1 id="heading">Hello <?php echo $_SESSION['email']; ?></h1>
<?php require '../header/display_header.php'; ?>
<h2>Edit Blog</h2>

<form method="POST" action="blog_edit.php?edit_id=<?= $id; ?>" enctype="multipart/form-data">
    <label for="title">Title</label><br>
    <input type="text" name="title" value="<?= htmlspecialchars($blogManager->old_title ?? '', ENT_QUOTES); ?>" required><br>

    <label for="section">Section</label><br>
    <textarea name="section" rows="10" required><?= htmlspecialchars($blogManager->old_section ?? '', ENT_QUOTES); ?></textarea><br>

    <label for="image">Image</label><br>
    <?php if (!empty($blogManager->old_image)): ?>
        <div>
            <img src="../images/<?php echo $blogManager->old_image; ?>" alt="Current Image" height="80" width="80">
            <p>Current Image</p>
        </div>
    <?php endif; ?><br>
    <input type="file" name="image" accept="image/*"><br>

    <label for="category">Category</label><br>
    <?php
    $blogManager->displayCategoryCheckboxes($blogManager->categories, null, 0, $blogManager->old_category);
    ?>

    <input type="submit" value="Update Blog"><br>
    <a href="blog_admin.php">Back</a><br>
    <h2><a href="admin_logout.php">Admin logout</a></h2>
</form>