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

// Generate a unique transaction ID to prevent undefined variable errors
$transaction_id = uniqid("TXN", true);

// Database connection
$conn = new mysqli("localhost", "root", "", "waste");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle waste pickup scheduling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['schedule_pickup'])) {
    $waste_type = $_POST['waste_type'];
    $pickup_date = $_POST['pickup_date'];

    if (!empty($waste_type) && !empty($pickup_date)) {
        $stmt = $conn->prepare("INSERT INTO industry_pickups (industry_id, waste_type, pickup_date, status) VALUES (?, ?, ?, 'Pending')");
        $stmt->bind_param("iss", $industry_id, $waste_type, $pickup_date);
        if ($stmt->execute()) {
            $success_message = "Waste pickup scheduled successfully!";
        } else {
            $error_message = "Failed to schedule pickup: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "All fields are required.";
    }
}


// Handle waste booking for recycling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_waste'])) {
    $requested_waste_type = $_POST['requested_waste_type'];
    
    if (!empty($requested_waste_type)) {
        $stmt = $conn->prepare("INSERT INTO industry_bookings (industry_id, waste_type, status) VALUES (?, ?, 'Pending')");
        $stmt->bind_param("is", $industry_id, $requested_waste_type);
        
        if ($stmt->execute()) {
            $success_message = "Waste booked successfully!";
        } else {
            $error_message = "Failed to book waste.";
        }
        $stmt->close();
    } else {
        $error_message = "Please select the type of waste you need.";
    }
}

// Handle payments
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_payment'])) {
    $payment_for = $_POST['payment_for'];
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    // Generate a unique transaction ID
    $transaction_id = uniqid("TXN", true); // Generates a unique transaction ID


   
    if (!empty($amount) && !empty($payment_for)) {
        $stmt = $conn->prepare("INSERT INTO payments (user_id, user_type, payment_for, amount, payment_method, transaction_id, status) 
                                VALUES (?, 'industry', ?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("isdss", $industry_id, $payment_for, $amount, $payment_method, $transaction_id);
        
        if ($stmt->execute()) {
            $success_message = "Payment submitted successfully!";
        } else {
            $error_message = "Failed to process payment: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "Please fill all required fields.";
    }
}


// Handle complaint submission (Feedback)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_complaint'])) {
    $complaint = trim($_POST['complaint']);
    
    if (!empty($complaint)) {
        $stmt = $conn->prepare("INSERT INTO complaints (user_id, user_type, complaint, status) VALUES (?, 'industry', ?, 'Pending')");
        $stmt->bind_param("is", $industry_id, $complaint);
        $stmt->execute() ? $success_message = "Complaint submitted successfully!" : $error_message = "Failed to submit complaint.";
        $stmt->close();
    } else {
        $error_message = "Complaint cannot be empty.";
    }
}

// Fetch industry bookings pickups and payments
$bookings = $conn->query("SELECT waste_type, status FROM industry_bookings WHERE industry_id = $industry_id ORDER BY id DESC");

$pickups = $conn->query("SELECT waste_type, pickup_date, status FROM industry_pickups WHERE industry_id = $industry_id ORDER BY pickup_date DESC");

$payments = $conn->query("SELECT payment_for, amount, payment_method, transaction_id, status FROM payments WHERE user_id = $industry_id ORDER BY id DESC");

$complaints = $conn->query("SELECT complaint, status FROM complaints WHERE user_id = $industry_id ORDER BY id DESC");


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Industry Dashboard</title>
    <style>
         body { 
        font-family: Arial, sans-serif; 
        background: url('background.jpg') no-repeat center center fixed; 
        background-size: cover; 
        margin: 20px;
    }
        h2 { color: #333; }
        h3 { color: #555; margin-top: 20px; }
        form { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; color: #333; }
        input[type="date"], input[type="number"], input[type="text"], select { width: calc(100% - 22px); padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #45a049; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .logout-btn { display: inline-block; padding: 10px 15px; background-color: #d9534f; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .logout-btn:hover { background-color: #c9302c; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($industry_name); ?>!</h2>
    
    <?php if (!empty($error_message)) echo "<p class='error'>$error_message</p>"; ?>
    <?php if (!empty($success_message)) echo "<p class='success'>$success_message</p>"; ?>

    <h3>Schedule a Waste Pickup</h3>
    <form method="POST">
        <label>Waste Type</label>
        <select name="waste_type" required>
            <option value="">Select Waste Type</option>
            <option value="Organic">Organic</option>
            <option value="Recyclable">Recyclable</option>
            <option value="Non-Recyclable">Non-Recyclable</option>
            <option value="Hazardous">Hazardous</option>
        </select>
        <label>Pickup Date</label>
        <input type="date" name="pickup_date" required>
        <button type="submit" name="schedule_pickup">Schedule Pickup</button>
    </form>
    
    <h3>Book Waste for Recycling</h3>
    <form method="POST">
        <label>Waste Type</label>
        <select name="requested_waste_type" required>
            <option value="">Select Waste Type</option>
            <option value="Organic">Organic</option>
            <option value="Recyclable">plastic</option>
            <option value="Non-Recyclable">metal</option>
            <option value="Hazardous">glass</option>
        </select>
        <button type="submit" name="book_waste">Book Waste</button>
    </form>

    <h3>Make a Payment</h3>
    <form method="POST">
        <label>Payment For</label>
        <select name="payment_for" required>
            <option value="">Select Payment Type</option>
            <option value="Waste Pickup">Waste Pickup</option>
            <option value="Booking">Booking</option>
            <option value="Both">Both</option>
        </select>
        <label>Amount</label>
        <input type="number" name="amount" required>
        <label>Payment Method</label>
        <select name="payment_method" required>
            <option value="Cash">Cash</option>
            <option value="Card">Card</option>
            <option value="Bank Transfer">Bank Transfer</option>
        </select>
        <label>Transaction ID</label>
<input type="text" name="transaction_id" value="<?php echo htmlspecialchars($transaction_id); ?>" readonly>


        <button type="submit" name="submit_payment">Submit Payment</button>
    </form>

    <h3>Submit Feedback / Complaint</h3>
    <form method="POST">
        <label>Complaint</label>
        <textarea name="complaint" rows="4" required></textarea>
        <button type="submit" name="submit_complaint">Submit Complaint</button>
    </form>

    <a href="logout.php?user=industry" class="logout-btn">Logout</a>
</body>
</html>