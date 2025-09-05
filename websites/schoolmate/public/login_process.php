<?php
session_start();
include 'database.php';

$email = trim($_POST['email']);
$password = trim($_POST['password']);

// --- First check in admin table ---
$sql = "SELECT id, name, password FROM admin WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();

    if (password_verify($password, $admin['password'])) {
        // Login success for admin
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'admin';
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid password!";
        header("Location: login.php");
        exit();
    }
}

// --- If not found in admin, check users table ---
$sql = "SELECT id, password FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        // Login success for users
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'user';
        header("Location: user_dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid password!";
        header("Location: login.php");
        exit();
    }
}

// --- If not found in users, check registered_schools ---
$sql = "SELECT id, school_name, password FROM registered_schools WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $school = $result->fetch_assoc();

    if (password_verify($password, $school['password'])) {
        // Login success for schools
        $_SESSION['school_id'] = $school['id'];
        $_SESSION['school_name'] = $school['school_name'];
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'school';
        header("Location: school_dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid password!";
        header("Location: login.php");
        exit();
    }
}

// If no account found
$_SESSION['error'] = "No account found with that email!";
header("Location: login.php");
exit();
?>