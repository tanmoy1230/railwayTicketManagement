<?php
$conn = new mysqli("localhost", "root", "", "railway ticket management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT DISTINCT name FROM carriage_class ORDER BY name";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<option value='' disabled selected>Select seat type</option>";
    while ($row = $result->fetch_assoc()) {
        // Set value as 'name'
        echo "<option value='{$row['name']}'>{$row['name']}</option>";
    }
} else {
    echo "<option>No seat types found</option>";
}

$conn->close();
?>