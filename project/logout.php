<?php
session_start();
session_unset();  // Clear session variables
session_destroy(); // Destroy the session

// Regenerate session ID for security
session_regenerate_id(true);

// Sanitize user input
$user = filter_input(INPUT_GET, 'user', FILTER_SANITIZE_STRING);

// Redirect based on user type
switch ($user) {
    case 'admin':
        header("Location: adminlogin.php");
        break;
    case 'industry':
        header("Location: indlogin.php");
        break;
    case 'resident':
        header("Location: loginres.php");
        break;
    case 'collector':
        header("Location: collectorlogin.php");
        break;
    default:
        header("Location: home.php"); // Default redirection
        break;
}

exit;
