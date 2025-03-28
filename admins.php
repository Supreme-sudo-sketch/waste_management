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
    $payment_id = intval($_POST['payment_id']); // Convert to integer for security

    if ($payment_id > 0) {
        $stmt = $conn->prepare("UPDATE payments SET status = 'Completed' WHERE id = ?");
        $stmt->bind_param("i", $payment_id);
        
        if ($stmt->execute()) {
            $success_message = "Payment marked as completed!";
        } else {
            $error_message = "Failed to update payment status: " . $conn->error;
        }

        $stmt->close();
    } else {
        $error_message = "Invalid payment ID.";
    }
}

// Handle complaint status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['resolve_complaint'])) {
    $complaint_id = intval($_POST['complaint_id']);
    if ($complaint_id > 0) {
        $stmt = $conn->prepare("UPDATE complaints SET status = 'Resolved' WHERE id = ?");
        $stmt->bind_param("i", $complaint_id);
        $stmt->execute() ? $success_message = "Complaint marked as resolved!" : $error_message = "Failed to update complaint status.";
        $stmt->close();
    }
}

// Fetch resident waste pickups with assigned collector
$resident_pickups = $conn->query("
    SELECT w.id, r.name AS resident_name, w.waste_type, w.pickup_date, w.status, 
           COALESCE(c.name, 'Not Assigned') AS collector_name
    FROM waste_pickups w
    JOIN residents r ON w.resident_id = r.id
    LEFT JOIN collectors c ON w.collector_id = c.id
    ORDER BY w.pickup_date DESC
");

// Fetch industry waste pickups with assigned collector
$industry_pickups = $conn->query("
    SELECT i.id, ind.name AS industry_name, i.waste_type, i.pickup_date, i.status, 
           COALESCE(c.name, 'Not Assigned') AS collector_name
    FROM industry_pickups i
    JOIN industries ind ON i.industry_id = ind.id
    LEFT JOIN collectors c ON i.collector_id = c.id
    ORDER BY i.pickup_date DESC
");

// Fetch industry waste bookings with assigned collector
$industry_bookings = $conn->query("
    SELECT b.id, ind.name AS industry_name, b.waste_type, b.status, 
           COALESCE(c.name, 'Not Assigned') AS collector_name
    FROM industry_bookings b
    JOIN industries ind ON b.industry_id = ind.id
    LEFT JOIN collectors c ON b.collector_id = c.id
    ORDER BY b.status DESC
");

// Fetch payments
$payments = $conn->query("SELECT * FROM payments ORDER BY created_at DESC");

// Fetch complaints
$complaints = $conn->query("SELECT id, user_id, user_type, complaint, status FROM complaints ORDER BY id DESC");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f4f4; display: flex; }
        .sidenav { width: 220px; background: #343a40; color: white; height: 100vh; padding-top: 20px; position: fixed; }
        .sidenav h2 { text-align: center; }
        .sidenav a { display: block; color: white; padding: 12px; text-decoration: none; transition: 0.3s; }
        .sidenav a:hover { background: #495057; }
        .container { margin-left: 230px; padding: 20px; width: 100%; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .btn { padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-complete { background: #28a745; color: white; }
        .btn-complete:hover { background: #218838; }
    </style>
</head>
<body>
    <div class="sidenav">
        <h2>Admin Panel</h2>
        <a href="#resident_pickups"><i class="fas fa-users"></i> Resident Pickups</a>
        <a href="#industry_pickups"><i class="fas fa-industry"></i> Industry Pickups</a>
        <a href="#industry_bookings"><i class="fas fa-recycle"></i> Industry Bookings</a>
        <a href="#payments"><i class="fas fa-credit-card"></i> Payments</a>
        <a href="#complaints"><i class="fas fa-comments"></i> Complaints</a>
        <a href="logout.php?user=admin"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($admin_name); ?> (Admin)</h2>

        <!-- Resident Waste Pickups -->
        <h3 id="resident_pickups">Resident Waste Pickups</h3>
        <table>
            <tr><th>Resident Name</th><th>Waste Type</th><th>Pickup Date</th><th>Status</th><th>Collected By</th></tr>
            <?php while ($row = $resident_pickups->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['resident_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['waste_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['pickup_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['collector_name']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Industry Waste Pickups -->
        <h3 id="industry_pickups">Industry Waste Pickups</h3>
        <table>
            <tr><th>Industry Name</th><th>Waste Type</th><th>Pickup Date</th><th>Status</th><th>Collected By</th></tr>
            <?php while ($row = $industry_pickups->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['industry_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['waste_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['pickup_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['collector_name']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Industry Waste Bookings -->
        <h3 id="industry_bookings">Industry Waste Bookings</h3>
        <table>
            <tr><th>Industry Name</th><th>Waste Type</th><th>Status</th><th>Collected By</th></tr>
            <?php while ($row = $industry_bookings->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['industry_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['waste_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['collector_name']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Payments Section -->
<h3 id="payments">Industry Payments</h3>
<table>
    <tr><th>User Type</th><th>Amount</th><th>Payment Method</th><th>Transaction ID</th><th>Status</th><th>Action</th></tr>
    <?php while ($row = $payments->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['user_type']); ?></td>
            
            </td>
            <td><?php echo htmlspecialchars($row['amount']); ?></td>
            <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
            <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td>
                <?php if ($row['status'] == 'Pending'): ?>
                    <form action="admins.php" method="POST">
                        <input type="hidden" name="payment_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="mark_completed" class="btn btn-complete">Mark as Completed</button>
                    </form>
                <?php else: ?>
                    <span style="color: green; font-weight: bold;">Completed</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

        <!-- Complaints Section -->
        <h3 id="complaints">Complaints</h3>
        <table>
            <tr><th>User ID</th><th>User Type</th><th>Complaint</th><th>Status</th><th>Action</th></tr>
            <?php while ($row = $complaints->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['complaint']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <?php if ($row['status'] == 'Pending'): ?>
                            <form action="admins.php" method="POST">
                                <input type="hidden" name="complaint_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="resolve_complaint" class="btn btn-resolve">Mark as Resolved</button>
                            </form>
                        <?php else: ?>
                            <span style="color: green; font-weight: bold;">Resolved</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

    </div>
</body>
</html>
