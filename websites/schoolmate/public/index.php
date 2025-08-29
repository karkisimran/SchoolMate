<?php
// include database connection
require 'database.php';

// $schools = [];
// $result = $conn->query("SELECT * FROM schools LIMIT 4");

// Fetch Montessori schools
$sqlMont = "SELECT * FROM montessories LIMIT 4";
$resultMont = $conn->query($sqlMont);

$montessories = [];
if ($resultMont->num_rows > 0) {
    while($row = $resultMont->fetch_assoc()) {
        $montessories[] = $row;
    }
}

// Fetch other schools
$sqlSchools = "SELECT * FROM schools LIMIT 4";
$resultSchools = $conn->query($sqlSchools);

$schools = [];
if ($resultSchools->num_rows > 0) {
    while($row = $resultSchools->fetch_assoc()) {
        $schools[] = $row;
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SchoolMate</title>
  <link rel="stylesheet" href="style.css"/>
</head>
<body>

 <?php include('partial/header.php');  ?> 

  <!-- Hero Section -->
  <section class="hero">
  <img src="images/kid.jpg" alt="Child studying" class="hero-bg">
  <div class="hero-overlay"></div>
  <div class="hero-text">
    <h1>Discover the Right School for Your Child</h1>
    <p>Explore verified schools, compare them, and enroll easily with <strong>SchoolMate</strong>.</p>
    <a href="#" class="cta-btn">Start Exploring</a>
  </div>
</section>


<!-- Montessori Section -->
<section class="featured">
  <h2>Featured Montessori</h2>
  <div class="school-list">
    <?php if (!empty($montessories)): ?>
      <?php foreach ($montessories as $montessori): ?>
        <a href="school_info.php?id=<?= $montessori['id'] ?>" class="school-card">
          <img src="<?= $montessori['image'] ?>" alt="<?= $montessori['name'] ?>">
          <h3><?= $montessori['name'] ?></h3>
          <p><?= $montessori['location'] ?></p>
        </a>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No Montessori schools have been added yet. Stay tuned!</p>
    <?php endif; ?>
  </div>
</section>

<!-- Other Schools Section -->
<section class="featured">
  <h2>Featured School</h2>
  <div class="school-list">
    <?php if (!empty($schools)): ?>
      <?php foreach ($schools as $school): ?>
        <a href="school_info.php?id=<?= $school['id'] ?>" class="school-card">
          <img src="<?= $school['image'] ?>" alt="<?= $school['name'] ?>">
          <h3><?= $school['name'] ?></h3>
          <p><?= $school['location'] ?></p>
        </a>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No other schools have been added yet. Stay tuned!</p>
    <?php endif; ?>
  </div>
</section>



  <!-- Call to Action: Register School -->
  <section class="register-school">
    <div class="register-text">
      <h2>Are You a School Administrator?</h2>
      <p>Register your school and reach thousands of parents seeking the best education options.</p>
    </div>
    <div class="register-btn-container">
      <a href="school_register.php" class="big-register-btn">Register Your School</a>
    </div>
  </section>

   <?php include('partial/footer.php');  ?>

 
</body>
</html>