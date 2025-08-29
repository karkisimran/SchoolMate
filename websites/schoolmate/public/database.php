<?php
$host = "mysql";        // Use the service name from docker-compose
$user = "student";      // The non-root DB user you defined
$pass = "student";      // This is MYSQL_PASSWORD (for 'student')
$dbname = "schoolmate"; // The database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Debug message for successful connection
// echo "Connected successfully";
?>
