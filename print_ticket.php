<?php
session_start();

if (!isset($_GET['booking_id'])) {
    die("Booking ID missing.");
}

$bookingId = $_GET['booking_id'];

echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<div class='container mt-5'>";
echo "<h2 class='text-center'>Train Ticket</h2>";
echo "<div class='border p-4 mt-3'>";
echo "<p><strong>Booking ID:</strong> " . htmlspecialchars($bookingId) . "</p>";
echo "<p><strong>Name:</strong> " . htmlspecialchars($_SESSION['personal_details']['passenger_name'] ?? 'N/A') . "</p>";
echo "<p><strong>Seats:</strong> " . htmlspecialchars(implode(', ', $_SESSION['selected_seats'] ?? [])) . "</p>";
echo "<p><strong>Train:</strong> " . htmlspecialchars($_SESSION['booking_data']['train_name'] ?? 'N/A') . "</p>";
echo "<p><strong>Date:</strong> " . htmlspecialchars($_SESSION['booking_data']['journey_date'] ?? 'N/A') . "</p>";
echo "<p><strong>Total Fare:</strong> ৳" . (($_SESSION['booking_data']['fare'] ?? 0) * count($_SESSION['selected_seats'] ?? [])) . "</p>";
echo "</div>";
echo "<div class='text-center mt-4'><button onclick='window.print();' class='btn btn-primary'>Print Ticket</button></div>";
echo "</div>";
?>

<!-- Add this at the top or bottom of your print_ticket.php page -->

<div class="container mt-4 text-center">
    <a href="nav_bar.php" class="btn btn-secondary">🏠 Home</a>
</div>
