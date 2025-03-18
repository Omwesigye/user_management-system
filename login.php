<?php
session_start();

// Database connection
$host = "localhost";  
$username = "root";     
$password = "";         
$dbname = "user_management"; 

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage());
}

if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit;
}
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_email'])) {
    $email = $_COOKIE['user_email'];
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

        // Redirect to profile page after setting session
        header("Location: profile.php");
        exit;
    } else {
        // Invalid cookie, delete it
        setcookie("user_email", "", time() - 3600, "/");
    }
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email exists in the database
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Start the session and store user information
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

        // Remember Me functionality
        if (!empty($_POST['remember_me'])) {
            setcookie("user_email", $user['email'], time() + 5, "/"); // 5s
        } else {
            setcookie("user_email", "", time() - 3600, "/"); // Expire the cookie
        }

        // Redirect to profile page
        header("Location: profile.php");
        exit;
    } else {
        echo "❌ Invalid email or password.";
    }
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css"> 
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Login</h2>
            <form action="login.php" method="POST">
                <label>Email:</label>
                <input type="email" name="email" required><br>
                
                <label>Password:</label>
                <input type="password" name="password" required><br>

                <label>
                    <input type="checkbox" name="remember_me"> Remember Me
                </label><br>
                
                <button type="submit" name="login">Login</button>
            </form>

            <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
        </div>
    </div>
</body>
</html>
