<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waste Management System - login</title>
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
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
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
        .hero {
            text-align: center;
            padding: 100px 20px;
            background: url('https://source.unsplash.com/1600x900/?recycling,environment') center/cover no-repeat;
            color: white;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.7);
        }
        .hero h1 {
            font-size: 48px;
            font-weight: bold;
        }
        .hero p {
            font-size: 20px;
            margin-top: 10px;
            max-width: 600px;
        }
        .buttons {
            margin-top: 30px;
        }
        .btn {
            display: inline-block;
            padding: 14px 30px;
            margin: 10px;
            font-size: 18px;
            text-decoration: none;
            color: white;
            border-radius: 30px;
            transition: all 0.3s ease-in-out;
            font-weight: bold;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }
        .btn:hover {
            transform: scale(1.05);
        }
        .btn-green {
            background: #28a745;
        }
        .btn-green:hover {
            background: #218838;
        }
        .btn-blue {
            background: #007bff;
        }
        .btn-blue:hover {
            background: #0056b3;
        }
        .section {
            text-align: center;
            padding: 50px 20px;
            background: white;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background: #333;
            color: white;
            margin-top: 30px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="home.html">Home</a>
        <a href="login.php">Login</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact</a>
    </div>

    <!-- Hero Section -->
    <div class="hero">
        <h1> City Safisha</h1>
        <p>Efficient, eco-friendly waste collection and recycling for a cleaner future.</p>
        <div class="buttons">
            <a href="login.php" class="btn btn-blue">Get Started</a>
        </div>
    </div>

    <!-- Registration Sections -->
    <div class="section">
        <h2>Register as a Resident</h2>
        <div class="buttons">
            <a href="regres.php" class="btn btn-green">Register</a>
            <a href="loginres.php" class="btn btn-blue">Login</a>
        </div>
    </div>

    <div class="section">
        <h2>Register as an Industry</h2>
        <div class="buttons">
            <a href="indreg.php" class="btn btn-green">Register</a>
            <a href="indlogin.php" class="btn btn-blue">Login</a>
        </div>
    </div>

    <div class="section">
        <h2>Register as a Waste Collector</h2>
        <div class="buttons">
            <a href="regcollectors.php" class="btn btn-green">Register</a>
            <a href="collectorlogin.php" class="btn btn-blue">Login</a>
        </div>
    </div>

    <div class="section">
        <h2>Admin Login</h2>
        <div class="buttons">
            <a href="adminlogin.php" class="btn btn-green">Login as Admin</a>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        &copy; 2025 Waste Management System | All Rights Reserved
    </div>

</body>
</html>
