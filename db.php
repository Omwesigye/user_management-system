<?php
$host = "localhost";  
$username = "root";     
$password = "";         

try {
    $conn = new PDO("mysql:host=$host;dbname=user_management", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  //  echo "✅ Database connected successfully!";
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage());
}

?>