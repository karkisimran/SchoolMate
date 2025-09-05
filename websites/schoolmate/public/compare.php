<?php
require 'database.php';

$sql = "
    SELECT school_id AS id, school_name, 'featured' AS source
    FROM school_details
    UNION
    SELECT id AS id, school_name, 'registered' AS source
    FROM registered_schools
    WHERE school_name NOT IN (SELECT school_name FROM school_details)
    ORDER BY school_name ASC
";


$result = $conn->query($sql);

$schools = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $schools[] = $row;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compare Schools - SchoolMate</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css"/>
    <style>
        .compare-section {
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .compare-section h2 {
            color: #4F46A5;
            font-size: 26px;
            margin-bottom: 25px;
        }

        .compare-selectors {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
        }

        .compare-selectors select {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .compare-boxes {
            display: flex;
            justify-content: space-between;
            gap: 30px;
        }

        .school-box {
            flex: 1;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }

        .school-box:hover {
            transform: translateY(-5px);
        }

        .school-box h3 {
            font-size: 18px;
            font-weight: 600;
            color: #4F46A5;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .school-box p {
            font-size: 14px;
            color: #444;
            margin: 5px 0;
        }

        .school-box p strong {
            color: #333;
        }

        .no-data {
            color: #777;
            text-align: center;
            margin-top: 50px;
        }

        @media (max-width: 768px) {
            .compare-selectors {
                flex-direction: column;
            }
            .compare-boxes {
                flex-direction: column;
            }
            .school-box {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <?php include 'partial/header.php'; ?>

    <div class="compare-section">
        <h2>Compare Between Schools</h2>

        <div class="compare-selectors">
            <select id="school1" onchange="loadSchoolDetails(this.value, 'left')">
                <option value="">Choose School 1</option>
                <?php foreach ($schools as $school): ?>
                    <option value="<?php echo $school['id']; ?>"><?php echo htmlspecialchars($school['school_name']); ?></option>
                <?php endforeach; ?>
            </select>
            <select id="school2" onchange="loadSchoolDetails(this.value, 'right')">
                <option value="">Choose School 2</option>
                <?php foreach ($schools as $school): ?>
                    <option value="<?php echo $school['id']; ?>"><?php echo htmlspecialchars($school['school_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="compare-boxes">
            <div class="school-box" id="left-box">
                <p class="no-data">Select a school to view details</p>
            </div>
            <div class="school-box" id="right-box">
                <p class="no-data">Select a school to view details</p>
            </div>
        </div>
    </div>

    <?php include 'partial/footer.php'; ?>
</body>
</html>

<script>
function loadSchoolDetails(schoolId, side) {
    if (schoolId === "") {
        document.getElementById(side + "-box").innerHTML = "<p class='no-data'>Select a school to view details</p>";
        return;
    }

    fetch("get_school_details.php?id=" + schoolId)
        .then(res => res.text())
        .then(data => {
            document.getElementById(side + "-box").innerHTML = data;
        })
        .catch(error => {
            console.error('Error fetching school details:', error);
            document.getElementById(side + "-box").innerHTML = "<p class='no-data'>Error loading details</p>";
        });
}
</script>