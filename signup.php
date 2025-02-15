<?php
require_once 'session/session_first.php';
require_once 'connection/connect.php';
require_once 'header/header.php';
require './class/UserAuth.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user->login($_POST);
    $user->signUp($_POST);
}
$error = $user->getErrors();
$alertMessage = $user->getAlertMessage();
$redirect = $user->shouldRedirect();
?>

<style>
    <?php require 'styles/signup.css'; ?>
</style>

<?php if ($redirect): ?>
    <script>
        setTimeout(() => {
            window.location.href = "login.php";
        }, 3000);
    </script>
<?php endif; ?>

<form action="signup.php" method="POST">
    <label for="name">Name *</label><br>
    <input type="text" placeholder="Enter Name" name="name" value="<?= $user->name ?? ''; ?>" maxlength="25"><br>
    <span class="color"><?= $error['name'] ?? ''; ?></span><br>

    <label for="email">Email *</label><br>
    <input type="text" placeholder="Enter Email" name="email" value="<?= $user->email ?? ''; ?>"><br>
    <span class="color"><?= $error['email'] ?? ''; ?></span><br>

    <label for="password">Password *</label><br>
    <input type="password" placeholder="Enter Password" name="password" value="<?= $user->password ?? ''; ?>"><br>
    <span class="color"><?= $error['password'] ?? ''; ?></span><br>

    <label for="confirm">Confirm Password *</label><br>
    <input type="password" placeholder="Confirm Password" name="confirm" value="<?= $user->c_password ?? ''; ?>"><br>
    <span class="color"><?= $error['confirm'] ?? ''; ?></span><br>

    <input type="submit" value="Create account">
    <a href="login.php" class="dec">Login</a>
</form>

<?php if ($alertMessage): ?>
    <p class="alert"><?= $alertMessage; ?></p>
<?php endif; ?>