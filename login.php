<?php

include 'vendor/autoload.php';

use Email\Manager\classes\AdminLogin;

// session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adminEmail = 'manuelseffe@gmail.com';
    $adminPassword = '#newEmail01';
    $cpanelHost = 'ivotecglobalservices.com';

    $emailInput = $_POST['email'];
    $passwordInput = $_POST['password'];

    if ($adminEmail === $emailInput && $adminPassword === $passwordInput) {
        $_SESSION['loggedin'] = true;
        $_SESSION['adminEmail'] = $adminEmail;
        $_SESSION['adminPassword'] = $adminPassword;
        $_SESSION['cpanelHost'] = $cpanelHost;

        // Redirect to the index page
        header("Location: index.php");
        exit();
    } else {
        $error = 'Invalid login credentials';
    }
}

if (!isset($_SESSION['loggedin'])) {
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
</head>
<body>
    <form method="post" action="">
        <label>Email: <input type="text" name="email"></label><br>
        <label>Password: <input type="password" name="password"></label><br>
        <input type="submit" value="Login">
    </form>
    <?php if (isset($error)) echo $error; ?>
</body>
</html>

<?php
} else {
    header("Location: index.php");
    exit();
}
?>
