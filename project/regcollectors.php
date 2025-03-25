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
        
        /* Background Image */
        body {
            background: url('https://i.pinimg.com/736x/17/e5/ea/17e5ea4493c00640fe49e1f49fdfb0fd.jpg') no-repeat center/cover;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            color: white;
            text-align: center;
            padding: 20px;
        }

        /* Overlay to improve readability */
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }

        /* Navbar */
        .navbar {
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background: #28a745;
            padding: 15px 0;
            display: flex;
            justify-content: center;
            gap: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .navbar a:hover {
            background: #218838;
        }

        /* Registration Box */
        .register-container {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.9); /* Slight transparency */
            padding: 30px;
            margin-top: 80px; /* Prevent overlap with navbar */
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            color: #333;
        }

        h2 { margin-bottom: 20px; color: #28a745; }
        .input-group { margin-bottom: 15px; text-align: left; }
        .input-group label { font-weight: bold; display: block; margin-bottom: 5px; }
        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .error-message { color: red; font-size: 14px; margin-bottom: 10px; }
        .success-message { color: green; margin-bottom: 10px; }
        .btn {
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover { background: #218838; }
        .link { margin-top: 10px; }
        .link a { color: #007bff; text-decoration: none; }
        .link a:hover { text-decoration: underline; }

        /* Responsive */
        @media (max-width: 768px) {
            .register-container {
                width: 90%;
            }
            .navbar {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>

    <div class="overlay"></div> <!-- Background Overlay -->

    <!-- Navbar -->
    <div class="navbar">
        <a href="home.html">Home</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact</a>
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
            </div>
            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email">
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
            </div>
            <div class="input-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>
            <button type="submit" class="btn">Register</button>
        </form>

        <div class="link">
            <a href="collectorlogin.php">Already have an account? Login</a>
        </div>
    </div>

</body>
</html>
