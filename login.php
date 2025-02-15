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
    <?php require 'styles/login.css'; ?>
</style>

<?php if ($user->shouldRedirectUser()) {
    header('location:./users/user.php');
} ?>

<?php if ($user->shouldRedirectAdmin()) {
    header('location:./admin/admin_data.php');
} ?>

<h1>Login</h1>
<form action="login.php" method="POST">
    <label for="email">Email *</label><br>
    <input type="text" maxlength="55" placeholder="Enter Email" name="email" value="<?= $user->email ?? '' ?>"><br>
    <span class="color"><?= $error['email'] ?? ''; ?></span><br>
    <label for="password">Password *</label><br>
    <input type="password" maxlength="30" placeholder="Enter Password" name="password"><br>
    <span class="color"><?= $error['password'] ?? ''; ?></span><br>
    <input type="submit" value="Login">
    <a href="signup.php" class="dec">Register new user</a><br>
</form>

<?php if ($alertMessage) {
    echo '<b>' . $alertMessage . '</b>';
} ?>