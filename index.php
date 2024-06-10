<?php
require 'vendor/autoload.php';

use Email\Manager\classes\Email;

$email = new Email();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Management</title>
    <script>
        async function fetchEmails(domain) {
            const response = await fetch(`index.php?domain=${domain}`);
            const emails = await response.json();
            const emailList = document.getElementById('emailList');
            emailList.innerHTML = '';
            emails.forEach(email => {
                const listItem = document.createElement('li');
                listItem.textContent = email.email;
                listItem.innerHTML += ` - <a href="#" onclick="deleteEmail('${email.email}', '${domain}')">Delete</a> - <a href="#" onclick="updatePassword('${email.email}', '${domain}')">Update Password</a>`;
                emailList.appendChild(listItem);
            });
        }

        function deleteEmail(email, domain) {
            if (confirm(`Are you sure you want to delete ${email}?`)) {
                fetch(`index.php?email=${email}&domain=${domain}`, { method: 'DELETE' })
                    .then(response => response.text())
                    .then(result => {
                        alert(result);
                        fetchEmails(domain);
                    });
            }
        }

        function updatePassword(email, domain) {
            const newPassword = prompt('Enter the new password for ' + email);
            if (newPassword) {
                fetch(`index.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password: newPassword, domain, updatePassword: true })
                })
                    .then(response => response.text())
                    .then(result => alert(result));
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('fetchEmailsBtn').addEventListener('click', () => {
                const domain = document.getElementById('domain').value;
                fetchEmails(domain);
            });

            document.getElementById('createEmailForm').addEventListener('submit', (event) => {
                event.preventDefault();
                const formData = new FormData(event.target);
                fetch('index.php', {
                    method: 'POST',
                    body: new URLSearchParams(formData)
                })
                .then(response => response.text())
                .then(result => {
                    alert(result);
                    fetchEmails(formData.get('domain'));
                });
            });
        });
    </script>
</head>
<body>
    <h1>Email Management</h1>
    <label for="domain">Domain:</label>
    <input type="text" id="domain" name="domain">
    <button id="fetchEmailsBtn">Fetch Emails</button>
    <ul id="emailList"></ul>

    <h2>Create New Email</h2>
    <form id="createEmailForm" method="POST">
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="domain">Domain:</label>
        <input type="text" id="createEmailDomain" name="domain" required>
        <br>
        <input type="hidden" name="createEmail" value="1">
        <input type="submit" value="Create Account">
    </form>
</body>
</html>
