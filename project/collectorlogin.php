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

        // Check if collector exists
        $sql = "SELECT * FROM collectors WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $collector = $result->fetch_assoc();
            if (password_verify($password, $collector['password'])) {
                $_SESSION['collector_id'] = $collector['id'];
                $_SESSION['collector_name'] = $collector['name'];
                header("Location: collectors.php"); // Redirect to collector dashboard
                exit();
            } else {
                $error_message = "Invalid email or password.";
            }
        } else {
            $error_message = "Collector not found.";
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
    <title>Collector Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }

        /* Background Image */
        body {
            background: url('https://i.pinimg.com/736x/17/fb/b5/17fbb5af66fe2f77be0ec450606d2e5d.jpg') no-repeat center/cover;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            color: white;
            text-align: center;
            padding: 20px;
            position: relative;
        }

        /* Overlay for better readability */
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

        /* Login Box */
        .login-container {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.9); /* Semi-transparent */
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
            .login-container {
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

    <!-- Login Form -->
    <div class="login-container">
        <h2>Collector Login</h2>

        <?php if (!empty($error_message)) { echo "<p class='error-message'>$error_message</p>"; } ?>

        <form method="POST" onsubmit="return validateForm()">
            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email">
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
            </div>
            <button type="submit" class="btn">Login</button>
        </form>

        <div class="link">
            <a href="regcollectors.php">Don't have an account? Register</a>
        </div>
    </div>

</body>
</html>
