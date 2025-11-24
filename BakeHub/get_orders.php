<?php
include 'config.php';

// --- 1. Add JSON Header (Required because config.php doesn't have it anymore) ---
header('Content-Type: application/json');

// --- 2. Check Login ---
if (!isset($_SESSION['user_email'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$email = $_SESSION['user_email'];

// --- 3. Get Customer ID ---
// IMPORTANT: Using table 'customer' (singular)
$sql_user = "SELECT id FROM customer WHERE email = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $email);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $user_row = $result_user->fetch_assoc();
    $my_id = $user_row['id'];
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

// --- 4. Get Orders ---
$sql = "SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $my_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

// --- 5. Send Data ---
echo json_encode(['success' => true, 'orders' => $orders]);

$stmt->close();
$conn->close();
?>