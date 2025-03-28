<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Registration</title>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body {
            background-image: url('https://i.pinimg.com/736x/9f/06/33/9f063305e8a8e9c54200cd811652dd0e.jpg'); /* Replace with your image path */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .navbar {
            background: #28a745;
            padding: 15px 20px;
            text-align: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 100;
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
        .register-container {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 80px; /* Add margin for navbar */
        }
        h2 { margin-bottom: 20px; color: #333; }
        .input-group { margin-bottom: 15px; text-align: left; }
        .input-group label { display: block; font-weight: bold; margin-bottom: 5px; }
        .input-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px; }
        .btn { width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 5px; font-size: 18px; cursor: pointer; transition: 0.3s; }
        .btn:hover { background: #218838; }
        .error-message, .success-message { margin-bottom: 10px; }
        .error-message { color: red; }
        .success-message { color: green; }
        .link { margin-top: 10px; }
        .link a { color: #007bff; text-decoration: none; }
        .link a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="navbar">
         <a href="home.html">Home</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact</a>
    </div>

    <div class="register-container">
        <h2>Resident Registration</h2>
        <?php
        session_start();
        $errorMessage = ""; // More descriptive variable names
        $successMessage = "";

        if ($_SERVER["REQUEST_METHOD"] === "POST") { // Strict comparison
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $confirmPassword = trim($_POST['confirm_password']);

            if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
                $errorMessage = "All fields are required.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errorMessage = "Invalid email format.";
            } elseif ($password !== $confirmPassword) {
                $errorMessage = "Passwords do not match.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $conn = new mysqli("localhost", "root", "", "waste");

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $checkSql = "SELECT * FROM residents WHERE email = ?";
                $stmt = $conn->prepare($checkSql);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $errorMessage = "Email already registered.";
                } else {
                    $insertSql = "INSERT INTO residents (name, email, password) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($insertSql);
                    $stmt->bind_param("sss", $name, $email, $hashedPassword);

                    if ($stmt->execute()) {
                        $successMessage = "Registration successful! <a href='loginres.php'>Login here</a>";
                    } else {
                        $errorMessage = "Error registering user.";
                    }
                }

                $stmt->close();
                $conn->close();
            }
        }

        if (!empty($errorMessage)) { echo "<p class='error-message'>$errorMessage</p>"; }
        if (!empty($successMessage)) { echo "<p class='success-message'>$successMessage</p>"; }
        ?>

        <form method="POST">
            <div class="input-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="input-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn">Register</button>
        </form>

        <div class="link">
            <a href="loginres.php">Already have an account? Login</a>
        </div>
    </div>

</body>
</html>