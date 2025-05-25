<?php

$conn = new mysqli("localhost", "root", "", "railway ticket management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_GET['booking_id'])) {
    $booking_id = intval($_GET['booking_id']);

    // Get Booking Info
    $booking_sql = "SELECT b.*, bs.status_name 
                    FROM bookings b 
                    JOIN booking_status bs ON b.status_id = bs.status_id 
                    WHERE b.booking_id = ?";
    $stmt = $conn->prepare($booking_sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $booking_result = $stmt->get_result();

    if ($booking_result->num_rows > 0) {
        $booking = $booking_result->fetch_assoc();

        echo "<div class='container mt-5'>";
        echo "<h4>Booking Details (ID: {$booking['booking_id']})</h4>";
        echo "<ul class='list-group'>";
        echo "<li class='list-group-item'>Train Code: {$booking['train_code']}</li>";
        echo "<li class='list-group-item'>Travel Date: {$booking['travel_date']}</li>";
        echo "<li class='list-group-item'>Total Fare: {$booking['total_fare']}</li>";
        echo "<li class='list-group-item'>Booking Time: {$booking['booking_time']}</li>";
        echo "<li class='list-group-item'>Status: {$booking['status_name']}</li>";
        echo "</ul><br>";

        // Get Passenger Info
        $passenger_sql = "SELECT p.name, p.age, g.name as gender, p.carriage_id
                          FROM passengers p
                          JOIN genders g ON p.gender_id = g.gender_id
                          WHERE p.booking_id = ?";
        $stmt2 = $conn->prepare($passenger_sql);
        $stmt2->bind_param("i", $booking_id);
        $stmt2->execute();
        $passenger_result = $stmt2->get_result();

        echo "<h5>Passengers</h5><table class='table table-bordered'><tr><th>Name</th><th>Age</th><th>Gender</th><th>Carriage</th></tr>";
        while ($row = $passenger_result->fetch_assoc()) {
            echo "<tr><td>{$row['name']}</td><td>{$row['age']}</td><td>{$row['gender']}</td><td>{$row['carriage_id']}</td></tr>";
        }
        echo "</table>";

        // Get Payment Info
        $payment_sql = "SELECT p.amount, p.payment_date, ps.status_name, pm.method_name 
                        FROM payments p
                        JOIN payment_status ps ON p.status_id = ps.status_id
                        JOIN payment_method pm ON p.method_id = pm.method_id
                        WHERE p.booking_id = ?";
        $stmt3 = $conn->prepare($payment_sql);
        $stmt3->bind_param("i", $booking_id);
        $stmt3->execute();
        $payment_result = $stmt3->get_result();

        echo "<h5>Payment Info</h5>";
        if ($payment_result->num_rows > 0) {
            while ($pay = $payment_result->fetch_assoc()) {
                echo "<ul class='list-group'>";
                echo "<li class='list-group-item'>Amount: {$pay['amount']}</li>";
                echo "<li class='list-group-item'>Date: {$pay['payment_date']}</li>";
                echo "<li class='list-group-item'>Method: {$pay['method_name']}</li>";
                echo "<li class='list-group-item'>Status: {$pay['status_name']}</li>";
                echo "</ul><br>";
            }
        } else {
            echo "<p>No payment record found.</p>";
        }

        // Get Seat Info
        $seat_sql = "SELECT carriage_id, seat_number FROM seat_reservations WHERE booking_id = ?";
        $stmt4 = $conn->prepare($seat_sql);
        $stmt4->bind_param("i", $booking_id);
        $stmt4->execute();
        $seat_result = $stmt4->get_result();

        echo "<h5>Seat Reservations</h5>";
        if ($seat_result->num_rows > 0) {
            echo "<table class='table table-bordered'><tr><th>Carriage</th><th>Seat Number</th></tr>";
            while ($seat = $seat_result->fetch_assoc()) {
                echo "<tr><td>{$seat['carriage_id']}</td><td>{$seat['seat_number']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No seat reservations found.</p>";
        }

        echo "</div>";
    } else {
        echo "<div class='container mt-5 alert alert-danger'>No booking found for ID $booking_id</div>";
    }
}
?>
