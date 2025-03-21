<?php
session_start();
$error_message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error_message = "All fields are required.";
    } else {
        // Database connection
        $conn = new mysqli("localhost", "root", "", "waste");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if resident exists
        $sql = "SELECT * FROM residents WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $resident = $result->fetch_assoc();
            if (password_verify($password, $resident['password'])) {
                $_SESSION['resident_id'] = $resident['id'];
                $_SESSION['resident_name'] = $resident['name'];
                header("Location: residents.php"); // Redirect to resident dashboard
                exit();
            } else {
                $error_message = "Invalid email or password.";
            }
        } else {
            $error_message = "Resident not found.";
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
    <title>Resident Login</title>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #f4f4f4; }

        /* Navbar Styling */
        .navbar {
            background: #28a745;
            padding: 15px 20px;
            text-align: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 18px;
            transition: 0.3s;
        }
        .navbar a:hover {
            background: #218838;
            border-radius: 5px;
        }

        /* Login Box */
        .login-container {
            background: white;
            padding: 30px;
            width: 100%;
            max-width: 400px;
            margin: 80px auto;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .input-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn:hover {
            background: #218838;
        }
        .forgot-password {
            margin-top: 10px;
        }
        .forgot-password a {
            color: #007bff;
            text-decoration: none;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }

        /* Error Message Styling */
        .error-message {
            color: red;
            font-size: 16px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="home.html">Home</a>
        <a href="regres.php">Register</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact</a>
    </div>

    <!-- Login Form -->
    <div class="login-container">
        <h2>Resident Login</h2>
        
        <?php if (!empty($error_message)) { echo "<p class='error-message'>$error_message</p>"; } ?>

        <form action="loginres.php" method="POST">
            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        
        <div class="forgot-password">
            <a href="forgot_password.php">Forgot Password?</a>
        </div>
    </div>

</body>
</html>
