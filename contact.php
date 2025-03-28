<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Waste Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        /* Background Image */
        body {
            background: url('https://i.pinimg.com/736x/7b/20/0c/7b200c51edd5fb28230611e5fc48754f.jpg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .navbar {
            background: rgba(40, 167, 69, 0.9);
            padding: 15px 20px;
            text-align: center;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
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
        .contact-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            width: 100%;
            max-width: 500px;
            margin-top: 100px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .contact-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .contact-info {
            font-size: 18px;
            color: #555;
            line-height: 1.6;
        }
        .contact-info a {
            color: #007bff;
            text-decoration: none;
        }
        .contact-info a:hover {
            text-decoration: underline;
        }
        .social-icons {
            margin-top: 20px;
        }
        .social-icons a {
            display: inline-block;
            margin: 10px;
            font-size: 24px;
            color: #007bff;
            transition: 0.3s;
        }
        .social-icons a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="navbar">

        <a href="home.html">Home</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact</a>
    </div>

    <div class="contact-container">
        <h2>Contact Us</h2>
        <p class="contact-info">
            📧 Email: <a href="mailto:waste@gmail.com">waste@gmail.com</a><br>
            📞 Phone: <a href="tel:0712345678">0712345678</a><br>
        </p>
        <h3>Follow us on social media:</h3>
        <div class="social-icons">
            <a href="#" target="_blank">🔵 Facebook</a>
            <a href="#" target="_blank">🔷 Twitter</a>
            <a href="#" target="_blank">📸 Instagram</a>
            <a href="#" target="_blank">💼 LinkedIn</a>
        </div>
    </div>

</body>
</html>
