<?php
session_start();

// Check if resident is logged in
if (!isset($_SESSION['resident_id'])) {
    header("Location: loginres.php");
    exit();
}

$resident_name = $_SESSION['resident_name'];
$resident_id = $_SESSION['resident_id'];

$error_message = "";
$success_message = "";

// Generate a unique transaction ID to prevent undefined variable errors
$transaction_id = uniqid("TXN", true);

// Database connection
$conn = new mysqli("localhost", "root", "", "waste");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle waste pickup request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['schedule_pickup'])) {
    $waste_type = $_POST['waste_type'];
    $pickup_date = $_POST['pickup_date'];

    if (empty($waste_type) || empty($pickup_date)) {
        $error_message = "All fields are required.";
    } else {
        $sql = "INSERT INTO waste_pickups (resident_id, waste_type, pickup_date, status) VALUES (?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $resident_id, $waste_type, $pickup_date);

        if ($stmt->execute()) {
            $success_message = "Waste pickup scheduled successfully!";
        } else {
            $error_message = "Failed to schedule pickup.";
        }

        $stmt->close();
    }
}

// Handle complaint submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_complaint'])) {
    $complaint = trim($_POST['complaint']);

    if (!empty($complaint)) {
        $sql = "INSERT INTO complaints (user_id, user_type, complaint, status) VALUES (?, 'resident', ?, 'Pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $resident_id, $complaint);

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

// Handle payment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_payment'])) {
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $transaction_id = $_POST['transaction_id'];

    if (!empty($amount) && !empty($transaction_id)) {
        $sql = "INSERT INTO payments (user_id, user_type, payment_for, amount, payment_method, transaction_id, status) 
                VALUES (?, 'resident', 'waste_collection', ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("idss", $resident_id, $amount, $payment_method, $transaction_id);

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

// Fetch resident's scheduled pickups
$sql = "SELECT waste_type, pickup_date, status FROM waste_pickups WHERE resident_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $resident_id);
$stmt->execute();
$result = $stmt->get_result();
$pickups = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Dashboard</title>
    <style>
    body { 
        font-family: Arial, sans-serif; 
        background: url('https://i.pinimg.com/736x/d5/97/50/d59750a5b56c821ea7114f0adf1e132b.jpg') no-repeat center center fixed; 
        background-size: cover; 
    }
    .container { 
        width: 50%; 
        margin: auto; 
        background: white; 
        padding: 20px; 
        border-radius: 8px; 
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); 
        opacity: 0.95; /* Slight transparency for better readability */
    }
    h2 { text-align: center; }
    .message { text-align: center; font-size: 16px; margin-bottom: 10px; }
    .error { color: red; }
    .success { color: green; }
    form { margin-bottom: 20px; }
    label { font-weight: bold; display: block; margin-top: 10px; }
    input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; }
    button { width: 100%; padding: 10px; background: #28a745; border: none; color: white; border-radius: 5px; cursor: pointer; margin-top: 10px; }
    button:hover { background: #218838; }
    .logout { text-align: center; margin-top: 20px; }
    .logout a { color: red; text-decoration: none; font-weight: bold; }
</style>

</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($resident_name); ?>!</h2>

        <?php if (!empty($error_message)) { echo "<p class='message error'>$error_message</p>"; } ?>
        <?php if (!empty($success_message)) { echo "<p class='message success'>$success_message</p>"; } ?>

        <!-- Schedule Waste Pickup -->
        <h3>Schedule a Waste Pickup</h3>
        <form action="residents.php" method="POST">
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

        <!-- Submit Complaint -->
        <h3>Submit a Complaint</h3>
        <form action="residents.php" method="POST">
            <label>Complaint</label>
            <textarea name="complaint" rows="4" required></textarea>
            <button type="submit" name="submit_complaint">Submit Complaint</button>
        </form>

        <!-- Pay for Waste Collection -->
        <h3>Pay for Waste Collection</h3>
<form action="residents.php" method="POST">
    <label>Amount</label>
    <input type="number" name="amount" step="0.01" required>
    
    <label>Payment Method</label>
    <select name="payment_method" required>
        <option value="Cash">Cash</option>
        <option value="Bank Transfer">Bank Transfer</option>
        <option value="Mobile Payment">Mobile Payment</option>
    </select>
    
    <label>Transaction ID</label>
    <input type="text" name="transaction_id" value="<?php echo htmlspecialchars($transaction_id); ?>" readonly>
    
    <button type="submit" name="submit_payment">Submit Payment</button>
</form>


        <!-- Logout -->
        <div class="logout">
            <a href="logout.php?user=resident">Logout</a>
        </div>
    </div>
</body>
</html>
