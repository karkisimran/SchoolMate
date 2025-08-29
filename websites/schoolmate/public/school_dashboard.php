<?php
session_start();
require 'database.php';

$schoolName = "SchoolMate"; // default
$location = ""; // Using 'address' from your table as location
$email = "";
$schoolType = "";
$logoPath = ""; // New variable for logo path

if (isset($_SESSION['school_id'])) {
    $schoolId = $_SESSION['school_id'];

    $sql = "SELECT school_name, school_type, email, address, logo_path FROM registered_schools WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $schoolId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $schoolName = $row['school_name'];
            $schoolType = $row['school_type'];
            $email = $row['email'];
            $location = $row['address']; // Map 'address' to 'location'
            $logoPath = $row['logo_path'] ?? ''; // Use empty string if NULL
        }
    }
}

// Handle image upload or update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_image"])) {
    $target_dir = "uploads/"; // Ensure this directory exists and is writable
    $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a valid type
    $allowed_types = array("jpg", "jpeg", "png", "gif");
    if (in_array($imageFileType, $allowed_types)) {
        $uploadOk = true;
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $sql = "UPDATE registered_schools SET logo_path = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("si", $target_file, $schoolId);
                $stmt->execute();
                $logoPath = $target_file; // Update logoPath after successful upload
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SchoolMate Dashboard</title>
    <link rel="stylesheet" href="school_dashboard.css">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- Top bar -->
<div class="top-bar">
    <div class="contact-info">
        <span>+977 9802585485</span>
        <span><i class="fa-solid fa-envelope"></i> schoolmate1@gmail.com</span>
    </div>
    <div class="logo">
        <i class="fa-solid fa-graduation-cap"></i> SchoolMate
    </div>
</div>

<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2><?php echo $schoolName; ?></h2>
        <ul>
            <li class="active"><i class="fa-solid fa-house"></i> Dashboard</li>
            <li><i class="fa-solid fa-user"></i> Profile Management</li>
            <li><i class="fa-solid fa-book"></i> Academic Details</li>
            <li><i class="fa-solid fa-cogs"></i> Facilities & Services</li>
            <li><i class="fa-solid fa-calendar"></i> Events & Announcements</li>
            <li><i class="fa-solid fa-envelope"></i> Messages & Inquiries</li>
            <li><i class="fa-solid fa-gear"></i> Account Settings</li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Welcome, <?php echo $schoolName; ?></h1>
        <div class="centered-upload">
            <div class="school-logo">
                <?php if (!empty($logoPath) && file_exists($logoPath)): ?>
                    <div class="profile-image-container">
                        <img src="<?php echo htmlspecialchars($logoPath); ?>" alt="School Logo" style="width: 200px; height: 200px; object-fit: cover; border-radius: 5px;">
                        <form method="post" enctype="multipart/form-data" class="change-profile-form">
                            <input type="file" name="profile_image" accept="image/*">
                            <button type="submit">Change Profile Picture</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div style="width: 150px; height: 150px; background-color: #ccc; position: relative; display: flex; align-items: center; justify-content: center;">
                        <form method="post" enctype="multipart/form-data">
                            <input type="file" name="profile_image" accept="image/*" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0; width: 150px; height: 150px; cursor: pointer;">
                            <span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #666; cursor: pointer;">Upload Image</span>
                            <button type="submit" style="position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); padding: 5px 10px; cursor: pointer;">Submit</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <h2>Basic information</h2>
        <div class="info-section">
            <h3><?php echo $schoolName; ?></h3>
            <table>
                <tr>
                    <td>School Type</td>
                    <td>:</td>
                    <td><input type="text" value="<?php echo htmlspecialchars($schoolType); ?>" readonly></td>
                </tr>
                <tr>
                    <td>Location</td>
                    <td>:</td>
                    <td><input type="text" value="<?php echo htmlspecialchars($location); ?>" readonly></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>:</td>
                    <td><input type="email" value="<?php echo htmlspecialchars($email); ?>" readonly></td>
                </tr>
                <tr>
                    <td>Contact Number</td>
                    <td>:</td>
                    <td><input type="text" value="" placeholder="Enter contact number"></td>
                </tr>
                <tr>
                    <td>About Us</td>
                    <td>:</td>
                    <td><textarea placeholder="Enter about us"></textarea></td>
                </tr>
            </table>
        </div>
    </div>
</div>

</body>
</html>