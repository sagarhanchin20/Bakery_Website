<?php
include 'config.php';

header('Content-Type: application/json');

// 1. Check if logged in
if (!isset($_SESSION['user_email'])) {
    echo json_encode(['logged_in' => false]);
    exit;
}

$email = $_SESSION['user_email'];

// 2. Get Name from 'customer' table
$sql = "SELECT name FROM customer WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $name = $row['name'];
} else {
    $name = "My Account";
}

// 3. Send data back to JS
echo json_encode(['logged_in' => true, 'name' => $name]);

$stmt->close();
$conn->close();
?>