<?php
session_start();
require 'database.php'; // your mysqli connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm) {
        $_SESSION['error'] = "Passwords do not match!";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert into DB
        $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $hashedPassword);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Successfully registered!";
            header("refresh:2; url=login.php");
            exit;
        } else {
            $_SESSION['error'] = "Error: " . $stmt->error;
        }
    }
}
?>


<!-- HTML & CSS -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SchoolMate - Register</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        .container {
            display: flex;
            height: 100vh;
            width: 80vw;
            max-width: 1300px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        /* Left side */
        .left-image {
            width: 45%;
            height: 100vh;
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .left-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        /* Right side */
        .login-box {
            width: 40%;
            padding: 150px 50px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        /* Logo row with image and text */
        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 25px;
        }

        .logo img {
            width: 25px;
            height: 25px;
        }

        .logo h2 {
            font-size: 28px;
            font-weight: 700;
            color: #000;
            margin: 0;
        }

        /* Register heading */
        .login-title {
            font-size: 45px;
            font-weight: 700;
            color: #4B34B4;
            margin-bottom: 20px;
        }

        /* Form */
        label {
            display: block;
            margin-top: 20px;
            font-weight: 500;
            color: #222;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
            color: #333;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: #4B34B4;
            outline: none;
            box-shadow: 0 0 5px rgba(75, 52, 180, 0.3);
        }

        /* Button */
        .btn {
            width: 100%;
            background: #4B34B4;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            margin-top: 25px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #3a2998;
        }

        /* Or text in light grey */
        .or {
            text-align: center;
            margin: 20px 0;
            font-weight: 600;
            color: #aaa; 
            font-size: 18px;
        }

        /* Social login container: flexbox, centered horizontally */
        .social-login {
            display: flex;
            justify-content: center;
            gap: 40px; 
            margin-bottom: 20px;
        }


        .social-icon img {
            width: 32px;  
            height: 32px; 
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .social-icon img:hover {
            transform: scale(1.1);
        }

        /* Signup text */
        .signup-text {
            font-size: 14px;
            color: #555;
            text-align: center;
        }

        .signup-text a {
            color: #4B34B4;
            text-decoration: none;
            font-weight: 600;
        }

        .signup-text a:hover {
            text-decoration: underline;
        }
    </style>

</head>

<body>

<div class="container">
    <div class="left-image">
        <img src="images/school.jpg" alt="School Building" />
    </div>

    <div class="login-box">
        <div class="logo">
            <img src="images/logo.jpg" alt="SchoolMate Logo" />
            <h2>SchoolMate</h2>
        </div>

        <h1 class="login-title">Register</h1>

        <?php if(isset($_SESSION['error'])): ?>
            <div style="color:red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['success'])): ?>
            <div style="color:green;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter Your Email" required />

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter Password" required />

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required />

            <button type="submit" class="btn">Register</button>
        </form>

        <div class="or">Or</div>

        <div class="social-login">
            <a href="register-google.php" class="social-icon">
                <img src="images/google.png" alt="Register with Google" />
            </a>
            <a href="register-facebook.php" class="social-icon">
                <img src="images/facebook.png" alt="Register with Facebook" />
            </a>
        </div>

        <p class="signup-text">
            Already have an account? <a href="login.php">Log in</a>
        </p>
    </div>
</div>

</body>
</html>