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

    // Delete email
    $emailManager->deleteEmail($email, $domain);

    // Redirect back to the email list page
    header("Location: list_emails.php?domain=$domain");
    exit();
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
