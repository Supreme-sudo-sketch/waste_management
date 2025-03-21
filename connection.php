<?php
// Connect to MySQL server
$server = "localhost";
$serveruseraccount = "root";
$serveruserpassword = "";

// Establish a connection to the database server
$connect = mysqli_connect($server, $serveruseraccount, $serveruserpassword);

// Check if successfully connected
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connection Successful<br>";

// Create a database
$query = "CREATE DATABASE waste";

// Execute query
$execute = mysqli_query($connect, $query);

// Check if successful
if ($execute) {
    echo "Database created Successfully";
} else {
    die("Error creating database: " . mysqli_error($connect));
}

// Close the connection
mysqli_close($connect);
?>
