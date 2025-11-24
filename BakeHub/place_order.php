<?php
include 'config.php';

// 1. Force JSON response
header('Content-Type: application/json');

// 2. Check Login
if (!isset($_SESSION['user_email'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to place an order.']);
    exit;
}

$data = json_decode(file_get_contents("php://input"));
$email = $_SESSION['user_email'];
$items = $data->items;
$total = $data->total;

// --- THIS IS THE FIX (Line 20) ---
// Make sure the word "customer" is physically there between FROM and WHERE
$sql_user = "SELECT id FROM customer WHERE email = ?";

$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $email);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $row = $result_user->fetch_assoc();
    $customer_id = $row['id'];
} else {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

// 4. Insert Order
$sql_insert = "INSERT INTO orders (customer_id, items, total_amount) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql_insert);
$stmt->bind_param("isd", $customer_id, $items, $total);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Order placed successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>