use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // If using Composer
// require 'path/to/PHPMailer/src/Exception.php';
// require 'path/to/PHPMailer/src/PHPMailer.php';
// require 'path/to/PHPMailer/src/SMTP.php';


function sendPasswordResetEmail($email, $token) {
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Gmail SMTP Server
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com'; // Your Gmail address
        $mail->Password = 'your-email-password'; // Your Gmail password or App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email Details
        $mail->setFrom('your-email@gmail.com', 'Your Website Name');
        $mail->addAddress($email);
        $mail->Subject = 'Password Reset Request';
        $mail->isHTML(true);

        // Email Content
        $reset_link = "http://localhost/reset_password.php?token=" . urlencode($token);
        $mail->Body = "
            <p>Hello,</p>
            <p>Click the link below to reset your password:</p>
            <p><a href='$reset_link'>$reset_link</a></p>
            <p>If you didn't request a password reset, please ignore this email.</p>
            <p>Regards,<br>Your Website Team</p>
        ";

        // Send Email
        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Mailer Error: " . $mail->ErrorInfo;
    }
}




<?php
session_start();
require "connectiondb.php";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // Generate reset token & expiry
                $token = bin2hex(random_bytes(32));
                $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

                // Store token in the database
                $stmt = $conn->prepare("UPDATE admins SET reset_token=?, reset_expiry=? WHERE email=?");
                if ($stmt) {
                    $stmt->bind_param("sss", $token, $expiry, $email);
                    $stmt->execute();

                    // Send email
                    $reset_link = "http://yourwebsite.com/reset_password.php?token=$token";
                    $subject = "Password Reset Request";
                    $message = "Click the link below to reset your password:\n\n$reset_link\n\nThis link expires in 1 hour.";
                    $headers = "From: no-reply@yourwebsite.com\r\n";

                    if (@mail($email, $subject, $message, $headers)) {
                        $success = "A password reset link has been sent to your email.";
                    } else {
                        $error = "Failed to send reset email. Please try again.";
                    }
                } else {
                    $error = "Database error: " . $conn->error;
                }
            } else {
                $error = "Email not found.";
            }
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f4f4f4;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            margin-bottom: 15px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
        .message {
            margin-top: 10px;
        }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <?php if ($error) echo "<p class='message error'>$error</p>"; ?>
        <?php if ($success) echo "<p class='message success'>$success</p>"; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Send Reset Link</button>
        </form>
    </div>
</body>
</html>
