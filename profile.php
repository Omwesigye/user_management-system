<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

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

// Get the current user's details
$sql = "SELECT username, email, profile_picture, created_at FROM users WHERE id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $username = $user['username'];
    $email = $user['email'];
    $profile_picture = $user['profile_picture'];
    $created_at = $user['created_at'];

} else {
    die("❌ User not found.");
}

// Handle profile update
if (isset($_POST['update_details'])) {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];

    $sql = "UPDATE users SET username = :username, email = :email WHERE id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':username' => $new_username, ':email' => $new_email, ':user_id' => $_SESSION['user_id']]);
    
    header("Location: profile.php");
    exit;
}
    // Handle new profile picture upload
    if (isset($_POST['change_picture'])) {
        if (!empty($_FILES['profile_picture']['name'])) {
            $file_ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
            
            if ($_FILES['profile_picture']['size'] > 5 * 1024 * 1024) {
                die("❌ File size must be less than 5MB.");
            }
            
            if (!in_array(strtolower($file_ext), $allowed_exts)) {
                die("❌ Invalid file type.");
            }
            
            $new_profile_picture = uniqid() . "." . $file_ext;
            move_uploaded_file($_FILES['profile_picture']['tmp_name'], "uploads/" . $new_profile_picture);
    
            $sql = "UPDATE users SET profile_picture = :profile_picture WHERE id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':profile_picture' => $new_profile_picture, ':user_id' => $_SESSION['user_id']]);
            
            header("Location: profile.php");
            exit;
        }
        header("Location: home.html");
    exit;
    }

    


// Logout action
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location:home.html");
    exit;
}

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
<div class="profile-container">
    <h1>User Profile</h1>
    <div class="profile-section">
        <img src="uploads/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" width="150"><br>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="profile_picture" required>
            <button type="submit" name="change_picture">Change profile picture</button>
        </form>
    </div>
    
    <h2><?php echo htmlspecialchars($username); ?></h2>
    <p>Email: <?php echo htmlspecialchars($email); ?></p>
    <p>Created At: <?php echo htmlspecialchars($created_at); ?></p>
   
    
    <button onclick="document.getElementById('updateForm').style.display='block'">Update</button>
    
    <div id="updateForm" style="display: none;">
        <form method="POST">
            <label>Username: </label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required><br>
            
            <label>Email: </label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br>
            
            <button type="submit" name="update_details">Save Changes</button>
        </form>
    </div>
    
    <form action="delete_account.php" method="POST">
        <button type="submit" name="delete_account" class="delete-btn">Delete Account</button>
    </form>
    
    <br>
    <a href="?logout=true">Logout</a>
</div>
</body>
</html>

