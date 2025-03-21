<?php
session_start();
$error_message = "";
$success_message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Database connection
        $conn = new mysqli("localhost", "root", "", "waste");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if email already exists
        $check_sql = "SELECT * FROM collectors WHERE email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "Email already registered.";
        } else {
            // Insert new waste collector
            $insert_sql = "INSERT INTO collectors (name, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt->execute()) {
                $success_message = "Registration successful! <a href='collectorlogin.php'>Login here</a>";
            } else {
                $error_message = "Error registering collector.";
            }
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collector Registration</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #f4f4f4; }

        /* Navbar */
        .navbar { background: #28a745; padding: 15px 20px; text-align: center; }
        .navbar a { color: white; text-decoration: none; padding: 10px 20px; font-size: 18px; transition: 0.3s; }
        .navbar a:hover { background: #218838; border-radius: 5px; }

        /* Registration Box */
        .register-container {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 30px;
            margin: 80px auto;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 { margin-bottom: 20px; color: #333; }
        .input-group { margin-bottom: 15px; text-align: left; }
        .input-group label { display: block; font-weight: bold; margin-bottom: 5px; }
        .input-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px; }
        .input-group input.error { border: 1px solid red; }
        .error-message { color: red; font-size: 14px; margin-bottom: 10px; }
        .success-message { color: green; margin-bottom: 10px; }
        .btn { width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 5px; font-size: 18px; cursor: pointer; transition: 0.3s; }
        .btn:hover { background: #218838; }
        .link { margin-top: 10px; }
        .link a { color: #007bff; text-decoration: none; }
        .link a:hover { text-decoration: underline; }
        .password-wrapper { position: relative; display: flex; align-items: center; }
        .password-wrapper input { flex-grow: 1; }
        .toggle-password { position: absolute; right: 10px; cursor: pointer; color: #555; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="home.html">Home</a>
        <a href="login.php">Login</a>
    </div>

    <!-- Registration Form -->
    <div class="register-container">
        <h2>Waste Collector Registration</h2>

        <?php if (!empty($error_message)) { echo "<p class='error-message'>$error_message</p>"; } ?>
        <?php if (!empty($success_message)) { echo "<p class='success-message'>$success_message</p>"; } ?>

        <form method="POST" onsubmit="return validateForm()">
            <div class="input-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name">
                <p class="error-message" id="name-error"></p>
            </div>
            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email">
                <p class="error-message" id="email-error"></p>
            </div>
            <div class="input-group password-wrapper">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
                <span class="toggle-password" onclick="togglePassword('password')">&#128065;</span>
                <p class="error-message" id="password-error"></p>
            </div>
            <div class="input-group password-wrapper">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password">
                <span class="toggle-password" onclick="togglePassword('confirm_password')">&#128065;</span>
                <p class="error-message" id="confirm-password-error"></p>
            </div>
            <button type="submit" class="btn">Register</button>
        </form>

        <div class="link">
            <a href="collectorlogin.php">Already have an account? Login</a>
        </div>
    </div>

    <script>
        function validateForm() {
            let isValid = true;

            // Get input values
            let name = document.getElementById("name").value.trim();
            let email = document.getElementById("email").value.trim();
            let password = document.getElementById("password").value.trim();
            let confirmPassword = document.getElementById("confirm_password").value.trim();

            // Reset errors
            document.querySelectorAll(".error-message").forEach(e => e.textContent = "");
            document.querySelectorAll(".input-group input").forEach(e => e.classList.remove("error"));

            // Validate Name
            if (name === "") {
                document.getElementById("name-error").textContent = "Full name is required.";
                document.getElementById("name").classList.add("error");
                isValid = false;
            }

            // Validate Email
            let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                document.getElementById("email-error").textContent = "Invalid email format.";
                document.getElementById("email").classList.add("error");
                isValid = false;
            }

            return isValid;
        }

        function togglePassword(fieldId) {
            let field = document.getElementById(fieldId);
            field.type = field.type === "password" ? "text" : "password";
        }
    </script>

</body>
</html>
