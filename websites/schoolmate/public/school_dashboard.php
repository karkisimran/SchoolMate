<?php
session_start();
require 'database.php';

$schoolName = "SchoolMate"; // default
$location = ""; // Using 'address' from your table as location
$email = "";
$schoolType = "";
$logoPath = ""; // New variable for logo path
$contactNumber = ""; // New variable for contact number
$aboutUs = ""; // New variable for about us
$profileCompletion = 0; // Default profile completion percentage
$totalViews = 0; // Default total views
$totalMessages = 0; // Default total messages

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
            $location = $row['address'];
            $logoPath = $row['logo_path'] ?? '';
            
            // Calculate profile completion (simple example)
            $profileCompletion = (!empty($schoolType) ? 20 : 0) + (!empty($location) ? 20 : 0) + (!empty($email) ? 20 : 0) + (!empty($contactNumber) ? 20 : 0) + (!empty($aboutUs) ? 20 : 0);
            $totalViews = rand(100, 1000); // Placeholder for views
            $totalMessages = rand(10, 50); // Placeholder for messages
        }
    }
}

// Handle profile updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $newSchoolType = $_POST['school_type'] ?? '';
    $newLocation = $_POST['location'] ?? '';
    $newEmail = $_POST['email'] ?? '';
    $newContactNumber = $_POST['contact_number'] ?? '';
    $newAboutUs = $_POST['about_us'] ?? '';

    $sql = "UPDATE registered_schools SET school_type = ?, address = ?, email = ?, contact_number = ?, about_us = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sssssi", $newSchoolType, $newLocation, $newEmail, $newContactNumber, $newAboutUs, $schoolId);
        $stmt->execute();
        // Refresh the page to reflect updates
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Handle image upload or update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_image"])) {
    $target_dir = "uploads/"; // Ensure this directory exists and is writable
    $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $allowed_types = array("jpg", "jpeg", "png", "gif");
    if (in_array($imageFileType, $allowed_types)) {
        $uploadOk = true;
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $sql = "UPDATE registered_schools SET logo_path = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("si", $target_file, $schoolId);
                $stmt->execute();
                $logoPath = $target_file;
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
            <li class="active"><a href="#dashboard"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li><a href="#profile"><i class="fa-solid fa-user"></i> Profile Management</a></li>
            <li><a href="#academic"><i class="fa-solid fa-book"></i> Academic Details</a></li>
            <li><a href="#facilities"><i class="fa-solid fa-cogs"></i> Facilities & Services</a></li>
            <li><a href="#events"><i class="fa-solid fa-calendar"></i> Events & Announcements</a></li>
            <li><a href="#messages"><i class="fa-solid fa-envelope"></i> Messages & Inquiries</a></li>
            <li><a href="#settings"><i class="fa-solid fa-gear"></i> Account Settings</a></li>
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
        <div class="info-section section" id="dashboard">
            <h1>Basic information</h1>
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
                    <td><input type="text" value="<?php echo htmlspecialchars($contactNumber); ?>"></td>
                </tr>
                <tr>
                    <td>About Us</td>
                    <td>:</td>
                    <td><textarea readonly><?php echo htmlspecialchars($aboutUs); ?></textarea></td>
                </tr>
            </table>

            <!-- Dashboard Section -->
            <h1>Dashboard</h1>
            <div class="stats">
                <div class="profile-completion">
                    <p>Profile Completion</p>
                    <div class="progress-bar">
                        <div class="progress" style="width: <?php echo $profileCompletion; ?>%;"></div>
                    </div>
                    <span><?php echo $profileCompletion; ?>%</span>
                </div>
                <div class="views-messages">
                    <p>Total Views<br><strong><?php echo $totalViews; ?></strong></p>
                    <p>Messages<br><strong><?php echo $totalMessages; ?></strong></p>
                </div>
                <div class="toggle-feature">
                    <p>Featured Listing</p>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

            <!-- Profile Management Section (standalone) -->
        <div class="section" id="profile-section">
            <h1>Profile Management</h1>
            <div class="profile-management">
                <div class="box"><p>Edit Basic Info</p><button onclick="showEditForm('basic-info')">Edit</button></div>
                <div class="box"><p>Edit Contact Info</p><button onclick="showEditForm('contact-info')">Edit</button></div>
                <div class="box"><p>Edit About Us</p><button onclick="showEditForm('about-us')">Edit</button></div>
                <div class="box"><p>Upload Logo & Cover Photo</p><button>Upload</button></div>
                <div class="box"><p>Edit Photo Gallery</p><button>Edit</button></div>
                <div class="box"><p>Edit Video Links</p><button>Edit</button></div>
            </div>
            <div id="basic-info-edit" class="edit-form">
                <form method="post">
                    <input type="text" name="school_type" value="<?php echo htmlspecialchars($schoolType); ?>" placeholder="School Type">
                    <input type="text" name="location" value="<?php echo htmlspecialchars($location); ?>" placeholder="Location">
                    <button type="submit" name="update_profile">Save</button>
                </form>
            </div>
            <div id="contact-info-edit" class="edit-form">
                <form method="post">
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Email">
                    <input type="text" name="contact_number" value="<?php echo htmlspecialchars($contactNumber); ?>" placeholder="Contact Number">
                    <button type="submit" name="update_profile">Save</button>
                </form>
            </div>
            <div id="about-us-edit" class="edit-form">
                <form method="post">
                    <textarea name="about_us" placeholder="About Us"><?php echo htmlspecialchars($aboutUs); ?></textarea>
                    <button type="submit" name="update_profile">Save</button>
                </form>
            </div>
        </div>

            <!-- Edit Forms -->
            <div id="basic-info-edit" class="edit-form">
                <form method="post">
                    <input type="text" name="school_type" value="<?php echo htmlspecialchars($schoolType); ?>" placeholder="School Type">
                    <input type="text" name="location" value="<?php echo htmlspecialchars($location); ?>" placeholder="Location">
                    <button type="submit" name="update_profile">Save</button>
                </form>
            </div>
            <div id="contact-info-edit" class="edit-form">
                <form method="post">
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Email">
                    <input type="text" name="contact_number" value="<?php echo htmlspecialchars($contactNumber); ?>" placeholder="Contact Number">
                    <button type="submit" name="update_profile">Save</button>
                </form>
            </div>
            <div id="about-us-edit" class="edit-form">
                <form method="post">
                    <textarea name="about_us" placeholder="About Us"><?php echo htmlspecialchars($aboutUs); ?></textarea>
                    <button type="submit" name="update_profile">Save</button>
                </form>
            </div>
        </div>

    

        <!-- Academic Details Section -->
        <div class="section" id="academic-section">
            <h1>Academic Details</h1>
            <div class="card-grid">
                <div class="card">
                    Programs/Classes
                    <span class="add">+</span>
                </div>
                <div class="card">
                    Subjects Offered
                    <span class="add">+</span>
                </div>
                <div class="card">
                    Admission Requirements
                    <span class="add">+</span>
                </div>
            </div>
            <div class="card-wide">
                <a href="#" class="link">Tuition &amp; Fees</a>
                <span class="add" style="float:right;">+</span>
            </div>
        </div>

        <!-- Facilities & Services Section -->
        <div class="section" id="facilities-section">
            <section class="facilities">
                <h1>Facilities & Services</h1>
                <div class="facility-box">
                    <p><b>Available facilities :</b></p>
                    <ul>
                        <li><input type="checkbox" checked> Playground</li>
                        <li><input type="checkbox" checked> Library</li>
                        <li><input type="checkbox" checked> Transportation</li>
                        <li><input type="checkbox" checked> Sports</li>
                    </ul>
                    <span class="add">+</span>
                </div>
            </section>
        </div>

        <!-- Events & Announcements Section -->
        <div class="section" id="events-section">
            <h1>Events & Announcements</h1>
            <section class="events">
                <div class="event-box">
                    <div class="event-header">
                        <span>Upcoming Events</span>
                        <input type="text" placeholder="Search For Events">
                        <button>Search</button>
                    </div>
                    <div class="event-grid">
                        <div class="event-item"><img src="images/events.jpg" alt="Upcoming Event"></div>
                        <div class="event-item"><img src="images/events.jpg" alt="Upcoming Event"></div>
                        <div class="event-item"><img src="images/events.jpg" alt="Upcoming Event"></div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Messages & Inquiries Section -->
        <div class="section" id="messages-section">
            <h1>Messages & Inquiries</h1>
            <div class="messages-inquiries-grid">
                <div class="message-box">
                    <i class="fa-solid fa-comment-dots"></i>
                    <span>Messages</span>
                    <button class="view-btn">View Messages</button>
                </div>
                <div class="inquiries-box">
                    <i class="fa-solid fa-comment-dots"></i>
                    <span>Inquiries</span>
                    <button class="view-btn">View Inquiries</button>
                </div>
            </div>
        </div>

        <!-- Account Settings Section -->
        <div class="section" id="settings-section">
            <h1>Account Settings</h1>
            <div class="settings-box">
                <i class="fa-solid fa-sign-out-alt"></i>
                <span>Logout</span>
                <button class="confirm-btn" onclick="showLogoutPopup()">Confirm</button>
            </div>

            <!-- Logout Confirmation Popup -->
            <div id="logout-popup" class="popup">
                <div class="popup-content">
                    <p>Do you really want to log out?</p>
                    <div class="popup-buttons">
                        <button class="popup-btn yes-btn" onclick="logout()">Yes</button>
                        <button class="popup-btn no-btn" onclick="hideLogoutPopup()">No</button>
                    </div>
                </div>
            </div>
        </div>

<script>
    function showEditForm(formId) {
        document.querySelectorAll('.edit-form').forEach(form => form.style.display = 'none');
        document.getElementById(formId + '-edit').style.display = 'block';
    }

    document.querySelectorAll('.sidebar a').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId + '-section');
            if (targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth' });
                document.querySelectorAll('.sidebar li').forEach(li => li.classList.remove('active'));
                this.parentElement.classList.add('active');
                if (targetId === 'dashboard') {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    const sectionOffset = targetElement.getBoundingClientRect().top + window.scrollY - 80;
                    window.scrollTo({ top: sectionOffset, behavior: 'smooth' });
                }
            }
        });
    });

    window.addEventListener('load', () => {
        document.getElementById('dashboard').scrollIntoView({ behavior: 'smooth' });
    });

    // Logout Popup Functions
function showLogoutPopup() {
    document.getElementById('logout-popup').style.display = 'flex';
}

function hideLogoutPopup() {
    document.getElementById('logout-popup').style.display = 'none';
}

function logout() {
    // Simulate logout by redirecting to index.php
    window.location.href = 'index.php';
}
</script>


</body>
</html>