<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Registration</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: #f4f4f4;
        }
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
        .register-container {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 30px;
            margin: 50px auto;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
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
        .error-message {
            color: red;
            margin-bottom: 10px;
        }
        .success-message {
            color: green;
            margin-bottom: 10px;
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
    </style>
</head>
<body>

    <div class="navbar">
        <a href="home.html">Home</a>
        <a href="login.php">Login</a>
    </div>

    <div class="register-container">
        <h2>Resident Registration</h2>
        <?php if (!empty($error_message)) { echo "<p class='error-message'>$error_message</p>"; } ?>
        <?php if (!empty($success_message)) { echo "<p class='success-message'>$success_message</p>"; } ?>
        
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
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Database connection
                $conn = new mysqli("localhost", "root", "", "waste");

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Check if email already exists
                $check_sql = "SELECT * FROM residents WHERE email = ?";
                $stmt = $conn->prepare($check_sql);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $error_message = "Email already registered.";
                } else {
                    // Insert new resident
                    $insert_sql = "INSERT INTO residents (name, email, password) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($insert_sql);
                    $stmt->bind_param("sss", $name, $email, $hashed_password);

                    if ($stmt->execute()) {
                        $success_message = "Registration successful! <a href='loginres.php'>Login here</a>";
                    } else {
                        $error_message = "Error registering user.";
                    }
                }

                $stmt->close();
                $conn->close();
            }
        }
        ?>

        <div class="link">
            <a href="loginres.php">Already have an account? Login</a>
        </div>
    </div>

</body>
</html>
