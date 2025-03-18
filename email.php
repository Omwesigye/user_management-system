<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

require 'db.php';
function sendPasswordResetCode($email) {
    global $conn; 

    $resetCode = mt_rand(100000, 999999);

    //  Update the reset code in the database
    $updateStmt = $conn->prepare("UPDATE users SET reset_code = :reset_code WHERE email = :email");
    $updateStmt->bindParam(':reset_code', $resetCode, PDO::PARAM_STR);
    $updateStmt->bindParam(':email', $email, PDO::PARAM_STR);
    $updateStmt->execute();

    //  Fetch the reset code directly from the database to ensure it matches
    $fetchStmt = $conn->prepare("SELECT reset_code FROM users WHERE email = :email");
    $fetchStmt->bindParam(':email', $email, PDO::PARAM_STR);
    $fetchStmt->execute();
    $result = $fetchStmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['reset_code'] == $resetCode) {
        $resetCode = $result['reset_code']; /// Get the stored reset code

        // Use PHPMailer to send the reset code
        $mail = new PHPMailer(true);
    
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // Gmail SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'omwesigyeseezi5@gmail.com'; // Your Gmail email
            $mail->Password = 'ubff mkuu shpa tkyw'; // Use an app password, NOT your real password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('omwesigyeseezi5@gmail.com', 'User management system');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Code';

            // Send the reset code in the email
            $mail->Body = "<h3>Password Reset Code</h3>
                           <p>Your password reset code is: <strong>$resetCode</strong></p>";

            $mail->send();
            return "✅ Reset code sent successfully to $email";
        } catch (Exception $e) {
            return "❌ Failed to send email. Error: {$mail->ErrorInfo}";
        }
    } else {
        return "No user found with that email.";
    }
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if email is sent via POST
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
        echo sendPasswordResetCode($email);
    } else {
        echo "❌ No email provided.";
    }
}
?>
