<?php
include 'config.php'; 

// IMPORTANT: This line is required now!
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"));
$response = ['success' => false, 'message' => ''];

if (empty($data->email) || empty($data->password)) {
    $response['message'] = 'Please enter both email and password.';
    echo json_encode($response);
    exit;
}

$email = $data->email;
$password_from_form = $data->password;

// FIXED: Using singular 'customer' table
$sql = "SELECT password FROM customer WHERE email = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $password_from_db = $user['password'];

    // Compare passwords (Plaintext as requested)
    if ($password_from_form === $password_from_db) {
        
        // Session is already started in config.php, so we just set variables
        $_SESSION['user_email'] = $email;
        $_SESSION['logged_in'] = true;

        $response['success'] = true;
        $response['message'] = 'Login successful!';
    } else {
        $response['message'] = 'Invalid email or password.';
    }
} else {
    $response['message'] = 'Invalid email or password.';
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>