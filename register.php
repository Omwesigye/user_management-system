<?php
require 'db.php'; 
if (isset($_POST['save_contact'])) {
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Handle file upload
    $upload_dir = "uploads/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true); 
    }
    $profile_picture = "default.png";

    if (!empty($_FILES['profile_picture']['name'])) {
        $file_ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if ($_FILES['profile_picture']['size'] > 5 * 1024 * 1024) {
            die("❌ File size must be less than 5MB.");
        }

        if (!in_array(strtolower($file_ext), $allowed_exts)) {
            die("❌ Invalid file type.");
        }

        $profile_picture = uniqid() . "." . $file_ext;
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_dir . $profile_picture);
    }

    // Insert into database
    $sql = "INSERT INTO users (username, email, password, profile_picture) VALUES (:username, :email, :password, :profile_picture)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':username'  => $_POST['username'],
        ':email' => $_POST['email'],
        ':password' => $hashed_password,
        ':profile_picture' => $profile_picture
    ]);
    header("Location: login.php");
    exit;
    echo "✅ Contact saved successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>

    <div class="container">
        <div class="form-container">
            <h2>Register</h2>
            <form action="register.php" method="POST" enctype="multipart/form-data">
                <label>Username:</label>
                <input type="text" name="username" required><br>

                <label>Email:</label>
                <input type="email" name="email" required><br>

                <label>Password:</label>
                <input type="password" name="password" required><br>

                <label>Profile Picture:</label>
                <input type="file" name="profile_picture"><br>

                <button type="submit" name="save_contact">Register</button>
            </form>
        </div>
    </div>

</body>
</html>

