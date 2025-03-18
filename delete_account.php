<?php
session_start();
require_once 'profile.php'; 

// Ensure the user is logged in before proceeding
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id']; // Get logged-in user's ID

// Fetch user details
$sql = "SELECT * FROM users WHERE id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$user = $stmt->fetch();

// Handle account deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    try {
        $conn->beginTransaction(); // Start transaction

        // Delete profile picture if it's not the default one
        if (!empty($user['profile_picture']) && $user['profile_picture'] !== 'default.png') {
            $profile_picture_path = 'uploads/' . $user['profile_picture'];
            if (file_exists($profile_picture_path)) {
                unlink($profile_picture_path); // Delete file
            }
        }

        // Delete the user from the database
        $sql = "DELETE FROM users WHERE id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);

        $conn->commit(); // Commit transaction

        // Destroy session and redirect
        session_destroy();
        header("Location: home.html");
        exit();
    } catch (Exception $e) {
        $conn->rollBack(); // Rollback transaction if an error occurs
        echo "❌ Error: Could not delete account. " . $e->getMessage();
    }
}
?>