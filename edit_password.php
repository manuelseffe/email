<?php

require 'vendor/autoload.php';

$config = require '../email/config/config.php';
use Email\Manager\classes\Email;

$Cpanel = $config['cpanel'];
$host = $Cpanel['host'];
$user = $Cpanel['username'];
$pass = $Cpanel['password'];

try {
    // Check if email and domain are provided
    if (!isset($_GET['email']) || !isset($_GET['domain'])) {
        throw new Exception('Email and domain parameters are required.');
    }

    // Get email and domain from the query string
    $email = $_GET['email'];
    $domain = $_GET['domain'];

    // Initialize Email class
    $emailManager = new Email($user, $pass, $host);

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if new password is provided
        if (!isset($_POST['new_password'])) {
            throw new Exception('New password is required.');
        }

        // Get new password
        $newPassword = $_POST['new_password'];

        // Update password
        $emailManager->updatePassword($email, $newPassword, $domain);

        // Redirect back to the email list page
        header("Location: list_emails.php?domain=$domain");
        exit();
    }

    // Display form to edit password
    echo "<h2>Edit Password for $email</h2>";
    echo '<form method="post">';
    echo 'New Password: <input type="password" name="new_password" required><br>';
    echo '<input type="submit" value="Update Password">';
    echo '</form>';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}