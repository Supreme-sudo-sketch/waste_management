<?php
session_start();

// Check if industry is logged in
if (!isset($_SESSION['industry_id'])) {
    header("Location: indlogin.php");
    exit();
}

$industry_name = $_SESSION['industry_name'];
$industry_id = $_SESSION['industry_id'];

$error_message = "";
$success_message = "";

// Database connection
$conn = new mysqli("localhost", "root", "", "waste");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle waste pickup scheduling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['schedule_pickup'])) {
    $waste_type = $_POST['waste_type'];
    $pickup_date = $_POST['pickup_date'];

    if (empty($waste_type) || empty($pickup_date)) {
        $error_message = "All fields are required.";
    } else {
        $sql = "INSERT INTO industry_pickups (industry_id, waste_type, pickup_date) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $industry_id, $waste_type, $pickup_date);

        if ($stmt->execute()) {
            $success_message = "Waste pickup scheduled successfully!";
        } else {
            $error_message = "Failed to schedule pickup.";
        }

        $stmt->close();
    }
}

// Handle waste booking for recycling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_waste'])) {
    $requested_waste_type = $_POST['requested_waste_type'];

    if (empty($requested_waste_type)) {
        $error_message = "Please select the type of waste you need.";
    } else {
        $sql = "INSERT INTO industry_bookings (industry_id, waste_type) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $industry_id, $requested_waste_type);

        if ($stmt->execute()) {
            $success_message = "Waste booked successfully!";
        } else {
            $error_message = "Failed to book waste.";
        }

        $stmt->close();
    }
}

// Handle payment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_payment'])) {
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $transaction_id = $_POST['transaction_id'];
    $payment_for = $_POST['payment_for'];

    if (!empty($amount) && !empty($transaction_id)) {
        $sql = "INSERT INTO payments (user_id, user_type, payment_for, amount, payment_method, transaction_id) 
                VALUES (?, 'industry', ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isdss", $industry_id, $payment_for, $amount, $payment_method, $transaction_id);

        if ($stmt->execute()) {
            $success_message = "Payment submitted successfully!";
        } else {
            $error_message = "Failed to process payment.";
        }

        $stmt->close();
    } else {
        $error_message = "Please fill all fields.";
    }
}

// Handle complaint submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_complaint'])) {
    $complaint = trim($_POST['complaint']);

    if (!empty($complaint)) {
        $sql = "INSERT INTO complaints (user_id, user_type, complaint) VALUES (?, 'industry', ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $industry_id, $complaint);

        if ($stmt->execute()) {
            $success_message = "Complaint submitted successfully!";
        } else {
            $error_message = "Failed to submit complaint.";
        }

        $stmt->close();
    } else {
        $error_message = "Complaint cannot be empty.";
    }
}

// Fetch industry pickups
$sql = "SELECT waste_type, pickup_date, status FROM industry_pickups WHERE industry_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $industry_id);
$stmt->execute();
$pickup_result = $stmt->get_result();
$pickups = $pickup_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch industry bookings
$sql = "SELECT waste_type, status FROM industry_bookings WHERE industry_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $industry_id);
$stmt->execute();
$booking_result = $stmt->get_result();
$bookings = $booking_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Industry Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .container { width: 50%; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; }
        .message { text-align: center; font-size: 16px; margin-bottom: 10px; }
        .error { color: red; }
        .success { color: green; }
        form { margin-bottom: 20px; }
        label { font-weight: bold; display: block; }
        input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; }
        button { width: 100%; padding: 10px; background: #007bff; border: none; color: white; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .logout { text-align: center; margin-top: 20px; }
        .logout a { color: red; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($industry_name); ?>!</h2>

        <?php if (!empty($error_message)) { echo "<p class='message error'>$error_message</p>"; } ?>
        <?php if (!empty($success_message)) { echo "<p class='message success'>$success_message</p>"; } ?>

        <h3>Schedule a Waste Pickup</h3>
        <form action="industries.php" method="POST">
            <label for="waste_type">Waste Type</label>
            <select name="waste_type" required>
                <option value="">Select Waste Type</option>
                <option value="Organic">Organic</option>
                <option value="Recyclable">Recyclable</option>
                <option value="Non-Recyclable">Non-Recyclable</option>
                <option value="Hazardous">Hazardous</option>
            </select>
            <label for="pickup_date">Pickup Date</label>
            <input type="date" name="pickup_date" required>
            <button type="submit" name="schedule_pickup">Schedule Pickup</button>
        </form>

        <h3>Submit a Complaint</h3>
        <form action="industries.php" method="POST">
            <textarea name="complaint" rows="4" required></textarea>
            <button type="submit" name="submit_complaint">Submit Complaint</button>
        </form>

        <div class="logout">
            <a href="logout.php?user=industry">Logout</a>
        </div>
    </div>
</body>
</html>
