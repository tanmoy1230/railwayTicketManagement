<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

echo "Welcome " . $_SESSION['email'] . "!<br>";
echo "<a href='logout.php'>Logout</a>";
?>
