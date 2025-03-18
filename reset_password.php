<?php
session_start();
require 'db.php';


if (!isset($_SESSION['verified_email'])) {
    die("❌ Unauthorized access!");
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_SESSION['verified_email'];  // Get the verified email from the session
   
    $newPassword = trim($_POST['password']); // New password entered by the user
    $confirmPassword = trim($_POST['confirm_password']);// Confirm password entered by the user

    // Check if the new password and confirm password match
    if ($newPassword !== $confirmPassword) {
        echo " Passwords do not match. Please try again.";
        exit;
    }

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // If the reset code matches, update the password and clear the reset code
        $updateStmt = $conn->prepare("UPDATE users SET password = :password WHERE email = :email");
        $updateStmt->execute([
            'password' => $hashedPassword,
            'email' => $email
        ]);
    

        unset($_SESSION['verified_email']);
        header("Location: home.html"); // Redirect to verification page
            exit;
        echo "✅ Password reset successfully!";
        exit();
   
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="reset_password.css"> <!-- Ensure to link the CSS file -->
</head>
<body>

<div class="content">
        <h2>Reset Your Password</h2>
        <form action="reset_password.php" method="POST">
            <label for="password">New Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>

            <label for="confirm_password">Confirm New Password:</label><br>
            <input type="password" id="confirm_password" name="confirm_password" required><br><br>

           
        <button type="submit">Reset Password</button>
        </form>
    </div>

</body>
</html>

