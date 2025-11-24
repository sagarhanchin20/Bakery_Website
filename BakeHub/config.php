<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    //Database Credentials
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bakehub";

    //Create Connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // --- Check Connection ---
    if ($conn->connect_error) {
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
        exit;
    }    
?>