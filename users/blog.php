<?php
require '../session/session_third.php';
require '../connection/connect.php';
require '../header/header.php';
require '../class/BlogManager.php';
?>

<style>
    <?php require '../styles/blog.css'; ?>
</style>

<h1 id="heading">Hello <?php echo $_SESSION['email']; ?></h1>

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

<h1>Blog Section</h1>
<a href="user_add_blog.php"><button>Add blogs</button></a><br>
<a href="user.php" class="he">Back</a><br>
<h2><a href="logout.php">Logout</a></h2>

<form method="POST" action="process_selected_blogs.php">
    <div class="container">
        <?php
        $blogManager->displayBlog();
        ?>
    </div>
</form>
</div>