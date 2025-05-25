<?php
session_start(); // Start the session to access user data

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Optionally fetch the logged-in user's name or any other data if needed
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest'; // Using user_name stored in the session

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Ticket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/book_ticket.css">
</head>
<body>

<div class="booking-box">
    <h2>Book Your Ticket</h2>

    <!-- Optionally, display the logged-in user's name or some information -->
    <p>Welcome, <?= htmlspecialchars($userName); ?>! Please book your ticket below.</p>

    <!-- Standard form submission to search_trains.php -->
    <form method="POST" action="search_trains.php">
        
        <!-- Start Station -->
        <div class="mb-3">
            <label for="start_station" class="form-label">Start Station</label>
            <select id="start_station" name="start_station" class="form-select" required>
                <?php include 'get_stations.php'; ?>
            </select>
        </div>

        <!-- End Station -->
        <div class="mb-3">
            <label for="end_station" class="form-label">End Station</label>
            <select id="end_station" name="end_station" class="form-select" required>
                <?php include 'get_stations.php'; ?>
            </select>
        </div>

        <!-- Seat Type -->
        <div class="mb-3">
            <label for="seat_type" class="form-label">Seat Type</label>
            <select id="seat_type" name="seat_type" class="form-select" required>
                <?php include 'get_carriage.php'; ?>
            </select>
        </div>

        <!-- Journey Date -->
        <div class="mb-4">
            <label for="journey_date" class="form-label">Journey Date</label>
            <input type="date" id="journey_date" name="journey_date" class="form-control"
                required min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+3 days')) ?>">
        </div>

        <button type="submit" class="btn btn-primary w-100">Search Trains</button>
    </form>
</div>

</body>
</html>
