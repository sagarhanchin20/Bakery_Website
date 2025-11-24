<?php
// Register.php

// 1. Include your database connection
include 'config.php'; 

header('Content-Type: application/json');

// 2. Get the raw JSON data from the request body
// We use this because the data is sent as JSON, not as form-data
$data = json_decode(file_get_contents("php://input"));

// 3. Prepare the response array
$response = ['success' => false, 'message' => ''];

// 4. Validate input
if (empty($data->name) || empty($data->email) || empty($data->password)) {
    $response['message'] = 'Please fill in all fields.';
    echo json_encode($response);
    exit;
}

// 5. Assign variables
$name = $data->name;
$email = $data->email;
$password = $data->password; // Storing as plaintext as you requested

// --- Check if email already exists ---
$sql_check = "SELECT id FROM customer WHERE email = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // Email already exists
    $response['message'] = 'An account with this email already exists.';
} else {
    // --- Email is new, proceed with registration ---
    
    // 6. Create the SQL query using prepared statements to prevent SQL injection
    $sql_insert = "INSERT INTO customer (name, email, password) VALUES (?, ?, ?)";
    
    // 7. Prepare and bind the statement
    $stmt = $conn->prepare($sql_insert);
    // 'sss' means we are binding three string parameters
    $stmt->bind_param("sss", $name, $email, $password);

    // 8. Execute the statement and send response
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Account created successfully! Redirecting to login...';
    } else {
        $response['message'] = 'Error: ' . $stmt->error;
    }
    
    // 9. Close the statement
    $stmt->close();
}

// Close the check statement and connection
$stmt_check->close();
$conn->close();

// 10. Send the JSON response back to JavaScript
echo json_encode($response);

?>