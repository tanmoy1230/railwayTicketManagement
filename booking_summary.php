<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .confirmation-box { background-color: #e9ecef; padding: 20px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
    .confirmation-btn { margin-top: 20px; text-align: center; }
    .payment-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; margin-top: 20px; border-radius: 8px; }
    .payment-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 15px; margin-top: 20px; border-radius: 8px; }
</style>

<?php
session_start();
$conn = new mysqli("localhost", "root", "", "railway ticket management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['personal_details'], $_SESSION['booking_data'], $_SESSION['selected_seats'], $_SESSION['id'])) {
    die("Missing session data.");
}

$personal = $_SESSION['personal_details'];
$booking = $_SESSION['booking_data'];
$selectedSeats = $_SESSION['selected_seats'];
$userId = $_SESSION['id'];

$trainCode = $booking['train_code'];
$journeyDate = $booking['journey_date'];
$farePerSeat = $booking['fare'];
$totalFare = $farePerSeat * count($selectedSeats);
$bookingTime = date("Y-m-d H:i:s");

$statusBooked = 2;
$paymentPaid = 2;

$paymentSuccessful = false;
$paymentError = null;
$bookingId = $_SESSION['current_booking_id'] ?? null;
$paymentId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['payment_id'])) {
    // Payment submission
    $paymentId = $_POST['payment_id'];
    $bookingIdPost = $_POST['booking_id'];

    if ($bookingIdPost == $_SESSION['current_booking_id']) {
        $method = intval($personal['payment_method']);
        if (($method === 1 || $method === 2) && isset($_POST['card_number'], $_POST['card_expiry'], $_POST['card_cvc'])) {
            $paymentSuccessful = true;
        } elseif (($method === 3 || $method === 4) && isset($_POST['phone_number'])) {
            $paymentSuccessful = true;
        } else {
            $paymentError = "Invalid payment details.";
        }

        if ($paymentSuccessful) {
            $conn->query("UPDATE payments SET status_id = $paymentPaid WHERE payment_id = $paymentId");
            $conn->query("UPDATE bookings SET status_id = $statusBooked WHERE booking_id = $bookingId");
        }
    } else {
        $paymentError = "Booking ID mismatch.";
    }
} elseif (!$bookingId) {
    // Insert booking & related data only if not yet saved
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, train_code, travel_date, total_fare, booking_time, status_id) VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("issds", $userId, $trainCode, $journeyDate, $totalFare, $bookingTime);
        $stmt->execute();
        $bookingId = $stmt->insert_id;
        $_SESSION['current_booking_id'] = $bookingId;
        $stmt->close();

        $gender = intval($personal['gender']);
        $carriageId = 1;

        $passengerStmt = $conn->prepare("INSERT INTO passengers (booking_id, name, age, gender_id, carriage_id) VALUES (?, ?, ?, ?, ?)");
        $passengerStmt->bind_param("isiii", $bookingId, $personal['passenger_name'], $personal['age'], $gender, $carriageId);
        $passengerStmt->execute();
        $passengerStmt->close();

        $reserveStmt = $conn->prepare("INSERT INTO seat_reservations (booking_id, train_code, carriage_id, seat_number, journey_date) VALUES (?, ?, ?, ?, ?)");
        foreach ($selectedSeats as $seat) {
            $reserveStmt->bind_param("isiss", $bookingId, $trainCode, $carriageId, $seat, $journeyDate);
            $reserveStmt->execute();
        }
        $reserveStmt->close();

        $count = count($selectedSeats);
        $seatUpdate = $conn->prepare("UPDATE seat_availability SET reserved_seats = reserved_seats + ?, available_seat = available_seat - ? WHERE train_code = ? AND carriage_id = ? AND journey_date = ?");
        $seatUpdate->bind_param("iisis", $count, $count, $trainCode, $carriageId, $journeyDate);
        $seatUpdate->execute();
        $seatUpdate->close();

        $method = intval($personal['payment_method']);
        $paymentStmt = $conn->prepare("INSERT INTO payments (booking_id, amount, method_id, status_id) VALUES (?, ?, ?, 1)");
        $paymentStmt->bind_param("idi", $bookingId, $totalFare, $method);
        $paymentStmt->execute();
        $paymentId = $paymentStmt->insert_id;
        $paymentStmt->close();

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "<div class='alert alert-danger'>Booking failed: " . $e->getMessage() . "</div>";
        exit;
    }
}

echo '<div class="container mt-5">';
echo "<h2 class='text-center mb-4'>Booking Summary & Payment</h2>";

if ($paymentError) {
    echo "<div class='payment-error'>" . htmlspecialchars($paymentError) . "</div>";
}

echo "<div class='confirmation-box'>";
echo "<p><strong>Booking ID:</strong> $bookingId</p>";
echo "<p><strong>Train:</strong> " . htmlspecialchars($booking['train_name']) . "</p>";
echo "<p><strong>Date:</strong> " . htmlspecialchars($journeyDate) . "</p>";
echo "<p><strong>Fare:</strong> ৳$totalFare</p>";
echo "<p><strong>Seat Type:</strong> " . htmlspecialchars($booking['seat_type']) . "</p>";
echo "<p><strong>Seats:</strong> " . htmlspecialchars(implode(', ', $selectedSeats)) . "</p>";
echo "<p><strong>Passenger:</strong> " . htmlspecialchars($personal['passenger_name']) . " (" . $personal['age'] . " yrs, " . ($personal['gender'] == 1 ? 'Male' : 'Female') . ")</p>";
echo "<p><strong>Payment Method:</strong> " . match(intval($personal['payment_method'])) {
    1 => "Credit Card", 2 => "Debit Card", 3 => "bKash", 4 => "Nagad", default => "Unknown"
} . "</p>";
echo "</div>";

if (!$paymentSuccessful) {
    echo "<form method='POST' class='mt-4'>";
    echo "<input type='hidden' name='booking_id' value='$bookingId'>";
    echo "<input type='hidden' name='payment_id' value='$paymentId'>";

    if (in_array(intval($personal['payment_method']), [1, 2])) {
        echo '<div class="mb-3"><label class="form-label">Card Number</label><input name="card_number" class="form-control" required></div>';
        echo '<div class="mb-3"><label class="form-label">Expiry Date</label><input name="card_expiry" class="form-control" required></div>';
        echo '<div class="mb-3"><label class="form-label">CVC</label><input name="card_cvc" class="form-control" required></div>';
    } else {
        echo '<div class="mb-3"><label class="form-label">Phone Number</label><input name="phone_number" class="form-control" required></div>';
    }

    echo '<div class="confirmation-btn"><button type="submit" class="btn btn-success">Confirm Payment</button></div>';
    echo '</form>';
}

if ($paymentSuccessful) {
    echo "<div class='payment-success'>Payment successful! Booking confirmed.</div>";
    echo "<div class='text-center mt-3'>";
    echo "<a href='print_ticket.php?booking_id=$bookingId' class='btn btn-info'>Print Ticket</a>";
    echo "</div>";
}

echo "</div>";
$conn->close();
?>
