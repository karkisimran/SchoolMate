<?php
session_start();
require 'database.php';

// Initialize
$schoolData = null;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$type = isset($_GET['type']) ? $_GET['type'] : 'school'; // default to 'school'

if ($id > 0) {

    if ($type === 'montessori') {
        // Fetch from montessori table
        $stmt = $conn->prepare("
            SELECT id AS school_id, name AS school_name, 'Montessori' AS school_type, '' AS email, 
                   location AS address, '' AS contact_number, '' AS about_us, '{}' AS academic_details, 
                   '{}' AS facilities, '' AS announcements, image AS logo_path
            FROM montessori 
            WHERE id = ?
        ");
    } else {
        // Fetch from school_details table
        $stmt = $conn->prepare("
            SELECT school_id, school_name, school_type, email, address, contact_number, about_us, 
                   academic_details, facilities, announcements, logo_path
            FROM school_details 
            WHERE school_id = ?
        ");
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $academicDetails = json_decode($row['academic_details'] ?? '{}', true);
        $facilities = json_decode($row['facilities'] ?? '{}', true);

        $schoolData = [
            'name' => $row['school_name'] ?? $row['name'] ?? '',
            'type' => $row['school_type'] ?? ($type === 'montessori' ? 'Montessori' : ''),
            'email' => $row['email'] ?? 'Not available',
            'address' => $row['address'] ?? '',
            'contact' => $row['contact_number'] ?? 'Not available',
            'about' => $row['about_us'] ?? 'Not available',
            'academic' => $academicDetails ?: [
                'programs' => 'Not available',
                'subjects' => 'Not available',
                'requirements' => 'Not available'
            ],
            'facilities' => $facilities ?: [
                'library' => 'Not available',
                'sports' => 'Not available',
                'labs' => 'Not available',
                'transport' => 'Not available',
                'eca' => 'Not available',
                'health' => 'Not available',
                'scholarships' => 'Not available',
                'cafeteria' => 'Not available'
            ],
            'announcements' => $row['announcements'] ?? 'No announcements available',
            'logo' => $row['logo_path'] ?? $row['image'] ?? 'images/placeholder.jpg'
        ];
    } else {
        $schoolData = null;
    }

} else {
    $schoolData = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $schoolData ? htmlspecialchars($schoolData['name']) . ' - SchoolMate' : 'School Info - SchoolMate'; ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="style.css"/>
<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f9f9f9;
}
.header {
    background-color: #fff;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
.logo { font-size: 22px; font-weight: bold; color: #000; }
.nav-menu a { text-decoration: none; color: #333; margin-left: 20px; font-size: 16px; }
.school-info-container { max-width: 1200px; margin: 20px auto; padding: 20px; position: relative; min-height: 600px; }
.school-image { width: 70%; max-height: 300px; object-fit: cover; border-radius: 8px; transition: all 0.5s ease-in-out; opacity: 0; visibility: hidden; }
.school-image.active { transform: scale(1.2); opacity: 1; visibility: visible; }
.school-details { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); margin-top: 320px; transition: all 0.5s ease-in-out; opacity: 0; visibility: hidden; }
.school-details.active { margin-top: 20px; opacity: 1; visibility: visible; }
.info-category { margin-bottom: 20px; }
.info-category h3 { font-size: 18px; font-weight: 600; color: #4F46A5; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
.info-category p { font-size: 14px; color: #444; margin: 5px 0; }
.info-category p strong { color: #333; }
.contact-btn { background: #4F46A5; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: 600; transition: background 0.3s ease; display: block; margin-top: 20px; }
.contact-btn:hover { background: #2E2B5F; }
.login-required { background: #dc3545; cursor: not-allowed; }
.login-required:hover { background: #c82333; }
.no-data { text-align: center; color: #777; margin-top: 50px; }
@media (max-width: 768px) {
    .school-image.active { width: 90%; transform: scale(1.1); }
    .school-details { margin-top: 340px; }
    .school-details.active { margin-top: 20px; }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const image = document.querySelector('.school-image');
    if (image) {
        setTimeout(() => {
            image.classList.add('active');
            document.querySelector('.school-details').classList.add('active');
        }, 100);
    }
});
</script>
</head>
<body>
<header class="header">
    <div class="logo"><i class="fa-solid fa-graduation-cap"></i> SchoolMate</div>
    <nav class="nav-menu">
        <a href="index.php">Home</a>
        <a href="compare.php">Compare</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<div class="school-info-container">
    <?php if ($schoolData): ?>
        <img src="<?php echo htmlspecialchars($schoolData['logo']); ?>" alt="<?php echo htmlspecialchars($schoolData['name']); ?> Image" class="school-image">
        <div class="school-details">
            <div class="info-category">
                <h3>Basic Info</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($schoolData['name']); ?></p>
                <p><strong>Type:</strong> <?php echo htmlspecialchars($schoolData['type']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($schoolData['address']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($schoolData['email']); ?></p>
                <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($schoolData['contact']); ?></p>
                <p><strong>About Us:</strong> <?php echo htmlspecialchars($schoolData['about']); ?></p>
            </div>
            <div class="info-category">
                <h3>Academic Details</h3>
                <p><strong>Programs/Classes:</strong> <?php echo htmlspecialchars($schoolData['academic']['programs']); ?></p>
                <p><strong>Subjects Offered:</strong> <?php echo htmlspecialchars($schoolData['academic']['subjects']); ?></p>
                <p><strong>Admission Requirements:</strong> <?php echo htmlspecialchars($schoolData['academic']['requirements']); ?></p>
            </div>
            <div class="info-category">
                <h3>Facilities</h3>
                <?php foreach ($schoolData['facilities'] as $facility => $value): ?>
                    <p><strong><?php echo ucfirst($facility); ?>:</strong> <?php echo htmlspecialchars($value); ?></p>
                <?php endforeach; ?>
            </div>
            <div class="info-category">
                <h3>Announcements</h3>
                <p><?php echo nl2br(htmlspecialchars($schoolData['announcements'])); ?></p>
            </div>
            <button class="contact-btn <?php echo !isset($_SESSION['user_id']) ? 'login-required' : ''; ?>" 
                    onclick="<?php echo isset($_SESSION['user_id']) ? 'window.location.href=\'contact.php?id=' . $id . '&type=' . $type . '\';' : 'alert(\'Please login to contact this school.\');'; ?>">
                Contact
            </button>
        </div>
    <?php else: ?>
        <p class="no-data">No school details found. Please check the school ID or try again later.</p>
    <?php endif; ?>
</div>

<?php include 'partial/footer.php'; ?>
</body>
</html>
