<?php

require 'vendor/autoload.php';

$config = require '../email/config/config.php';
use Email\Manager\classes\Email;

$Cpanel = $config['cpanel'];
$host = $Cpanel['host'];
$user = $Cpanel['username'];
$pass = $Cpanel['password'];

try {
    // Initialize Email class with correct parameters order
    $emailManager = new Email($user, $pass, $host);

    // List emails for the specified domain
    $emails = $emailManager->listEmail($host);
    

    // Print the list of emails
    // echo "<h2>List of Emails for $host:</h2>";
   
    echo '<ul>';
    foreach ($emails as $email) {
        echo '<li>' . htmlspecialchars($email['email']);
        echo ' - <a href="edit_password.php?email=' . urlencode($email['email']) . '&domain=' . urlencode($host) . '">Edit Password</a>';
        echo ' | <a href="delete_email.php?email=' . urlencode($email['email']) . '&domain=' . urlencode($host) . '">Delete</a>';
        echo '</li>';
    }
    echo '</ul>';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}