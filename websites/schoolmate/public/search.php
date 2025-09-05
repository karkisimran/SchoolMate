<?php
// Include your database connection
include 'database.php';

// Initialize variables
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Prepare the query
if (!empty($search)) {
    $query = "SELECT id, school_name, school_type, address, contact_number, email, logo_path 
              FROM registered_schools 
              WHERE school_name LIKE ? 
                 OR address LIKE ? 
                 OR school_type LIKE ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $search_param = "%$search%";
        $stmt->bind_param("sss", $search_param, $search_param, $search_param);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        die("Prepare failed: " . $conn->error);
    }
} else {
    $query = "SELECT id, school_name, school_type, address, contact_number, email, logo_path 
              FROM registered_schools LIMIT 10";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
}

include 'partial/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search For Schools</title>
    <link rel="stylesheet" href="style.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .school-heading {
            text-align: center;
            color: #3B38A0;
        }
        .search-section {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin: 40px 0;
        }
        .search-section h1 {
            text-align: center;
            margin-bottom: 25px;
            color: #2c3e50;
            font-size: 32px;
            position: relative;
            padding-bottom: 15px;
        }
        .search-section h1:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #3B38A0, #2c3e50);
            border-radius: 2px;
        }
        .search-form {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            position: relative;
        }
        .search-input {
            width: 70%;
            padding: 16px 20px;
            border: 2px solid #e0e6ed;
            border-radius: 8px 0 0 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .search-input:focus {
            outline: none;
            border-color: #3498db;
        }
        .search-button {
            padding: 16px 24px;
            background: linear-gradient(135deg, #3B38A0, #2980b9);
            color: white;
            border: none;
            border-radius: 0 8px 8px 0;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .search-button:hover {
            background: linear-gradient(135deg, #3B38A0, #3498db);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }
        .filters {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .filter-btn {
            padding: 10px 20px;
            background-color: #f8f9fa;
            border: 1px solid #e0e6ed;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
        }
        .filter-btn:hover, .filter-btn.active {
            background-color: #3B38A0;
            color: white;
            border-color: #3498db;
        }
        .schools-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 50px;
        }
        .school-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            padding: 25px;
            transition: all 0.3s ease;
            border-left: 4px solid #3B38A0;
            display: flex;
            flex-direction: column;
        }
        .school-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        .school-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .school-logo {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            background: linear-gradient(135deg, #3B38A0, #2c3e50);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-size: 24px;
        }
        .school-name {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }
        .school-type {
            font-size: 14px;
            color: #7f8c8d;
            margin-top: 4px;
        }
        .school-details {
            margin: 15px 0;
            flex-grow: 1;
        }
        .detail-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: #5a6c7d;
        }
        .detail-item i {
            margin-right: 10px;
            color: #3498db;
            width: 20px;
        }
        .school-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        .view-button {
            padding: 10px 20px;
            background: linear-gradient(135deg, #3B38A0, #2980b9);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            text-align: center;
            flex-grow: 1;
            margin-right: 10px;
        }
        .view-button:hover {
            background: linear-gradient(135deg, #3B38A0, #3498db);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }
        .contact-button {
            padding: 10px 15px;
            background-color: #f8f9fa;
            color: #5a6c7d;
            text-decoration: none;
            border: 1px solid #e0e6ed;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            text-align: center;
        }
        .contact-button:hover {
            background-color: #2c3e50;
            color: white;
            border-color: #2c3e50;
        }
        footer {
            background: linear-gradient(135deg, #2c3e50, #1a2530);
            color: #ecf0f1;
            padding: 40px 0 20px;
            margin-top: 60px;
        }
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            padding: 0 20px;
            gap: 30px;
        }
        .footer-section {
            flex: 1;
            min-width: 250px;
        }
        .footer-section h3 {
            margin-bottom: 20px;
            font-size: 18px;
            color: #3498db;
            position: relative;
            padding-bottom: 10px;
        }
        .footer-section h3:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background-color: #3498db;
            border-radius: 2px;
        }
        .footer-section ul {
            list-style: none;
        }
        .footer-section ul li {
            margin-bottom: 12px;
        }
        .footer-section ul li a {
            color: #ecf0f1;
            text-decoration: none;
            transition: color 0.3s;
            display: flex;
            align-items: center;
        }
        .footer-section ul li a i {
            margin-right: 10px;
            font-size: 14px;
        }
        .footer-section ul li a:hover {
            color: #3498db;
        }
        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 30px;
            color: #bdc3c7;
            font-size: 14px;
        }
        .no-results {
            text-align: center;
            padding: 40px;
            grid-column: 1 / -1;
            color: #7f8c8d;
        }
        .no-results i {
            font-size: 60px;
            margin-bottom: 20px;
            color: #e0e6ed;
        }
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            nav ul {
                margin-top: 15px;
                flex-wrap: wrap;
                justify-content: center;
            }
            nav ul li {
                margin: 5px;
            }
            .search-input {
                width: 65%;
            }
            .schools-list {
                grid-template-columns: 1fr;
            }
            .footer-content {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="container">
        <h1 class="school-heading">Search For Schools</h1>
        
        <div class="search-section">
            <form class="search-form" method="GET" action="">
                <input type="text" class="search-input" name="search" placeholder="Enter school name, location, or type" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search'] ?? '') : ''; ?>">
                <button type="submit" class="search-button">Search</button>
            </form>
            
            <div class="filters">
                <button class="filter-btn active">All</button>
                <button class="filter-btn">Primary</button>
                <button class="filter-btn">Secondary</button>
                <button class="filter-btn">Higher Secondary</button>
                <button class="filter-btn">International</button>
            </div>
            
            <!-- School List Section -->
            <h2 class="school-heading">School List</h2>
            <div class="schools-list">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($school = mysqli_fetch_assoc($result)): ?>
                    <div class="school-card">
                        <div class="school-header">
                            <div class="school-logo">
                                <?php if (!empty($school['logo_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($school['logo_path'] ?? ''); ?>" alt="School Logo">
                                <?php else: ?>
                                    <i class="fas fa-school"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="school-name"><?php echo htmlspecialchars($school['school_name'] ?? ''); ?></div>
                                <div class="school-type"><?php echo htmlspecialchars($school['school_type'] ?? ''); ?></div>
                            </div>
                        </div>
                        <div class="school-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($school['address'] ?? ''); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <span><?php echo htmlspecialchars($school['contact_number'] ?? ''); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-envelope"></i>
                                <span><?php echo htmlspecialchars($school['email'] ?? ''); ?></span>
                            </div>
                        </div>
                        <div class="school-actions">
                            <a href="school_info.php?id=<?php echo $school['id']; ?>" class="view-button">View Details</a>
                            <a href="contact.php?id=<?php echo $school['id']; ?>" class="contact-button">Contact</a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-results">
                        <i class="fas fa-search"></i>
                        <h3>No schools found</h3>
                        <p>Try different keywords or check back later for new registrations</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'partial/footer.php'; ?>

    <script>
        // Filter buttons functionality
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Get the filter text
                const filter = this.textContent;
                
                // Show all cards if "All" is selected
                if (filter === 'All') {
                    document.querySelectorAll('.school-card').forEach(card => {
                        card.style.display = 'flex';
                    });
                } else {
                    // Filter by school type
                    document.querySelectorAll('.school-card').forEach(card => {
                        const schoolType = card.querySelector('.school-type').textContent.trim();
                        if (schoolType.includes(filter)) {
                            card.style.display = 'flex';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }
            });
        });

        // Highlight search term in results
        const urlParams = new URLSearchParams(window.location.search);
        const searchTerm = urlParams.get('search');
        if (searchTerm) {
            const schoolNames = document.querySelectorAll('.school-name');
            schoolNames.forEach(el => {
                const text = el.textContent.trim();
                const regex = new RegExp(searchTerm, 'gi');
                el.innerHTML = text.replace(regex, match => `<span style="background-color: #ffff76">${match}</span>`);
            });
        }
    </script>
</body>
</html>