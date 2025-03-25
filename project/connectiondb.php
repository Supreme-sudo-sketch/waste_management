<?php
$host = "localhost"; // Change if your DB is hosted elsewhere
$user = "root"; // Change to your DB username
$password = ""; // Change to your DB password (leave empty if no password)
$database = "waste"; // Your database name

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
