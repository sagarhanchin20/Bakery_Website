<?php
include 'config.php';

//Check login
if (!isset($_SESSION['user_email'])) {
    header("Location: Login.html");
    exit;
}

$email = $_SESSION['user_email'];

//Get the logged-in user's ID from customers table
$sql_user = "SELECT id FROM customer WHERE email = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $email);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_row = $result_user->fetch_assoc();
$my_id = $user_row['id'];


//Fetch orders matching that customer_id
$sql = "SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $my_id);
$stmt->execute();
$result = $stmt->get_result();
?>