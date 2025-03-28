<?php
require "connectiondb.php"; // Ensure database connection

$error = $success = "";
$token = $_GET['token'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Verify token
    $stmt = $conn->prepare("SELECT id FROM admins WHERE reset_token=? AND reset_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Reset password
        $stmt = $conn->prepare("UPDATE admins SET password=?, reset_token=NULL, reset_expiry=NULL WHERE reset_token=?");
        $stmt->bind_param("ss", $new_password, $token);
        $stmt->execute();
        $success = "Password has been reset! <a href='adminlogin.php'>Login here</a>";
    } else {
        $error = "Invalid or expired token.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
    <form method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <input type="password" name="password" placeholder="Enter new password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
