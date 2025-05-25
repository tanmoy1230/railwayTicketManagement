<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .form-control:read-only { background-color: #e9ecef; opacity: 1; }
    .form-control.selected-seats { font-weight: bold; color: #007bff; text-align: center; }
    .booking-form { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #dee2e6; border-radius: 0.3rem; background-color: #f8f9fa; }
</style>

<?php
session_start();

// Check if session data exists for booking
if (!isset($_SESSION['booking_data']) || !isset($_SESSION['selected_seats'])) {
    die("Booking data not found.");
}

$bookingData = $_SESSION['booking_data'];
$selectedSeats = $_SESSION['selected_seats'];

$trainCode = $bookingData['train_code'];
$trainName = $bookingData['train_name'];
$journeyDate = $bookingData['journey_date'];
$fare = $bookingData['fare'];
$seatType = $bookingData['seat_type'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['passenger_name'], $_POST['age'], $_POST['gender'], $_POST['payment_method'])) {
        $passengerName = $_POST['passenger_name'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $paymentMethod = $_POST['payment_method'];
        $paymentNumber = $_POST['payment_number'] ?? null;

        // Save form data to session
        $_SESSION['personal_details'] = [
            'passenger_name' => $passengerName,
            'age' => $age,
            'gender' => $gender,
            'payment_method' => $paymentMethod,
            'payment_number' => $paymentNumber,
            'selected_seats' => $selectedSeats
        ];

        // Redirect to booking confirmation page
        header("Location: booking_summary.php");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Please fill in all the required fields.</div>";
    }
}

echo '<div class="container mt-5">';
echo "<h3 class='text-center mb-4'>Enter Your Details</h3>";
?>

<form method="POST" class="booking-form">
    <div class="row mb-3">
        <label for="passenger_name" class="col-sm-3 col-form-label">Passenger Name</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="passenger_name" name="passenger_name" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="age" class="col-sm-3 col-form-label">Age</label>
        <div class="col-sm-9">
            <input type="number" class="form-control" id="age" name="age" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="gender" class="col-sm-3 col-form-label">Gender</label>
        <div class="col-sm-9">
            <select class="form-select" id="gender" name="gender" required>
                <option value="">-- Select Gender --</option>
                <option value="1">Male</option>
                <option value="2">Female</option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="payment_method" class="col-sm-3 col-form-label">Payment Method</label>
        <div class="col-sm-9">
            <select class="form-select" id="payment_method" name="payment_method" onchange="togglePaymentInput()" required>
                <option value="">-- Select Method --</option>
                <option value="1">Credit Card</option>
                <option value="2">Debit Card</option>
                <option value="3">bKash</option>
                <option value="4">Nagad</option>
            </select>
        </div>
    </div>

    <div class="row mb-3" id="payment_number_row" style="display: none;">
        <label for="payment_number" class="col-sm-3 col-form-label" id="payment_label">Card/Phone Number</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="payment_number" name="payment_number" placeholder="">
        </div>
    </div>

    <div class="row mb-3">
        <label for="selected_seats" class="col-sm-3 col-form-label">Selected Seats</label>
        <div class="col-sm-9">
            <input type="text" class="form-control selected-seats" id="selected_seats" name="selected_seats" value="<?= htmlspecialchars(implode(', ', $selectedSeats)) ?>" readonly>
            <small class="form-text text-muted">These are the seats you selected.</small>
        </div>
    </div>

    <div class="text-center">
        <button type="submit" class="btn btn-primary">Confirm Booking and payment</button>
    </div>
</form>
</div>

<script>
    function togglePaymentInput() {
        const method = document.getElementById('payment_method').value;
        const paymentRow = document.getElementById('payment_number_row');
        const label = document.getElementById('payment_label');
        const input = document.getElementById('payment_number');

        if (method === '1' || method === '2') {
            label.textContent = 'Card Number';
            input.placeholder = 'Enter card number';
            paymentRow.style.display = 'flex';
        } else if (method === '3' || method === '4') {
            label.textContent = 'Phone Number';
            input.placeholder = 'Enter bKash/Nagad number';
            paymentRow.style.display = 'flex';
        } else {
            paymentRow.style.display = 'none';
            input.value = '';
        }
    }
</script>
