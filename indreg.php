<?php
session_start();
require "connectiondb.php"; // Ensure database connection

$errorMessage = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $errorMessage = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $errorMessage = "Passwords do not match.";
    } else {
        // Hash password before saving
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO industries (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashedPassword);

        if ($stmt->execute()) {
            $successMessage = "Registration successful! You can now <a href='indlogin.php'>Login here</a>.";
        } else {
            $errorMessage = "Error registering user.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Industrial Registration</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background: #f4f4f4;
            background-image: url('https://i.pinimg.com/736x/8c/e7/16/8ce716cf43bcf501e81c44c90565ed71.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        nav {
            background: #28a745;
            padding: 15px 20px;
            text-align: center;
        }
        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 18px;
            transition: 0.3s;
        }
        nav a:hover {
            background: #218838;
            border-radius: 5px;
        }
        main {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 30px;
            margin: 50px auto;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        h2 {
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
        .input-group input.error {
            border: 1px solid red;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }
        .success-message {
            color: green;
            margin-bottom: 10px;
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
        .link {
            margin-top: 10px;
        }
        .link a {
            color: #007bff;
            text-decoration: none;
        }
        .link a:hover {
            text-decoration: underline;
        }
        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .password-wrapper input {
            flex-grow: 1;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            cursor: pointer;
            color: #555;
        }
    </style>
</head>
<body>

    <nav>
       <a href="home.html">Home</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact</a>
    </nav>

    <main>
        <h2>Industrial Registration</h2>

        <?php if (!empty($errorMessage)): ?>
            <p class="error-message"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <?php if (!empty($successMessage)): ?>
            <p class="success-message"><?php echo $successMessage; ?></p>
        <?php endif; ?>

        <form method="POST" action="indreg.php" id="registrationForm">
            <div class="input-group">
                <label for="name">Company Name</label>
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
                <span class="toggle-password" data-target="password">&#128065;</span>
                <p class="error-message" id="password-error"></p>
            </div>
            <div class="input-group password-wrapper">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password">
                <span class="toggle-password" data-target="confirm_password">&#128065;</span>
                <p class="error-message" id="confirm-password-error"></p>
            </div>
            <button type="submit" class="btn">Register</button>
        </form>

        <div class="link">
            <a href="indlogin.php">Already have an account? Login</a>
        </div>
    </main>

    <script>
        document.getElementById('registrationForm').addEventListener('submit', function(event) {
            let isValid = true;
            const name = document.getElementById("name").value.trim();
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value.trim();
            const confirmPassword = document.getElementById("confirm_password").value.trim();

            if (!name) {
                document.getElementById("name-error").textContent = "Company name is required.";
                isValid = false;
            }

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                document.getElementById("email-error").textContent = "Invalid email format.";
                isValid = false;
            }

            if (confirmPassword !== password) {
                document.getElementById("confirm-password-error").textContent = "Passwords do not match.";
                isValid = false;
            }

            if (!isValid) {
                event.preventDefault();
            }
        });

        document.querySelectorAll('.toggle-password').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const field = document.getElementById(this.dataset.target);
                field.type = field.type === "password" ? "text" : "password";
            });
        });
    </script>

</body>
</html>
