<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SchoolMate - Login</title>
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
            gap: 8px; /* space between icon and text */
            margin-bottom: 25px;
        }

        .logo img {
            width: 24px;
            height: 24px;
        }

        .logo h2 {
            font-size: 28px;
            font-weight: 600;
            color: #000;
            margin: 0;
        }

        /* Login heading */
        .login-title {
            font-size: 45px;
            font-weight: 700;
            color: #4B34B4;
            margin-bottom: 8px;
        }

        /* Subtitle text */
        .subtitle {
            font-size: 16px;
            color: #333;
            font-weight: 400;
            margin-bottom: 25px;
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

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #4B34B4;
            outline: none;
            box-shadow: 0 0 5px rgba(75, 52, 180, 0.3);
        }

        /* Remember / Forgot */
        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            font-size: 13px;
            color: #555;
        }

        .options a {
            text-decoration: none;
            color: #4B34B4;
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
        }

        .btn:hover {
            background: #3a2794;
        }

        /* OR text */
        .or {
            margin: 25px 0;
            font-size: 16px;
            color: gray;
            text-align: center;
        }

        /* Social login */
        .social-login {
            text-align: center;
            font-size: 0;
        }

        .social-login a {
            display: inline-block;
            font-size: initial;
        }

        .social-login img {
            width: 30px;
            margin: 0 30px;
            cursor: pointer;
        }

        /* Signup text */
        .signup-text {
            font-size: 16px;
            margin-top: 30px;
            text-align: center;
            color: #555;
        }

        .signup-text a {
            color: #4B34B4;
            text-decoration: none;
        }  
            .message {
                padding: 15px;
                margin-bottom: 20px;
                border-radius: 5px;
                font-weight: bold;
                text-align: center;
            }
            .success { background-color: #d4edda; color: #155724; }
            .error { background-color: #f8d7da; color: #721c24; }
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

        <h1 class="login-title">Log In</h1>
        <p class="subtitle">Welcome back! Please enter your details</p>

        <!-- Display success message from registration -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="message success">
                <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']); 
                ?>
            </div>
        <?php endif; ?>

        <!-- Display login error message -->
        <?php if(isset($_SESSION['error'])): ?>
            <div class="message error">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']); 
                ?>
            </div>
        <?php endif; ?>

        <form action="login_process.php" method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter Your Email" required />

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter Password" required />

            <div class="options">
                <label><input type="checkbox" name="remember" /> Remember?</label>
                <a href="#">Forgot Password?</a>
            </div>

            <button type="submit" class="btn">Log In</button>
        </form>

        <div class="or">Or</div>

        <div class="social-login">
            <a href="login-google.php">
                <img src="images/google.png" alt="Login with Google" />
            </a>
            <a href="login-facebook.php">
                <img src="images/facebook.png" alt="Login with Facebook" />
            </a>
        </div>

        <p class="signup-text">
            Don’t have an account? <a href="register.php">Sign up</a>
        </p>
    </div>
</div>

</body>
</html>