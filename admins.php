<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit();
}

$admin_name = $_SESSION['admin_name'];

$error_message = "";
$success_message = "";

// Database connection
$conn = new mysqli("localhost", "root", "", "waste");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle payment status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mark_completed'])) {
    $payment_id = $_POST['payment_id'];
    $sql = "UPDATE payments SET status = 'Completed' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $payment_id);

    if ($stmt->execute()) {
        $success_message = "Payment marked as completed!";
    } else {
        $error_message = "Failed to update payment status.";
    }

    $stmt->close();
}

// Handle complaint review
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mark_reviewed'])) {
    $complaint_id = $_POST['complaint_id'];
    $sql = "UPDATE complaints SET status = 'Reviewed' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $complaint_id);

    if ($stmt->execute()) {
        $success_message = "Complaint marked as reviewed!";
    } else {
        $error_message = "Failed to update complaint status.";
    }

    $stmt->close();
}

// Fetch data from database
$resident_pickups = $conn->query("SELECT residents.name, waste_type, pickup_date, status FROM waste_pickups 
    JOIN residents ON waste_pickups.resident_id = residents.id ORDER BY pickup_date DESC");
    
$industry_pickups = $conn->query("SELECT industries.name, waste_type, pickup_date, status FROM industry_pickups 
    JOIN industries ON industry_pickups.industry_id = industries.id ORDER BY pickup_date DESC");

$complaints = $conn->query("SELECT * FROM complaints ORDER BY created_at DESC");

$payments = $conn->query("SELECT * FROM payments ORDER BY created_at DESC");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { display: flex; background: #f4f4f4; }

        /* Sidebar */
        .sidebar { width: 250px; background: #007bff; height: 100vh; color: white; padding: 20px; }
        .sidebar h2 { margin-bottom: 20px; }
        .sidebar a { display: block; color: white; padding: 10px; text-decoration: none; border-radius: 5px; margin-bottom: 10px; }
        .sidebar a:hover { background: #0056b3; }
        
        /* Main Content */
        .main-content { flex-grow: 1; padding: 20px; background: white; border-radius: 10px; margin: 20px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; color: #333; }
        .message { text-align: center; font-size: 16px; margin-bottom: 10px; }
        .error { color: red; }
        .success { color: green; }

        /* Table Styling */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #007bff; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }

        /* Buttons */
        .btn { padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-approve { background: #28a745; color: white; }
        .btn-reject { background: #dc3545; color: white; }
        .btn:hover { opacity: 0.8; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <a href="admins.php">Home</a>
        <a href="#pickup_requests">Pickup Requests</a>
        <a href="#complaints">Complaints</a>
        <a href="#payments">Payments</a>
        <a href="logout.php?user=admin" style="background: red;">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2>Welcome, <?php echo htmlspecialchars($admin_name); ?> (Admin)</h2>

        <?php if (!empty($error_message)) { echo "<p class='message error'>$error_message</p>"; } ?>
        <?php if (!empty($success_message)) { echo "<p class='message success'>$success_message</p>"; } ?>

        <!-- Pickup Requests -->
        <h3 id="pickup_requests">Resident Waste Pickup Requests</h3>
        <table>
            <tr>
                <th>Resident Name</th>
                <th>Waste Type</th>
                <th>Pickup Date</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $resident_pickups->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['waste_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['pickup_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Complaints -->
        <h3 id="complaints">User Complaints</h3>
        <table>
            <tr>
                <th>User Type</th>
                <th>Complaint</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $complaints->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['user_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['complaint']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <?php if ($row['status'] == 'Pending'): ?>
                            <form action="admins.php" method="POST">
                                <input type="hidden" name="complaint_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="mark_reviewed" class="btn btn-approve">Mark as Reviewed</button>
                            </form>
                        <?php else: ?>
                            <span>Reviewed</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Payments -->
        <h3 id="payments">Payments Transactions</h3>
        <table>
            <tr>
                <th>User Type</th>
                <th>Payment For</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Transaction ID</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $payments->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['user_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['payment_for']); ?></td>
                    <td><?php echo htmlspecialchars($row['amount']); ?></td>
                    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                    <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

    </div>

</body>
</html>
