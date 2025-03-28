<?php
session_start();
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $errorMessage = "All fields are required.";
    } else {
        $conn = new mysqli("localhost", "root", "", "waste");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM residents WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            $errorMessage = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $resident = $result->fetch_assoc();
                if (password_verify($password, $resident['password'])) {
                    $_SESSION['resident_id'] = $resident['id'];
                    $_SESSION['resident_name'] = $resident['name'];
                    header("Location: residents.php");
                    exit();
                } else {
                    $errorMessage = "Invalid email or password.";
                }
            } else {
                $errorMessage = "Resident not found.";
            }

            $stmt->close();
        }
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
        body {
            background-image: url('https://i.pinimg.com/736x/24/25/74/242574a31c08a044aba20fcf15694ca7.jpg'); /* Replace with your image path */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed; /* Optional: Fix the background */
            display: flex; /* For centering the login form */
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            min-height: 100vh; /* Ensure full viewport height */
        }

        .navbar {
            background: #28a745;
            padding: 15px 20px;
            text-align: center;
            position: fixed; /* Fixed navbar at the top */
            top: 0;
            left: 0;
            width: 100%;
            z-index: 100; /* Ensure navbar is above other content */
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

        .login-container {
            background: white;
            padding: 30px;
            width: 100%;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 80px; /* Add margin to prevent overlap with navbar */
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

        .error-message {
            color: red;
            font-size: 16px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="home.html">Home</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact</a>
    </div>

    <div class="login-container">
        <h2>Resident Login</h2>

        <?php if (!empty($errorMessage)) { echo "<p class='error-message'>$errorMessage</p>"; } ?>

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