<?php
session_start();
include 'db_connect.php';


$username = $_POST['username'];
$password = $_POST['password'];

// Prepare and execute the SQL query
$query = $conn->prepare("SELECT * FROM users WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

if ($user && $password === $user['password']) {
    $_SESSION['user_id'] = $user['id']; // Set session for logged-in user
    $_SESSION['username'] = $user['username'];
    $_SESSION['customer_logged_in'] = true;

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password. Try again']);
}