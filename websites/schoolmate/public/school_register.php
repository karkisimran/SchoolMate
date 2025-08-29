<?php
session_start();
require 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['school_name'] = $_POST['school_name'];
    $_SESSION['school_type'] = $_POST['school_type'];
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['address'] = $_POST['address'];

    // Redirect to step 2
    header("Location: school_register2.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Your School</title>
    <link rel="stylesheet" href="school_register.css">
</head>
<body>
    <!-- Logo and Brand -->
   <div class="header">
        <img src="images/logo.jpg" alt="Logo" class="logo">
        <span class="brand">SchoolMate</span>
    </div>

    <!-- Form -->
    <div class="form-container">
        <h2>Register Your School</h2>
        <p class="subtitle">Please complete the form to create your school account</p>

        <form method="POST" action="">
            <label>School Name</label>
            <input type="text" name="school_name" placeholder="Enter Your School Name" required>

            <label for="school_type">School Type</label>
            <select name="school_type" id="school_type" required>
                <option value="" disabled selected>Select School Type</option>
                <option value="Montessori">Montessori</option>
                <option value="School">School</option>
            </select>


            <label>Email</label>
            <input type="email" name="email" placeholder="Enter Your Email" required>

            <label>Address</label>
            <input type="text" name="address" placeholder="Enter Your Address" required>

            <button type="submit" class="btn">Next</button>
        </form>
    </div>
</body>
</html>
