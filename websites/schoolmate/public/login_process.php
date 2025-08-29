<?php
session_start();
include 'database.php';

$email = trim($_POST['email']);
$password = trim($_POST['password']);

// Fetch user
$sql = "SELECT id, password FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $email;
        header("Location: dashboard.php"); // Redirect to dashboard after login
        exit();
    } else {
        $_SESSION['error'] = "Invalid password!";
    }
} else {
    $_SESSION['error'] = "No account found with that email!";
}

header("Location: login.php");
exit();
?>