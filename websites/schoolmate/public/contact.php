<?php
session_start();
require 'database.php'; // adjust path if needed

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$school_id = $_GET['id'] ?? null;
$type = $_GET['type'] ?? null;

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $enquiry = $_POST['enquiry'];

    $stmt = $conn->prepare("INSERT INTO enquiries (user_id, school_id, enquiry_text, status) VALUES (?, ?, ?, 'Pending')");
    $stmt->bind_param("iis", $user_id, $school_id, $enquiry);
    $stmt->execute();
    $stmt->close();

    $message = "✅ Your enquiry has been submitted successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact School</title>
    <link rel="stylesheet" href="contact.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    background: #f4f6f8;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.contact-container {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    width: 400px;
    text-align: center;
}

h2 {
    margin-bottom: 20px;
    color: #333;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    text-align: left;
}

textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    resize: none;
    margin-bottom: 15px;
    font-size: 14px;
}

button {
    background: #007BFF;
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s;
}

button:hover {
    background: #0056b3;
}

.success {
    color: green;
    font-weight: bold;
    margin-bottom: 15px;
}

.back-btn {
    display: inline-block;
    margin-top: 15px;
    padding: 8px 14px;
    background: #6c757d;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    transition: background 0.3s;
}

.back-btn:hover {
    background: #495057;
}

        </style>
</head>
<body>
    <div class="contact-container">
        <h2>Contact School</h2>

        <?php if ($message): ?>
            <p class="success"><?php echo $message; ?></p>
            <a href="school_info.php?id=<?php echo $school_id; ?>&type=<?php echo $type; ?>" class="back-btn">⬅ Back to School Info</a>
        <?php else: ?>
            <form method="POST" class="contact-form">
                <label for="enquiry">Your Message:</label>
                <textarea name="enquiry" id="enquiry" rows="5" required></textarea>
                <button type="submit">Send Enquiry</button>
            </form>
            <a href="school_info.php?id=<?php echo $school_id; ?>&type=<?php echo $type; ?>" class="back-btn">⬅ Back to School Info</a>
        <?php endif; ?>
    </div>
</body>
</html>
