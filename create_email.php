<?php

require 'vendor/autoload.php';

$config = require '../email/config/config.php';
use Email\Manager\classes\Email;

$Cpanel = $config['cpanel'];
$host = $Cpanel['host'];
$user = $Cpanel['username'];
$pass = $Cpanel['password'];

try {
    // Check if email, password, and domain are provided
    if (!isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['domain'])) {
        throw new Exception('Email, password, and domain parameters are required.');
    }

    // Get email, password, and domain from the form
    $email = $_POST['email'];
    $password = $_POST['password'];
    $domain = $_POST['domain'];

    // Initialize Email class
    $emailManager = new Email($user, $pass, $host);

    // Create email
    $emailGone = $emailManager->createEmail($email, $password, $domain);

    if($emailGone){
        // Redirect back to the email list page
        header("Location: list_emails.php?domain=$domain");
        exit();         
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

// Set the domain
$domain = $_GET['domain'] ?? 'ivotecglobalservices.com';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Email</title>
</head>
<body>
    <h2>Create Email Account</h2>
    <form action="create_email.php" method="post">
        Email: <input type="text" name="local_part" required> <?php echo '@' . $domain; ?>
        Password: <input type="password" name="password" required><br>
        <input type="hidden" name="domain" value="<?php echo htmlspecialchars($domain); ?>"><br>
        <input type="submit" value="Create Email">
    </form>
</body>
</html>
