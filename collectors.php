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
        form { margin-top: 20px; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        textarea { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; }
        button { width: 100%; padding: 10px; background: #28a745; border: none; color: white; border-radius: 5px; cursor: pointer; margin-top: 10px; }
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
        <a href="logout.php?user=collector" style="background: red;">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2>Welcome, <?php echo htmlspecialchars($collector_name); ?>!</h2>

        <?php if (!empty($error_message)) { echo "<p class='message error'>$error_message</p>"; } ?>
        <?php if (!empty($success_message)) { echo "<p class='message success'>$success_message</p>"; } ?>

        <!-- View Resident Pickups -->
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
                            <button type="submit" name="update_resident_pickup" style="background: #007bff; color: white;">Mark as Completed</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- View Industry Pickups -->
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
                            <button type="submit" name="update_industry_pickup" style="background: #007bff; color: white;">Mark as Completed</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Submit Complaint -->
        <h3>Submit a Complaint</h3>
        <form action="collectors.php" method="POST">
            <label>Complaint</label>
            <textarea name="complaint" rows="4" placeholder="Enter your complaint..." required></textarea>
            <input type="hidden" name="user_id" value="<?php echo $_SESSION['collector_id']; ?>">
            <input type="hidden" name="user_type" value="collector">
            <button type="submit" name="submit_complaint">Submit Complaint</button>
        </form>

    </div>

</body>
</html>
