<?php
session_start();
session_unset();  // Clear all session variables
session_destroy(); // Destroy the session

// Redirect based on user type
if (isset($_GET['user']) && $_GET['user'] === 'admin') {
    header("Location: adminlogin.php");
} elseif (isset($_GET['user']) && $_GET['user'] === 'industry') {
    header("Location: indlogin.php");
} elseif (isset($_GET['user']) && $_GET['user'] === 'resident') {
    header("Location: loginres.php");
} elseif (isset($_GET['user']) && $_GET['user'] === 'collector') {
    header("Location: collectorlogin.php");
} else {
    header("Location: index.php"); // Default redirection to home page
}
exit();
