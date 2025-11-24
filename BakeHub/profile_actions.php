<?php
include 'config.php';
header('Content-Type: application/json');

// 1. Get the 'action' from the URL (e.g., ?action=fetch)
// If no action is set, default to empty string
$action = isset($_GET['action']) ? $_GET['action'] : '';

// 2. Handle Logout (Doesn't require database check)
if ($action === 'logout') {
    session_unset();
    session_destroy();
    echo json_encode(['success' => true]);
    exit;
}

// 3. Check Login for all other actions
if (!isset($_SESSION['user_email'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$email = $_SESSION['user_email'];

// 4. Switch based on the action requested
switch ($action) {

    // --- CASE 1: FETCH DATA ---
    case 'fetch':
        $sql = "SELECT name, email, phone, gender, birthday FROM customer WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($data = $result->fetch_assoc()) {
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    // --- CASE 2: UPDATE DATA ---
    case 'update':
        // Read JSON body
        $data = json_decode(file_get_contents("php://input"));
        
        $sql = "UPDATE customer SET name=?, phone=?, gender=?, birthday=? WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $data->name, $data->phone, $data->gender, $data->birthday, $email);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }
        break;

    // --- CASE 3: DELETE ACCOUNT ---
    case 'delete':
        // Delete orders first (Foreign Key constraint)
        $sql_orders = "DELETE FROM orders WHERE customer_id = (SELECT id FROM customer WHERE email = ?)";
        $stmt1 = $conn->prepare($sql_orders);
        $stmt1->bind_param("s", $email);
        $stmt1->execute();

        // Delete user
        $sql_user = "DELETE FROM customer WHERE email = ?";
        $stmt2 = $conn->prepare($sql_user);
        $stmt2->bind_param("s", $email);

        if ($stmt2->execute()) {
            session_destroy();
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

$conn->close();
?>