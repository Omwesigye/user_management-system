<?php
session_start();
require 'db.php'; // Database connection
require 'email.php'; // PHPMailer function

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
      
        
  $_SESSION['verified_email'] = $email;

            header("Location:reset.php"); // Redirect to verification page
            exit;
        } else {
            // Handle the case when the email is not found
            echo "This email address is not registered.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="forgot_password.css"> <!-- Ensure to link the CSS file -->
</head>
<body>

    <!-- Content Section -->
    <div class="content">
        <h2>Forgot Password</h2>
        <form action="forgot_password.php" method="POST">
            <label for="email">Enter your email:</label><br>
            <input type="email" id="email" name="email" required><br><br>
            <button type="submit">Send Verification Code</button>
        </form>
    </div>

</body>
</html>

