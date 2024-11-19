<?php
session_start();
header('Content-Type: application/json');

include 'db_connect.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['success'] = false;
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

if (!isset($_POST['username']) || !isset($_POST['password'])) {
    $response['success'] = false;
    $response['message'] = 'Username and password are required';
    echo json_encode($response);
    exit;
}

$username = trim($_POST['username']);
$password = trim($_POST['password']);

try {
    // Check if username already exists
    $check_query = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response['success'] = false;
        $response['message'] = 'Username already exists';
        echo json_encode($response);
        exit;
    }

    $user_type = 'customer';

    // Insert new user
    $insert_query = "INSERT INTO users (username, password, user_type) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("sss", $username, $password, $user_type);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Account created successfully';
    } else {
        $response['success'] = false;
        $response['message'] = 'Failed to create account';
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'An error occurred. Please try again later.';
}

echo json_encode($response);
?>