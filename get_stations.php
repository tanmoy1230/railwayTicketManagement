//it is sending information about train station in book_ticket.php

<?php
$conn = new mysqli("localhost", "root", "", "railway ticket management");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT station_code, station_name FROM train_station ORDER BY station_name";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<option value='' disabled selected>Select station</option>";
    while ($row = $result->fetch_assoc()) {
        echo "<option value='{$row['station_code']}'>{$row['station_name']} ({$row['station_code']})</option>";
    }
} else {
    echo "<option>No stations found</option>";
}

$conn->close();
