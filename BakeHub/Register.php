<?php
include 'config.php'; 

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"));

$response = ['success' => false, 'message' => ''];

//Validate input
if (empty($data->name) || empty($data->email) || empty($data->password)) {
    $response['message'] = 'Please fill in all fields.';
    echo json_encode($response);
    exit;
}

//Assign variables
$name = $data->name;
$email = $data->email;
$password = $data->password;

//Check if email already exists
$sql_check = "SELECT id FROM customer WHERE email = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    //If Email exists
    $response['message'] = 'An account with this email already exists.';
} else {
    $sql_insert = "INSERT INTO customer (name, email, password) VALUES (?, ?, ?)";
    
    $stmt = $conn->prepare($sql_insert);

    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Account created successfully! Redirecting to login...';
    } else {
        $response['message'] = 'Error: ' . $stmt->error;
    }
    
    $stmt->close();
}

$stmt_check->close();
$conn->close();

echo json_encode($response);

?>