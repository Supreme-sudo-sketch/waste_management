<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Waste Management System</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        /* Navbar Styles */
        .navbar {
            background: #28a745; /* Green color to match other pages */
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .navbar h1 {
            color: white;
            font-size: 24px;
            margin: 0;
        }

        .navbar .nav-links {
            display: flex;
            gap: 20px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            font-size: 18px;
            transition: background 0.3s;
            border-radius: 5px;
        }

        .navbar a:hover {
            background: #218838; /* Darker green */
        }

        /* Header Section */
        .header {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                        url('https://i.pinimg.com/736x/79/33/d4/7933d4291e849451ac9181653b914f4a.jpg') no-repeat center center/cover;
            text-align: center;
            padding: 140px 20px 100px;
            color: white;
            margin-top: 60px; /* Prevents content from hiding behind navbar */
        }

        .header h1 {
            font-size: 3em;
            margin-bottom: 10px;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }

        .section {
            background: white;
            padding: 30px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .section:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .section h2 {
            color: #28a745; /* Green */
            margin-bottom: 20px;
        }

        /* Background Sections */
        .section.bg-image {
            background-size: cover;
            background-position: center;
            color: white;
            padding: 50px 30px;
            border-radius: 10px;
        }

        .section.bg-image h2, .section.bg-image p {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        }

        .section.residents {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                              url('https://i.pinimg.com/736x/60/f2/08/60f2085b58498f0e76c9618cf0d65486.jpg');
        }

        .section.industries {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                              url('https://i.pinimg.com/736x/f1/44/24/f14424e8569bac0239ccb6c932d6e862.jpg');
        }

        .section.collectors {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                              url('https://i.pinimg.com/736x/b1/cf/d1/b1cfd1685dbe2886b628aa8b593a5e5a.jpg');
        }

        .contact-info {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            text-align: center;
        }

        .contact-info p {
            margin: 10px;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 30px;
            background: #28a745;
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                text-align: center;
            }

            .navbar .nav-links {
                flex-direction: column;
                width: 100%;
                align-items: center;
            }

            .header {
                padding: 120px 20px;
            }

            .header h1 {
                font-size: 2.5em;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <h1>CitySafisha</h1>
        <div class="nav-links">
            <a href="home.html">Home</a>
            
            <a href="about.php">About us</a>
            <a href="contact.php">Contact</a>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="header">
        <h1>About Us</h1>
        <p>Creating a cleaner and more sustainable environment</p>
    </div>

    <div class="container">
        <div class="section">
            <h2>Who We Are</h2>
            <p>Welcome to CitySafisha, an innovative platform designed for residents, industries, and waste collectors. We are dedicated to providing sustainable and efficient waste management solutions for a cleaner environment.</p>
        </div>

        <div class="section bg-image residents">
            <h2>For Residents</h2>
            <p>Residents can log in to schedule waste pickups, track collection, make payments, and submit complaints. Experience hassle-free waste management with our user-friendly platform.</p>
        </div>

        <div class="section bg-image industries">
            <h2>For Industries</h2>
            <p>Industries can book waste for recycling, schedule pickups, process payments, and monitor their requests. Streamline your waste management processes and contribute to a greener future.</p>
        </div>

        <div class="section bg-image collectors">
            <h2>For Waste Collectors</h2>
            <p>Collectors log in to view assigned pickups, update collection status, and handle industry waste bookings. Optimize your collection routes and improve efficiency with our integrated system.</p>
        </div>

        <div class="section contact-info">
            <h2>Contact Us</h2>
            <p>Email: <a href="mailto:waste@gmail.com">waste@gmail.com</a></p>
            <p>Phone: 0712345678</p>
            <p>Follow us on: <a href="#">Facebook</a>, <a href="#">Twitter</a>, <a href="#">Instagram</a></p>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2025 Waste Management System. All Rights Reserved.</p>
    </div>

</body>
</html>
