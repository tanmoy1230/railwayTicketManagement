<?php
session_start();
$conn = new mysqli("localhost", "root", "", "railway ticket management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['id'];

$trainCode = isset($_GET['train_code']) ? mysqli_real_escape_string($conn, $_GET['train_code']) : '';
$trainName = isset($_GET['train_name']) ? mysqli_real_escape_string($conn, $_GET['train_name']) : '';
$journeyDate = isset($_GET['date']) ? mysqli_real_escape_string($conn, $_GET['date']) : '';
$fare = isset($_GET['fare']) ? mysqli_real_escape_string($conn, $_GET['fare']) : '';
$seatType = isset($_GET['seat_type']) ? mysqli_real_escape_string($conn, $_GET['seat_type']) : '';
$carriageId = 1;

$reservedSeats = [];
$stmt = $conn->prepare("SELECT seat_number FROM seat_reservations WHERE train_code = ? AND carriage_id = ? AND journey_date = ?");
$stmt->bind_param("sis", $trainCode, $carriageId, $journeyDate);
$stmt->execute();
$res = $stmt->get_result();
$reservedSeats = array_column($res->fetch_all(MYSQLI_ASSOC), 'seat_number');
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['selected_seats'])) {
        echo "<div class='alert alert-warning'>Please select at least one seat.</div>";
    } else {
        $selectedSeats = $_POST['selected_seats'];
        $totalFare = $fare * count($selectedSeats); // calculate total fare

        // Store in session
        $_SESSION['booking_data'] = [
            'train_code' => $trainCode,
            'train_name' => $trainName,
            'journey_date' => $journeyDate,
            'fare' => $totalFare,
            'seat_type' => $seatType,
            'carriage_id' => $carriageId
        ];
        $_SESSION['selected_seats'] = $selectedSeats;

        header("Location: personal_details.php");
        exit();
    }
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Seats - <?= htmlspecialchars($trainName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .seat { width: 50px; height: 50px; text-align: center; line-height: 50px; border-radius: 5px; margin: 4px; cursor: pointer; }
        .available { background-color: #28a745; color: white; }
        .selected { background-color: #ffc107; color: black; }
        .reserved { background-color: #dc3545; color: white; cursor: not-allowed; }
        input[type="checkbox"] { display: none; }
        .seat-grid { display: flex; flex-wrap: wrap; max-width: 300px; justify-content: center; }
        .seat-wrapper { display: flex; justify-content: center; align-items: center; min-height: 300px; }
    </style>
</head>
<body class="container py-4">
    <h3 class="text-center mb-4">Select Your Seat(s)</h3>
    <p class="text-center"><strong>Train:</strong> <?= htmlspecialchars($trainName) ?> |
       <strong>Date:</strong> <?= htmlspecialchars($journeyDate) ?> |
       <strong>Class:</strong> <?= htmlspecialchars($seatType) ?> |
       <strong>Fare (per seat):</strong> ৳<?= htmlspecialchars($fare) ?>
    </p>

    <form method="post" action="">
        <div class="seat-wrapper">
            <div class="seat-grid">
                <?php
                $rows = ['A','B','C','D','E','F','G','H'];
                $cols = range(1, 5);
                foreach ($rows as $r) {
                    foreach ($cols as $c) {
                        $seat = $r.$c;
                        $isReserved = in_array($seat, $reservedSeats);
                        $statusClass = $isReserved ? 'reserved' : 'available';
                        $disabled = $isReserved ? 'disabled' : '';
                        echo "
                        <label class='seat $statusClass'>
                            <input type='checkbox' name='selected_seats[]' value='$seat' $disabled>
                            $seat
                        </label>";
                    }
                }
                ?>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary mt-4 px-4">Confirm Selection</button>
        </div>
    </form>

    <script>
        document.querySelectorAll('.seat input[type="checkbox"]').forEach(chk => {
            chk.addEventListener('change', function () {
                this.parentElement.classList.toggle('selected', this.checked);
            });
        });
    </script>
</body>
</html>
