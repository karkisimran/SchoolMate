<?php
require 'database.php';

// Get school ID and type (default to 'school')
$id   = isset($_GET['id']) ? intval($_GET['id']) : 0;
$type = isset($_GET['type']) ? $_GET['type'] : 'school';

if ($id <= 0) {
    echo "<p class='no-data'>Invalid school ID</p>";
    exit;
}

if ($type === 'montessori') {
    // Montessori data: only minimal fields exist
    $stmt = $conn->prepare("
        SELECT 
            id AS school_id,
            name AS school_name,
            'Montessori' AS school_type,
            '' AS email,
            location AS address,
            '' AS contact_number,
            '' AS about_us,
            '{}' AS academic_details,
            '{}' AS facilities,
            '' AS announcements,
            '' AS logo_path
        FROM montessories
        WHERE id = ?
    ");
} else {
    // Featured schools (full details)
    $stmt = $conn->prepare("
        SELECT 
            school_id,
            school_name,
            school_type,
            email,
            address,
            contact_number,
            about_us,
            academic_details,
            facilities,
            announcements,
            logo_path
        FROM school_details
        WHERE school_id = ?
    ");
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Decode JSON fields safely
    $academic = json_decode($row['academic_details'] ?? '{}', true) ?: [
        'programs'    => 'Not available',
        'subjects'    => 'Not available',
        'requirements'=> 'Not available'
    ];

    $facilities = json_decode($row['facilities'] ?? '{}', true) ?: [
        'library'     => 'Not available',
        'sports'      => 'Not available',
        'labs'        => 'Not available',
        'transport'   => 'Not available',
        'eca'         => 'Not available',
        'health'      => 'Not available',
        'scholarships'=> 'Not available',
        'cafeteria'   => 'Not available'
    ];

    // Display
    echo "<h3>" . htmlspecialchars($row['school_name']) . "</h3>";

    echo "<div class='info-category'>
            <h3>Basic Info</h3>
            <p><strong>Type:</strong> " . htmlspecialchars($row['school_type']) . "</p>
            <p><strong>Address:</strong> " . htmlspecialchars($row['address']) . "</p>
            <p><strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>
            <p><strong>Contact:</strong> " . htmlspecialchars($row['contact_number']) . "</p>
            <p><strong>About Us:</strong> " . htmlspecialchars($row['about_us']) . "</p>
          </div>";

    echo "<div class='info-category'>
            <h3>Academic Details</h3>
            <p><strong>Programs:</strong> " . htmlspecialchars($academic['programs']) . "</p>
            <p><strong>Subjects:</strong> " . htmlspecialchars($academic['subjects']) . "</p>
            <p><strong>Requirements:</strong> " . htmlspecialchars($academic['requirements']) . "</p>
          </div>";

    echo "<div class='info-category'>
            <h3>Facilities</h3>";
    foreach ($facilities as $key => $val) {
        echo "<p><strong>" . ucfirst($key) . ":</strong> " . htmlspecialchars($val) . "</p>";
    }
    echo "</div>";

    echo "<div class='info-category'>
            <h3>Announcements</h3>
            <p>" . htmlspecialchars($row['announcements'] ?: 'No announcements available') . "</p>
          </div>";

} else {
    echo "<p class='no-data'>No details found for this school.</p>";
}
