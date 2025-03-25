<?php
session_start();

// Check if collector is logged in
if (!isset($_SESSION['collector_id'])) {
    header("Location: collectorlogin.php");
    exit();
}

$collector_name = $_SESSION['collector_name'];
$error_message = "";
$success_message = "";

// Database connection
$conn = new mysqli("localhost", "root", "", "waste");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// **Handle resident waste pickup status update**
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_resident_pickup'])) {
    $pickup_id = $_POST['pickup_id'];
    $status = "Completed";

    $sql = "UPDATE waste_pickups SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $pickup_id);

    if ($stmt->execute()) {
        $success_message = "Resident waste pickup marked as completed!";
    } else {
        $error_message = "Failed to update resident waste pickup status.";
    }
    $stmt->close();
}

// **Handle industry waste pickup status update**
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_industry_pickup'])) {
    $pickup_id = $_POST['pickup_id'];
    $status = "Completed";

    $sql = "UPDATE industry_pickups SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $pickup_id);

    if ($stmt->execute()) {
        $success_message = "Industry waste pickup marked as completed!";
    } else {
        $error_message = "Failed to update industry waste pickup status.";
    }
    $stmt->close();
}

// **Handle industry waste booking status update**
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_industry_booking'])) {
    $booking_id = $_POST['booking_id'];
    $status = "Completed";

    $sql = "UPDATE industry_bookings SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $booking_id);

    if ($stmt->execute()) {
        $success_message = "Industry waste booking marked as completed!";
    } else {
        $error_message = "Failed to update industry waste booking status.";
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_resident_pickup'])) {
    $pickup_id = $_POST['pickup_id'];
    $collector_id = $_SESSION['collector_id'];

    $stmt = $conn->prepare("UPDATE waste_pickups SET status = 'Completed', collector_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $collector_id, $pickup_id);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_industry_pickup'])) {
    $pickup_id = $_POST['pickup_id'];
    $collector_id = $_SESSION['collector_id'];

    $stmt = $conn->prepare("UPDATE industry_pickups SET status = 'Completed', collector_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $collector_id, $pickup_id);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_industry_booking'])) {
    $booking_id = $_POST['booking_id'];
    $collector_id = $_SESSION['collector_id'];

    $stmt = $conn->prepare("UPDATE industry_bookings SET status = 'Completed', collector_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $collector_id, $booking_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch resident waste pickups
$sql = "SELECT waste_pickups.id, residents.name AS resident_name, waste_pickups.waste_type, waste_pickups.pickup_date, waste_pickups.status 
        FROM waste_pickups 
        JOIN residents ON waste_pickups.resident_id = residents.id 
        WHERE waste_pickups.status = 'Pending' 
        ORDER BY waste_pickups.pickup_date ASC";
$resident_pickups = $conn->query($sql);

// Fetch industry waste pickups
$sql = "SELECT industry_pickups.id, industries.name AS industry_name, industry_pickups.waste_type, industry_pickups.pickup_date, industry_pickups.status 
        FROM industry_pickups 
        JOIN industries ON industry_pickups.industry_id = industries.id 
        WHERE industry_pickups.status = 'Pending' 
        ORDER BY industry_pickups.pickup_date ASC";
$industry_pickups = $conn->query($sql);

// Fetch industry waste bookings
$sql = "SELECT industry_bookings.id, industries.name AS industry_name, industry_bookings.waste_type, industry_bookings.status 
        FROM industry_bookings 
        JOIN industries ON industry_bookings.industry_id = industries.id 
        WHERE industry_bookings.status = 'Pending' 
        ORDER BY industry_bookings.id ASC";
$industry_bookings = $conn->query($sql);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waste Collector Dashboard</title>
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

        /* Form Styling */
        form { margin-top: 10px; display: inline-block; }
        button { padding: 8px 12px; background: #28a745; border: none; color: white; border-radius: 5px; cursor: pointer; }
        button:hover { background: #218838; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Collector Dashboard</h2>
        <a href="collectors.php">Home</a>
        <a href="#resident_pickups">Resident Pickups</a>
        <a href="#industry_pickups">Industry Pickups</a>
        <a href="#industry_bookings">Industry Bookings</a>
        <a href="logout.php?user=collector" style="background: red;">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2>Welcome, Collector!</h2>
        <h3 id="resident_pickups">Resident Waste Pickups</h3>
<table>
    <tr>
        <th>Resident Name</th>
        <th>Waste Type</th>
        <th>Pickup Date</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $resident_pickups->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['resident_name']); ?></td>
            <td><?php echo htmlspecialchars($row['waste_type']); ?></td>
            <td><?php echo htmlspecialchars($row['pickup_date']); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td>
                <form action="collectors.php" method="POST">
                    <input type="hidden" name="pickup_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="update_resident_pickup">Mark as Completed</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

        <h3 id="industry_pickups">Industry Waste Pickups</h3>
<table>
    <tr>
        <th>Industry Name</th>
        <th>Waste Type</th>
        <th>Pickup Date</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $industry_pickups->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['industry_name']); ?></td>
            <td><?php echo htmlspecialchars($row['waste_type']); ?></td>
            <td><?php echo htmlspecialchars($row['pickup_date']); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td>
                <form action="collectors.php" method="POST">
                    <input type="hidden" name="pickup_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="update_industry_pickup">Mark as Completed</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

        <h3 id="industry_bookings">Industry Waste Bookings</h3>
<table>
    <tr>
        <th>Industry Name</th>
        <th>Waste Type</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $industry_bookings->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['industry_name']); ?></td>
            <td><?php echo htmlspecialchars($row['waste_type']); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td>
                <form action="collectors.php" method="POST">
                    <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="update_industry_booking">Mark as Completed</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
    </div>
</body>
</html>
