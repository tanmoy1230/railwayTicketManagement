<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<?php
session_start(); // Start the session to access user data

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "railway ticket management");

$start = $_POST['start_station'];
$end = $_POST['end_station'];
$date = $_POST['journey_date'];
$seat_type = $_POST['seat_type']; 

// User data
$user_id = $_SESSION['id'];  // Logged-in user's ID

// You can also fetch more user details like user name, email, etc., if needed
// $user_name = $_SESSION['user_name']; 

$sql = "
SELECT t.train_code, t.train_name, t.start_time,
        cc.name AS carriage_type, tc.fare, sa.available_seat
FROM train t
JOIN train_carriage tc ON t.train_code = tc.train_code
JOIN carriage_class cc ON tc.carriage_id = cc.carriage_id
JOIN seat_availability sa ON tc.train_code = sa.train_code AND tc.carriage_id = sa.carriage_id
WHERE t.start_station_code = ? AND t.end_station_code = ? 
  AND sa.journey_date = ? AND cc.name = ?  
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL error: " . $conn->error);
}

$stmt->bind_param("ssss", $start, $end, $date, $seat_type); // Bind seat type parameter
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<div class='row g-4 mt-4'>";

    while ($row = $result->fetch_assoc()) {
        echo "
        <div class='col-md-6 col-lg-4'>
            <div class='card h-100 shadow-sm border-0'>
                <div class='card-body'>
                    <h5 class='card-title text-primary fw-bold'>{$row['train_name']}</h5>
                    <p class='mb-2'><strong>Departure:</strong> {$row['start_time']}</p>
                    <p class='mb-2'><strong>Class:</strong> {$row['carriage_type']}</p>
                    <p class='mb-2'><strong>Date:</strong> {$date}</p>
                    <p class='mb-2'><strong>Fare:</strong> ৳{$row['fare']}</p>
                    <p class='mb-3'><strong>Available Seats:</strong> {$row['available_seat']}</p>
                    <a href=\"book_now.php?train_code={$row['train_code']}&train_name=" . urlencode($row['train_name']) . "&date=" . urlencode($date) . "&fare=" . urlencode($row['fare']) . "&seat_type=" . urlencode($seat_type) . "\" class='btn btn-success w-100'>Book Now</a>
                </div>
            </div>
        </div>
        ";
    }

    echo "</div>"; // Close row
} else {
    echo "<div class='alert alert-warning mt-4'>No trains found for the selected route, date, and seat type.</div>";
}
?>
