<?php
session_start();
require 'db.php';

if (!isset($_SESSION['verified_email'])) {
    die("❌ Unauthorized access!");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_SESSION['verified_email'];  // Get the verified email from the session
    $submittedResetCode = trim($_POST['reset_code']); // Get the submitted reset code

    // Fetch the stored reset code for the email
    $stmt = $conn->prepare("SELECT reset_code FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['reset_code'] === $submittedResetCode) {
        // Redirect to password reset page
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "Invalid reset code. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        /* General Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Arial', sans-serif;
    color: white;
    background-color: #34495e;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
}

/* Background Section */
.background {
    background-image: url('bg.jpg'); 
    background-size: cover;
    background-position: center;
    height: 100vh; 
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Content Section */
.content {
    background-color: rgba(0, 0, 0, 0.7); 
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    max-width: 400px;
    width: 80%;
}

h2 {
    font-size: 2rem;
    margin-bottom: 20px;
    color: #ECF0F1;
}

label {
    font-size: 1rem;
    color: #BDC3C7;
}

/* Input Field */
input[type="text"] {
    width: 100%;
    padding: 12px;
    margin-top: 10px;
    border-radius: 5px;
    border: none;
    font-size: 1rem;
}

/* Submit Button */
button {
    background-color: #3498db;
    color: white;
    padding: 15px 30px;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    margin-top: 20px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #2980b9;
}

/* Responsive Design */
@media (max-width: 768px) {
    .content {
        width: 90%;
    }

    h2 {
        font-size: 1.8rem;
    }

    button {
        padding: 12px 25px;
    }
}

    </style>
</head>
<body>


<div class="background">
    <div class="content">
        <h2>Enter Reset Code</h2>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <label for="reset_code">Reset Code:</label><br>
            <input type="text" id="reset_code" name="reset_code" required><br><br>
            <button type="submit">Verify Code</button>
        </form>
    </div>
</div>

</body>
</html>
